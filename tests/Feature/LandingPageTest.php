<?php

namespace Tests\Feature;

use App\Models\Participant;
use App\Models\Setting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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
        $this->get(route('home'))
            ->assertSee('Guest speaker RETINA 2026')
            ->assertDontSee('r-speaker__bio', false)
            ->assertDontSee('Dipandu oleh');

        config([
            'retina.speaker.role' => 'Pelatih komunikasi klinis',
            'retina.speaker.bio' => 'Berpengalaman melatih tenaga kesehatan.',
        ]);

        $this->get(route('home'))
            ->assertSee('Pelatih komunikasi klinis')
            ->assertSee('Berpengalaman melatih tenaga kesehatan.');
    }

    public function test_speaker_photo_falls_back_to_the_banner_crop(): void
    {
        $this->assertFileExists(public_path(config('retina.speaker.photo_default')));

        $this->get(route('home'))
            ->assertSee('r-speaker--photo', false)
            ->assertSee('src="'.asset(config('retina.speaker.photo_default')).'"', false);
    }

    public function test_uploaded_speaker_photo_takes_precedence_over_the_default(): void
    {
        config(['retina.speaker.photo' => 'landing/speaker.jpg']);

        $this->get(route('home'))
            ->assertSee('src="'.Storage::disk('public')->url('landing/speaker.jpg').'"', false)
            ->assertDontSee(config('retina.speaker.photo_default'), false);
    }

    public function test_speaker_section_has_no_photo_without_upload_or_default(): void
    {
        config(['retina.speaker.photo_default' => null]);

        $this->get(route('home'))->assertDontSee('r-speaker--photo', false);
    }

    public function test_contact_link_renders_only_when_configured(): void
    {
        $this->get(route('home'))->assertDontSee('wa.me', false);

        config(['retina.contact.whatsapp' => '0812-3456-7890']);

        $this->get(route('home'))->assertSee('https://wa.me/6281234567890', false);
    }

    public function test_hero_shows_the_event_banner(): void
    {
        $banner = config('retina.banner');

        $this->assertFileExists(public_path($banner['file']));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<h1 class="r-sr-only">RETINA 2026', false)
            ->assertSee('src="'.asset($banner['file']).'"', false)
            ->assertSee($banner['alt']);
    }

    public function test_hero_strip_shows_the_registration_call_to_action_while_open(): void
    {
        $this->get(route('home'))
            ->assertSeeInOrder([
                'r-hero__strip',
                'Sabtu, 31 Oktober 2026 · 09.00 WIB',
                'Pendaftaran dibuka',
                'href="'.route('registration').'">Amankan kursimu',
            ], false);
    }

    public function test_hero_strip_shows_a_closed_message_once_registration_closes(): void
    {
        Setting::putValue(Participant::SETTING_CLOSING_AT, '2026-10-13 23:59:00');

        $this->get(route('home'))
            ->assertSeeInOrder(['r-hero__strip', 'r-hero__status--closed', 'Pendaftaran ditutup'], false)
            ->assertDontSee('Amankan kursimu');
    }

    public function test_social_preview_uses_the_banner(): void
    {
        $bannerUrl = asset(config('retina.banner.file'));

        $this->get(route('home'))
            ->assertSee('<meta property="og:image" content="'.$bannerUrl.'">', false)
            ->assertSee('<meta name="twitter:image" content="'.$bannerUrl.'">', false)
            ->assertSee('"image":["'.$bannerUrl.'"]', false);
    }

    public function test_location_section_shows_the_campus_photo_with_its_source_credit(): void
    {
        $image = config('retina.venue_image');

        $this->assertFileExists(public_path($image['file']));

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['id="lokasi"', asset($image['file'])], false)
            ->assertSee($image['alt'])
            ->assertSee($image['credit'])
            ->assertSee($image['source'], false);
    }

    public function test_ticket_preview_links_to_the_registration_form(): void
    {
        $this->get(route('home'))
            ->assertSee('data-ticket-name', false)
            ->assertSee('data-ticket-holder', false)
            ->assertSee('data-ticket-link', false);
    }

    public function test_ticket_preview_link_is_hidden_once_registration_closes(): void
    {
        Setting::putValue(Participant::SETTING_CLOSING_AT, '2026-10-13 23:59:00');

        $this->get(route('home'))
            ->assertSee('data-ticket-name', false)
            ->assertDontSee('data-ticket-link', false)
            ->assertSee('Pendaftaran ditutup.');
    }

    public function test_landing_page_has_ukwms_logo_and_seo_metadata(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('images/retina/logo-ukwms.png', false)
            ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('favicon-32x32.png', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"Event"', false);
    }

    public function test_registration_page_is_not_given_event_structured_data(): void
    {
        $this->get(route('registration'))
            ->assertOk()
            ->assertDontSee('application/ld+json', false);
    }

    public function test_sitemap_lists_public_pages(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee('<loc>'.route('registration').'</loc>', false);
    }
}
