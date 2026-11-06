<?php

namespace Tests\Feature;

use App\Models\Article;
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
}
