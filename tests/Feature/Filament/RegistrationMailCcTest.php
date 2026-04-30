<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Registrations\Pages\ListRegistrations;
use App\Mail\RegistrationConfirmedMail;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationMailCcTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    public function test_it_sends_registration_confirmation_mail_with_cc()
    {
        Mail::fake();

        $admin = User::factory()->create();
        $registration = Registration::factory()->create([
            'validated_at' => null,
            'email' => 'recipient@example.com',
        ]);

        Livewire::actingAs($admin)
            ->test(ListRegistrations::class)
            ->callTableAction('validate', $registration)
            ->assertHasNoTableActionErrors();

        Mail::assertQueued(RegistrationConfirmedMail::class, function ($mail) use ($registration) {
            return $mail->hasTo('recipient@example.com') &&
                   $mail->hasCc('adminlpks@ukwms.ac.id') &&
                   $mail->registration->id === $registration->id;
        });

        $this->assertNotNull($registration->fresh()->validated_at);
    }
}
