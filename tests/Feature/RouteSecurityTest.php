<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RouteSecurityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_preview_email_is_not_available_outside_local_environment(): void
    {
        $this->get('/preview-email')->assertNotFound();

        $this->assertDatabaseCount('invitations', 0);
        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_preview_email_is_not_available_to_authenticated_users_outside_local_environment(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/preview-email')
            ->assertNotFound();
    }

    public function test_user_manual_requires_authentication(): void
    {
        $this->get('/user-manual')->assertRedirect(route('login'));
    }

    public function test_user_manual_is_available_to_authenticated_users(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/user-manual')
            ->assertOk();
    }

    public function test_invitation_template_requires_authentication(): void
    {
        $this->get('/admin/invitations/template')->assertRedirect(route('login'));
    }

    public function test_legacy_check_in_url_no_longer_changes_registration_state(): void
    {
        $registration = Registration::factory()->create(['checked_in_at' => null]);

        $this->actingAs(User::factory()->create())
            ->get("/admin/check-in/{$registration->uuid}")
            ->assertNotFound();

        $this->assertNull($registration->fresh()->checked_in_at);
    }
}
