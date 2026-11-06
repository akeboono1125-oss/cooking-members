<?php

namespace Tests\Feature;

use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_announcement_list(): void
    {
        $announcement = Announcement::factory()->create();

        $response = $this->get('/announcements');

        $response->assertOk();
        $response->assertSee($announcement->title);
    }

    public function test_unpublished_announcements_are_not_listed(): void
    {
        Announcement::factory()->create(['title' => '公開済みのお知らせ']);
        Announcement::factory()->create(['title' => '予約中のお知らせ', 'published_at' => now()->addDay()]);
        Announcement::factory()->create(['title' => '下書きのお知らせ', 'published_at' => null]);

        $response = $this->get('/announcements');

        $response->assertSee('公開済みのお知らせ');
        $response->assertDontSee('予約中のお知らせ');
        $response->assertDontSee('下書きのお知らせ');
    }

    public function test_guest_can_view_published_announcement(): void
    {
        $announcement = Announcement::factory()->create();

        $response = $this->get(route('announcements.show', $announcement));

        $response->assertOk();
        $response->assertSee($announcement->title);
    }

    public function test_scheduled_announcement_returns_404(): void
    {
        $announcement = Announcement::factory()->create(['published_at' => now()->addDay()]);

        $response = $this->get(route('announcements.show', $announcement));

        $response->assertNotFound();
    }
}
