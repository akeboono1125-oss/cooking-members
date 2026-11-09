<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_articles_including_unpublished(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Article::factory()->create(['title' => '公開中の記事']);
        Article::factory()->draft()->create(['title' => '下書きの記事']);
        Article::factory()->scheduled()->create(['title' => '予約中の記事']);

        $response = $this->actingAs($admin)->get(route('admin.articles.index'));

        $response->assertOk();
        $response->assertSee('公開中の記事');
        $response->assertSee('下書きの記事');
        $response->assertSee('予約中の記事');
    }

    public function test_member_cannot_view_admin_article_list(): void
    {
        $member = User::factory()->create();

        $response = $this->actingAs($member)->get(route('admin.articles.index'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.articles.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_status_label_is_shown_for_each_article(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Article::factory()->create(['title' => '公開中の記事']);
        Article::factory()->draft()->create(['title' => '下書きの記事']);
        Article::factory()->scheduled()->create(['title' => '予約中の記事']);

        $response = $this->actingAs($admin)->get(route('admin.articles.index'));

        // 作成日時が同じため並び順は決まらないので、同じ行（</tr> の手前まで）に正しい状態が出ているかを確認する
        $html = $response->getContent();
        $this->assertMatchesRegularExpression('#公開中の記事(?:(?!</tr>).)*?<td[^>]*>\s*公開中\s*<#su', $html);
        $this->assertMatchesRegularExpression('#下書きの記事(?:(?!</tr>).)*?<td[^>]*>\s*下書き\s*<#su', $html);
        $this->assertMatchesRegularExpression('#予約中の記事(?:(?!</tr>).)*?<td[^>]*>\s*公開予約\s*<#su', $html);
    }

    public function test_draft_article_shows_dash_for_published_at(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Article::factory()->draft()->create();

        $response = $this->actingAs($admin)->get(route('admin.articles.index'));

        $response->assertSee('—');
    }

    public function test_articles_are_ordered_by_newest_created(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Article::factory()->create(['title' => '先に作った記事', 'created_at' => now()->subDay()]);
        Article::factory()->draft()->create(['title' => '後で作った下書き', 'created_at' => now()]);

        $response = $this->actingAs($admin)->get(route('admin.articles.index'));

        $response->assertSeeInOrder(['後で作った下書き', '先に作った記事']);
    }

    public function test_article_list_is_paginated_by_20(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        // カテゴリー名は5種類しかないので、21件とも同じカテゴリーにする
        $category = Category::factory()->create();
        Article::factory()->count(21)->for($category)->create();

        $response = $this->actingAs($admin)->get(route('admin.articles.index'));

        $response->assertViewHas('articles', fn ($articles) => $articles->count() === 20 && $articles->total() === 21);
    }
}
