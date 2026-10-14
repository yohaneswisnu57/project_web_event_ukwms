<?php

namespace Tests\Feature;

use App\Livewire\RegistrationForm;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_keeps_banner_and_speakers_and_shows_event_details(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('/images/hero-banner.webp', false)
            ->assertSee('Speakers')
            ->assertSee('Ignasius Jonan')
            ->assertSee('Mgr. Agustinus Tri Budi Utomo')
            ->assertSee('Auditorium B, Lantai 4')
            ->assertSee('14.00 – 17.00 WIB')
            ->assertSee('Sabtu')
            ->assertSee('Rundown');
    }

    public function test_placeholder_blocks_are_labelled(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Contoh data')
            ->assertSee('Contoh foto');
    }

    public function test_placeholder_labels_disappear_when_flags_are_removed(): void
    {
        config([
            'event.agenda.is_placeholder' => false,
            'event.venue.photo_is_placeholder' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Contoh data')
            ->assertDontSee('Contoh foto');
    }

    public function test_registration_page_uses_shared_banner_and_event_details(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 10, 9, 0, 0, 'Asia/Jakarta'));
        $invitation = Invitation::factory()->create(['name' => 'PT Maju Mundur']);

        Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code])
            ->assertSee('/images/hero-banner.webp', false)
            ->assertSee('PT Maju Mundur')
            ->assertSee('Auditorium B, Lantai 4')
            ->assertSee('Formulir Kehadiran');
    }

    public function test_landing_page_ships_scroll_reveal_motion_with_reduced_motion_fallback(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('data-reveal', false)
            ->assertSee('data-reveal="wipe"', false)
            ->assertSee('prefers-reduced-motion: reduce', false)
            ->assertSee('IntersectionObserver', false);
    }

    public function test_reveal_elements_on_registration_page_are_protected_from_livewire_morphing(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 10, 9, 0, 0, 'Asia/Jakarta'));
        $invitation = Invitation::factory()->create();

        Livewire::test(RegistrationForm::class, ['code' => $invitation->unique_code])
            ->assertSeeHtml('data-reveal="slow" wire:ignore.self');
    }
}
