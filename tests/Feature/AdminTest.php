<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_admin_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('管理画面です。');
    }

    public function test_member_cannot_view_admin_dashboard(): void
    {
        $member = User::factory()->create();

        $response = $this->actingAs($member)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_unverified_admin_is_redirected_to_verification_notice(): void
    {
        $admin = User::factory()->unverified()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_admin_link_is_shown_only_to_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $member = User::factory()->create();

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertSee(route('admin.dashboard'));

        $this->actingAs($member)->get(route('dashboard'))
            ->assertDontSee(route('admin.dashboard'));
    }

    public function test_is_admin_cannot_be_changed_from_profile_form(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->patch(route('profile.update'), [
            'name' => $member->name,
            'email' => $member->email,
            'is_admin' => true,
        ]);

        $this->assertFalse($member->fresh()->is_admin);
    }

    public function test_is_admin_cannot_be_set_on_registration(): void
    {
        $this->post(route('register'), [
            'name' => '新規会員',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_admin' => true,
        ]);

        $user = User::where('email', 'new@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->is_admin);
    }
}
