<?php

namespace Tests\Feature;

use App\Livewire\RegistrationForm;
use App\Models\Invitation;
use App\Models\Registration;
use App\Models\RegistrationAttendee;
use App\Models\Setting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::updateOrCreate(
            ['key' => 'registration_closing_at'],
            ['value' => now()->addDay()->format('Y-m-d H:i:s')],
        );
    }

    /** @test */
    public function test_can_access_registration_page_with_valid_code()
    {
        $invitation = Invitation::factory()->create([
            'is_used' => false,
        ]);

        $response = $this->get("/join/{$invitation->unique_code}");

        $response->assertStatus(200);
        $response->assertSeeLivewire(RegistrationForm::class);
    }

    /** @test */
    public function test_returns_404_for_invalid_code()
    {
        $response = $this->get('/join/INVALID-CODE');

        $response->assertStatus(404);
    }

    /** @test */
    public function test_shows_already_registered_if_invitation_is_used()
    {
        $invitation = Invitation::factory()->create([
            'is_used' => true,
        ]);
        Registration::factory()->create([
            'invitation_id' => $invitation->id,
        ]);

        $response = $this->get("/join/{$invitation->unique_code}");

        $response->assertStatus(200);
        $response->assertSee('Registrasi Berhasil!');
    }

    /** @test */
    public function test_can_submit_registration_form()
    {
        $invitation = Invitation::factory()->create(['is_used' => false]);

        Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code])
            ->set('attendance_count', 1)
            ->set('attendees.0.name', 'John Doe')
            ->set('attendees.0.email', 'john@example.com')
            ->set('attendees.0.phone', '08123456789')
            ->set('attendees.0.position', 'Manager')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('registrations', [
            'invitation_id' => $invitation->id,
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('registration_attendees', [
            'name' => 'John Doe',
            'position' => 'Manager',
        ]);

        $this->assertTrue($invitation->fresh()->is_used);
    }

    /** @test */
    public function test_validates_required_fields()
    {
        $invitation = Invitation::factory()->create(['is_used' => false]);

        Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code])
            ->set('attendance_count', 1)
            ->set('attendees.0.name', '')
            ->call('submit')
            ->assertHasErrors(['attendees.0.name' => 'required']);
    }

    /** @test */
    public function test_can_submit_registration_with_multiple_attendees()
    {
        $invitation = Invitation::factory()->create(['is_used' => false]);

        Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code])
            ->set('attendance_count', 2)
            ->set('attendees.0.name', 'Main Guest')
            ->set('attendees.0.email', 'main@example.com')
            ->set('attendees.0.phone', '08123456789')
            ->set('attendees.0.position', 'CEO')
            ->set('attendees.1.name', 'Plus One')
            ->set('attendees.1.email', 'plus@example.com')
            ->set('attendees.1.phone', '08123456780')
            ->set('attendees.1.position', 'Assistant')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertCount(2, RegistrationAttendee::all());

        $this->assertDatabaseHas('registration_attendees', [
            'name' => 'Plus One',
            'position' => 'Assistant',
        ]);
    }
}
