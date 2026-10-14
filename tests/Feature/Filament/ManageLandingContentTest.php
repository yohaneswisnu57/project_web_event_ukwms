<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\ManageLandingContent;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ManageLandingContentTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 10, 14, 10, 0, 0, 'Asia/Jakarta'));
        Storage::fake(ManageLandingContent::PHOTO_DISK);

        $this->actingAs(User::factory()->create());
    }

    /**
     * @return array<string, mixed>
     */
    protected function validContent(array $overrides = []): array
    {
        return array_merge([
            'speaker_role' => 'Dokter & pelatih public speaking',
            'speaker_bio' => 'Melatih tenaga kesehatan berbicara di depan publik.',
            'sessions' => [
                ['title' => 'Membuka dengan kuat', 'description' => 'Merancang pembukaan yang menarik perhatian.'],
                ['title' => 'Menutup dengan berkesan', 'description' => 'Merangkum pesan agar mudah diingat.'],
            ],
            'ends_at_time' => '12:30',
            'perks' => 'Termasuk sertifikat',
            'contact_name' => 'Kak Rina',
            'contact_whatsapp' => '081298765432',
        ], $overrides);
    }

    public function test_guest_cannot_access_the_page(): void
    {
        auth()->logout();

        $this->get(ManageLandingContent::getUrl())
            ->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_form_is_prefilled_with_config_defaults(): void
    {
        Livewire::test(ManageLandingContent::class)
            ->assertOk()
            ->assertFormSet([
                'speaker_role' => null,
                'perks' => config('retina.perks'),
                'ends_at_time' => null,
            ])
            ->assertFormFieldExists('sessions')
            ->assertSee(config('retina.sessions.0.title'));
    }

    public function test_saved_content_is_shown_on_the_landing_page(): void
    {
        Livewire::test(ManageLandingContent::class)
            ->fillForm($this->validContent())
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Konten landing page disimpan');

        $this->assertSame('12:30', Setting::getJson(Setting::LANDING_CONTENT)['ends_at_time']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Dokter &amp; pelatih public speaking', false)
            ->assertSee('Melatih tenaga kesehatan berbicara di depan publik.')
            ->assertSee('Dua babak')
            ->assertSee('Membuka dengan kuat')
            ->assertSee('Menutup dengan berkesan')
            ->assertDontSee('Menyusun pesan')
            ->assertSee('09.00–12.30 WIB')
            ->assertSee('Termasuk sertifikat')
            ->assertSee('https://wa.me/6281298765432', false)
            ->assertSee('Narahubung: Kak Rina');
    }

    public function test_saved_form_reloads_with_stored_values(): void
    {
        Livewire::test(ManageLandingContent::class)
            ->fillForm($this->validContent())
            ->call('save');

        Livewire::test(ManageLandingContent::class)
            ->assertFormSet([
                'speaker_role' => 'Dokter & pelatih public speaking',
                'ends_at_time' => '12:30',
                'contact_name' => 'Kak Rina',
            ])
            ->assertSee('Membuka dengan kuat');
    }

    public function test_cleared_fields_are_hidden_instead_of_falling_back_to_defaults(): void
    {
        Livewire::test(ManageLandingContent::class)
            ->fillForm($this->validContent(['perks' => '', 'ends_at_time' => null]))
            ->call('save')
            ->assertHasNoFormErrors();

        $defaultPerks = config('retina.perks');

        $this->get(route('home'))
            ->assertDontSee($defaultPerks)
            ->assertDontSee('hingga ');
    }

    public function test_content_is_validated(): void
    {
        Livewire::test(ManageLandingContent::class)
            ->fillForm($this->validContent([
                'sessions' => [],
                'ends_at_time' => '08:00',
                'contact_whatsapp' => '12345',
            ]))
            ->call('save')
            ->assertHasFormErrors(['sessions', 'ends_at_time', 'contact_whatsapp']);

        Livewire::test(ManageLandingContent::class)
            ->fillForm($this->validContent(['contact_whatsapp' => '']))
            ->call('save')
            ->assertHasFormErrors(['contact_whatsapp' => 'required_with']);

        $this->assertSame([], Setting::getJson(Setting::LANDING_CONTENT));
    }

    public function test_speaker_photo_is_stored_publicly_and_replaced_cleanly(): void
    {
        Livewire::test(ManageLandingContent::class)
            ->fillForm($this->validContent(['speaker_photo' => UploadedFile::fake()->image('henry.jpg', 400, 500)]))
            ->call('save')
            ->assertHasNoFormErrors();

        $firstPhoto = Setting::getJson(Setting::LANDING_CONTENT)['speaker_photo'];

        $this->assertStringStartsWith(ManageLandingContent::PHOTO_DIRECTORY.'/', $firstPhoto);
        Storage::disk(ManageLandingContent::PHOTO_DISK)->assertExists($firstPhoto);

        Livewire::test(ManageLandingContent::class)
            ->fillForm(['speaker_photo' => [UploadedFile::fake()->image('henry-baru.jpg', 400, 500)]])
            ->call('save')
            ->assertHasNoFormErrors();

        $secondPhoto = Setting::getJson(Setting::LANDING_CONTENT)['speaker_photo'];

        $this->assertNotSame($firstPhoto, $secondPhoto);
        Storage::disk(ManageLandingContent::PHOTO_DISK)->assertMissing($firstPhoto);
        Storage::disk(ManageLandingContent::PHOTO_DISK)->assertExists($secondPhoto);

        $this->get(route('home'))
            ->assertSee(Storage::disk(ManageLandingContent::PHOTO_DISK)->url($secondPhoto), false)
            ->assertSee('alt="Foto Henry Christian Tjandra"', false);
    }

    public function test_removing_the_photo_deletes_it_and_falls_back_to_initials(): void
    {
        Livewire::test(ManageLandingContent::class)
            ->fillForm($this->validContent(['speaker_photo' => UploadedFile::fake()->image('henry.jpg', 400, 500)]))
            ->call('save');

        $photo = Setting::getJson(Setting::LANDING_CONTENT)['speaker_photo'];

        Livewire::test(ManageLandingContent::class)
            ->fillForm(['speaker_photo' => []])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull(Setting::getJson(Setting::LANDING_CONTENT)['speaker_photo']);
        Storage::disk(ManageLandingContent::PHOTO_DISK)->assertMissing($photo);

        $this->get(route('home'))
            ->assertDontSee('alt="Foto Henry Christian Tjandra"', false)
            ->assertDontSee('r-speaker__media', false);
    }

    public function test_landing_page_uses_config_defaults_until_content_is_saved(): void
    {
        $this->get(route('home'))
            ->assertSee('Tiga babak')
            ->assertSee(config('retina.sessions.0.title'))
            ->assertDontSee('wa.me', false);
    }
}
