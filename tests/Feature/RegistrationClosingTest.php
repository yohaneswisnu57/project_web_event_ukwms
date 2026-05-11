<?php

namespace Tests\Feature;

use App\Livewire\RegistrationForm;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationClosingTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_is_open_before_closing_date()
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 13, 11, 59, 59, 'Asia/Jakarta'));
        $invitation = Invitation::factory()->create();

        Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code])
            ->assertSet('isClosed', false)
            ->assertDontSee('Registrasi Ditutup');
    }

    public function test_registration_is_closed_at_closing_date()
    {
        // Precisely at 12:00:01 (greater than 12:00:00)
        Carbon::setTestNow(Carbon::create(2026, 5, 13, 12, 0, 1, 'Asia/Jakarta'));
        $invitation = Invitation::factory()->create();

        Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code])
            ->assertSet('isClosed', true)
            ->assertSee('Registrasi Ditutup');
    }

    public function test_cannot_submit_after_closing_date()
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 13, 11, 59, 0, 'Asia/Jakarta'));
        $invitation = Invitation::factory()->create();

        $component = Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code]);

        // Move time forward
        Carbon::setTestNow(Carbon::create(2026, 5, 13, 12, 0, 1, 'Asia/Jakarta'));

        $component->call('submit')
            ->assertSet('isClosed', true);

        // Assert that the registration was NOT created
        $this->assertDatabaseCount('registrations', 0);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset time
        parent::tearDown();
    }
}
