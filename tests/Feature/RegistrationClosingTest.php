<?php

namespace Tests\Feature;

use App\Livewire\RegistrationForm;
use App\Models\Invitation;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationClosingTest extends TestCase
{
    use RefreshDatabase;

    private function setClosingAt(string $value): void
    {
        Setting::updateOrCreate(
            ['key' => 'registration_closing_at'],
            ['value' => $value],
        );
    }

    public function test_registration_is_open_before_closing_date()
    {
        $this->setClosingAt('2026-05-13 16:00:00');
        Carbon::setTestNow(Carbon::create(2026, 5, 13, 15, 59, 59, 'Asia/Jakarta'));
        $invitation = Invitation::factory()->create();

        Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code])
            ->assertSet('isClosed', false)
            ->assertDontSee('Registrasi Ditutup');
    }

    public function test_registration_is_closed_at_closing_date()
    {
        // Precisely at 16:00:01 (greater than 16:00:00)
        $this->setClosingAt('2026-05-13 16:00:00');
        Carbon::setTestNow(Carbon::create(2026, 5, 13, 16, 0, 1, 'Asia/Jakarta'));
        $invitation = Invitation::factory()->create();

        Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code])
            ->assertSet('isClosed', true)
            ->assertSee('Registrasi Ditutup');
    }

    public function test_cannot_submit_after_closing_date()
    {
        $this->setClosingAt('2026-05-13 16:00:00');
        Carbon::setTestNow(Carbon::create(2026, 5, 13, 15, 59, 0, 'Asia/Jakarta'));
        $invitation = Invitation::factory()->create();

        $component = Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code]);

        // Move time forward
        Carbon::setTestNow(Carbon::create(2026, 5, 13, 16, 0, 1, 'Asia/Jakarta'));

        $component->call('submit')
            ->assertSet('isClosed', true);

        // Assert that the registration was NOT created
        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_default_closing_time_is_used_when_no_setting_exists()
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 13, 15, 59, 59, 'Asia/Jakarta'));
        $openInvitation = Invitation::factory()->create();

        Livewire::test(RegistrationForm::class, ['code' => $openInvitation->unique_code])
            ->assertSet('isClosed', false);

        Carbon::setTestNow(Carbon::create(2026, 5, 13, 16, 0, 1, 'Asia/Jakarta'));

        Livewire::test(RegistrationForm::class, ['code' => $openInvitation->unique_code])
            ->assertSet('isClosed', true);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset time
        parent::tearDown();
    }
}
