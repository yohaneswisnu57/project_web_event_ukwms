<?php

namespace Tests\Feature;

use App\Models\Participant;
use App\Models\Setting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 10, 14, 10, 0, 0, 'Asia/Jakarta'));
    }

    public function test_landing_page_shows_event_details_and_registration_link(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>RETINA 2026 — Redaction Training in Art</title>', false)
            ->assertSee('Healthcare Public Speaking &amp; Communication Training', false)
            ->assertSee('Sabtu, 31 Oktober 2026')
            ->assertSee('09.00 WIB')
            ->assertSee('Ruang Teater Barat')
            ->assertSee('Henry Christian Tjandra')
            ->assertSee('30.000')
            ->assertSee('141 002 178 3519')
            ->assertSee('Yayasan Widya Mandala Surabaya')
            ->assertSee('Pendaftaran dibuka')
            ->assertSee(route('registration'))
            ->assertSee('calendar.google.com', false);
    }

    public function test_countdown_is_rendered_from_the_server(): void
    {
        // 31 Okt 09.00 - 14 Okt 10.00 = 16 hari 23 jam 0 menit.
        $this->get(route('home'))
            ->assertSeeInOrder(['data-unit="days">16<', 'data-unit="hours">23<', 'data-unit="minutes">00<'], false);
    }

    public function test_remaining_quota_is_shown_when_a_quota_is_set(): void
    {
        Setting::putValue(Participant::SETTING_QUOTA, '50');
        Participant::factory()->count(3)->create();
        Participant::factory()->rejected()->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Sisa 47 kursi');
    }

    public function test_closing_date_is_mentioned_in_the_faq(): void
    {
        Setting::putValue(Participant::SETTING_CLOSING_AT, '2026-10-28 23:59:00');

        $this->get(route('home'))
            ->assertSee('Pendaftaran dibuka hingga 28 Oktober')
            ->assertSee('Pada Rabu, 28 Oktober 2026 pukul 23.59 WIB');
    }

    public function test_registration_links_are_hidden_once_registration_closes(): void
    {
        Setting::putValue(Participant::SETTING_CLOSING_AT, '2026-10-13 23:59:00');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Pendaftaran ditutup')
            ->assertDontSee('Pendaftaran dibuka')
            ->assertDontSee(route('registration'));
    }

    public function test_optional_speaker_details_render_only_when_configured(): void
    {
        $this->get(route('home'))->assertDontSee('r-speaker__role', false);

        config([
            'retina.speaker.role' => 'Pelatih komunikasi klinis',
            'retina.speaker.bio' => 'Berpengalaman melatih tenaga kesehatan.',
        ]);

        $this->get(route('home'))
            ->assertSee('Pelatih komunikasi klinis')
            ->assertSee('Berpengalaman melatih tenaga kesehatan.');
    }

    public function test_contact_link_renders_only_when_configured(): void
    {
        $this->get(route('home'))->assertDontSee('wa.me', false);

        config(['retina.contact.whatsapp' => '0812-3456-7890']);

        $this->get(route('home'))->assertSee('https://wa.me/6281234567890', false);
    }
}
