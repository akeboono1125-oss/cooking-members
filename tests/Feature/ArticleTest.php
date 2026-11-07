<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_can_view_article_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/articles');
        $response->assertOk();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/articles');
        $response->assertRedirect(route('login'));
    }

    public function test_unverified_user_is_redirected_to_verification_notice(): void
    {
        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->get('/articles');
        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unpublished_articles_are_not_listed(): void
    {
        Article::factory()->create(['title' => '公開済みの記事']);
        Article::factory()->scheduled()->create(['title' => '予約中の記事']);
        Article::factory()->draft()->create(['title' => '下書きの記事']);

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/articles');

        $response->assertSee('公開済みの記事');
        $response->assertDontSee('予約中の記事');
        $response->assertDontSee('下書きの記事');
    }

    public function test_unpublished_article_returns_404(): void
    {
        $article = Article::factory()->draft()->create();

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('articles.show', $article));

        $response->assertNotFound();
    }

    public function test_articles_can_be_filtered_by_category(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        $articleA = Article::factory()->create(['category_id' => $categoryA->id, 'title' => 'カテゴリーAのタイトル']);
        $articleB = Article::factory()->create(['category_id' => $categoryB->id, 'title' => 'カテゴリーBのタイトル']);

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('articles.index', ['category' => $categoryA->id]));

        $response->assertSee($articleA->title);
        $response->assertDontSee($articleB->title);
    }

    public function test_articles_can_be_searched_by_title(): void
    {
        $articleA = Article::factory()->create(['title' => 'カレーの作り方', 'body' => '肉を切り、炒める']);
        $articleB = Article::factory()->create(['title' => 'ハンバーグの作り方', 'body' => '魂がこもっていれば完成']);

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('articles.index', ['keyword' => 'ハンバーグ']));

        $response->assertSee($articleB->title);
        $response->assertDontSee($articleA->title);
    }

    public function test_articles_can_be_searched_by_body(): void
    {
        $articleA = Article::factory()->create(['title' => 'カレーの作り方', 'body' => '肉を切り、炒める']);
        $articleB = Article::factory()->create(['title' => 'ハンバーグの作り方', 'body' => '魂がこもっていれば完成']);

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('articles.index', ['keyword' => '魂']));

        $response->assertSee($articleB->title);
        $response->assertDontSee($articleA->title);
    }

    public function test_search_does_not_show_unpublished_articles(): void
    {

        $articleA = Article::factory()->create(['title' => 'カレーの作り方', 'body' => '肉を切る']);
        $articleB = Article::factory()->scheduled()->create(['title' => 'ハンバーグの作り方', 'body' => 'カレーを叩く']);
        $articleC = Article::factory()->draft()->create(['title' => 'うどんの作り方', 'body' => 'カレーにしてすする']);

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('articles.index', ['keyword' => 'カレー']));

        $response->assertSee($articleA->title);
        $response->assertDontSee($articleB->title);
        $response->assertDontSee($articleC->title);

    }
}
