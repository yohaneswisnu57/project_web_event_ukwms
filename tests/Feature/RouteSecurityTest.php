<?php

namespace Tests\Feature;

use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RouteSecurityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_retired_wimates_routes_are_no_longer_available(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/preview-email')->assertNotFound();
        $this->get('/user-manual')->assertNotFound();
        $this->get('/join/ANYCODE')->assertNotFound();
        $this->get('/admin/invitations/template')->assertNotFound();
    }

    public function test_admin_panel_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_legacy_check_in_url_does_not_change_participant_state(): void
    {
        $participant = Participant::factory()->verified()->create();

        $this->actingAs(User::factory()->create())
            ->get("/admin/check-in/{$participant->uuid}")
            ->assertNotFound();

        $this->assertNull($participant->fresh()->checked_in_at);
    }
}
