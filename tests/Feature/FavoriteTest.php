<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_published_article_to_favorites(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('articles.show', $article))
            ->post(route('favorites.store', $article));

        $response->assertRedirect(route('articles.show', $article));
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_adding_same_article_twice_does_not_duplicate(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $this->actingAs($user)->post(route('favorites.store', $article));
        $this->actingAs($user)->post(route('favorites.store', $article));

        $this->assertDatabaseCount('favorites', 1);
    }

    public function test_unpublished_article_cannot_be_added_to_favorites(): void
    {
        $user = User::factory()->create();
        $draft = Article::factory()->draft()->create();
        $scheduled = Article::factory()->scheduled()->create();

        $this->actingAs($user)->post(route('favorites.store', $draft))->assertNotFound();
        $this->actingAs($user)->post(route('favorites.store', $scheduled))->assertNotFound();

        $this->assertDatabaseCount('favorites', 0);
    }

    public function test_guest_cannot_add_to_favorites(): void
    {
        $article = Article::factory()->create();

        $response = $this->post(route('favorites.store', $article));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('favorites', 0);
    }

    public function test_user_can_remove_article_from_favorites(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();
        $user->favoriteArticles()->attach($article);

        $response = $this->actingAs($user)
            ->from(route('articles.show', $article))
            ->delete(route('favorites.destroy', $article));

        $response->assertRedirect(route('articles.show', $article));
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_user_can_remove_article_that_became_unpublished(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();
        $user->favoriteArticles()->attach($article);
        $article->update(['status' => 'draft']);

        $this->actingAs($user)->delete(route('favorites.destroy', $article));

        $this->assertDatabaseCount('favorites', 0);
    }

    public function test_removing_does_not_affect_other_users_favorites(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $article = Article::factory()->create();
        $user->favoriteArticles()->attach($article);
        $otherUser->favoriteArticles()->attach($article);

        $this->actingAs($user)->delete(route('favorites.destroy', $article));

        $this->assertDatabaseHas('favorites', [
            'user_id' => $otherUser->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_article_page_shows_button_matching_favorite_state(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $this->actingAs($user)->get(route('articles.show', $article))
            ->assertSee('お気に入りに追加')
            ->assertDontSee('お気に入りから外す');

        $user->favoriteArticles()->attach($article);

        $this->actingAs($user)->get(route('articles.show', $article))
            ->assertSee('お気に入りから外す')
            ->assertDontSee('お気に入りに追加');
    }

    public function test_favorites_list_shows_only_own_favorites(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $mine = Article::factory()->create(['title' => '自分のお気に入り記事']);
        $others = Article::factory()->create(['title' => '他の人のお気に入り記事']);
        $user->favoriteArticles()->attach($mine);
        $otherUser->favoriteArticles()->attach($others);

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertOk();
        $response->assertSee('自分のお気に入り記事');
        $response->assertDontSee('他の人のお気に入り記事');
    }

    public function test_favorites_list_does_not_show_unpublished_articles(): void
    {
        $user = User::factory()->create();
        $published = Article::factory()->create(['title' => '公開中の記事']);
        $unpublished = Article::factory()->create(['title' => '非公開になった記事']);
        $user->favoriteArticles()->attach([$published->id, $unpublished->id]);
        $unpublished->update(['status' => 'draft']);

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertSee('公開中の記事');
        $response->assertDontSee('非公開になった記事');
    }

    public function test_favorites_list_is_ordered_by_newest_added(): void
    {
        $user = User::factory()->create();
        $older = Article::factory()->create(['title' => '先に追加した記事']);
        $newer = Article::factory()->create(['title' => '後で追加した記事']);

        $this->travelTo(now()->subDay());
        $user->favoriteArticles()->attach($older);
        $this->travelBack();
        $user->favoriteArticles()->attach($newer);

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertSeeInOrder(['後で追加した記事', '先に追加した記事']);
    }

    public function test_empty_favorites_list_shows_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertSee('お気に入りはまだありません。');
    }
}
