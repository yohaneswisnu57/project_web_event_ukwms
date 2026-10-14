<?php

namespace Tests\Feature;

use App\Enums\ParticipantOrigin;
use App\Enums\ParticipantStatus;
use App\Livewire\RegistrationForm;
use App\Models\Participant;
use App\Models\Setting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationFormTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 10, 14, 10, 0, 0, 'Asia/Jakarta'));
        Storage::fake(Participant::PAYMENT_PROOF_DISK);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function fillForm(array $overrides = []): Testable
    {
        $data = array_merge([
            'full_name' => 'Maria Natalia',
            'email' => 'maria@example.com',
            'whatsapp' => '081234567890',
            'origin' => 'internal',
            'faculty' => 'Kedokteran',
            'student_number' => '1923024001',
            'cohort_year' => '2024',
            'food_allergy' => '',
            'payment_proof' => UploadedFile::fake()->image('bukti.jpg'),
        ], $overrides);

        $component = Livewire::test(RegistrationForm::class);

        foreach ($data as $property => $value) {
            $component->set($property, $value);
        }

        return $component;
    }

    public function test_registration_page_renders_the_form(): void
    {
        $this->get(route('registration'))
            ->assertOk()
            ->assertSeeLivewire(RegistrationForm::class)
            ->assertSee('Formulir pendaftaran')
            ->assertSee('141 002 178 3519');
    }

    public function test_internal_student_can_register(): void
    {
        $component = $this->fillForm(['food_allergy' => 'Udang'])
            ->call('register')
            ->assertHasNoErrors();

        $participant = Participant::query()->sole();

        $component
            ->assertSet('registeredCode', $participant->code)
            ->assertSet('registeredEmail', 'maria@example.com')
            ->assertSet('full_name', '')
            ->assertSee('Pendaftaran diterima.')
            ->assertSee($participant->code);

        $this->assertSame('Maria Natalia', $participant->full_name);
        $this->assertSame(ParticipantOrigin::Internal, $participant->origin);
        $this->assertSame(ParticipantStatus::Pending, $participant->status);
        $this->assertNull($participant->institution);
        $this->assertSame(2024, $participant->cohort_year);
        $this->assertSame('Udang', $participant->food_allergy);
        $this->assertStringStartsWith(Participant::PAYMENT_PROOF_DIRECTORY.'/', $participant->payment_proof_path);
        Storage::disk(Participant::PAYMENT_PROOF_DISK)->assertExists($participant->payment_proof_path);
    }

    public function test_external_student_must_fill_institution(): void
    {
        $this->fillForm(['origin' => 'external'])
            ->call('register')
            ->assertHasErrors(['institution' => 'required']);

        $this->fillForm(['origin' => 'external', 'institution' => 'Universitas Airlangga'])
            ->call('register')
            ->assertHasNoErrors();

        $participant = Participant::query()->sole();

        $this->assertSame(ParticipantOrigin::External, $participant->origin);
        $this->assertSame('Universitas Airlangga', $participant->institution);
    }

    public function test_institution_is_discarded_for_internal_students(): void
    {
        $this->fillForm(['origin' => 'external', 'institution' => 'Universitas Airlangga'])
            ->set('origin', 'internal')
            ->call('register')
            ->assertHasNoErrors();

        $this->assertNull(Participant::query()->sole()->institution);
    }

    public function test_required_fields_are_validated(): void
    {
        Livewire::test(RegistrationForm::class)
            ->call('register')
            ->assertHasErrors([
                'full_name' => 'required',
                'email' => 'required',
                'whatsapp' => 'required',
                'faculty' => 'required',
                'student_number' => 'required',
                'cohort_year' => 'required',
                'payment_proof' => 'required',
            ])
            ->assertHasNoErrors(['institution', 'food_allergy', 'origin'])
            ->assertSee('Nama lengkap wajib diisi.');

        $this->assertDatabaseCount(Participant::class, 0);
    }

    public function test_whatsapp_is_normalized_before_validation(): void
    {
        $this->fillForm(['whatsapp' => '0812 3456-7890'])
            ->call('register')
            ->assertHasNoErrors();

        $this->assertSame('081234567890', Participant::query()->sole()->whatsapp);
    }

    public function test_invalid_values_are_rejected(): void
    {
        $this->fillForm([
            'email' => 'bukan-email',
            'whatsapp' => '12345',
            'cohort_year' => '1999',
        ])
            ->call('register')
            ->assertHasErrors([
                'email' => 'email',
                'whatsapp' => 'regex',
                'cohort_year' => 'min',
            ]);

        $this->fillForm(['cohort_year' => '2027'])
            ->call('register')
            ->assertHasErrors(['cohort_year' => 'max']);

        $this->assertDatabaseCount(Participant::class, 0);
    }

    public function test_payment_proof_must_be_a_small_image(): void
    {
        $this->fillForm(['payment_proof' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf')])
            ->assertHasErrors(['payment_proof' => 'image']);

        $this->fillForm(['payment_proof' => UploadedFile::fake()->image('bukti.jpg')->size(5000)])
            ->assertHasErrors(['payment_proof' => 'max']);
    }

    public function test_email_cannot_be_reused_even_after_rejection(): void
    {
        Participant::factory()->rejected()->create(['email' => 'maria@example.com']);

        $this->fillForm(['email' => 'Maria@Example.com '])
            ->call('register')
            ->assertHasErrors(['email' => 'unique'])
            ->assertSee('Email ini sudah dipakai untuk mendaftar.');

        $this->assertDatabaseCount(Participant::class, 1);
    }

    public function test_fixed_fields_lose_their_error_once_corrected(): void
    {
        $this->fillForm(['whatsapp' => '123'])
            ->call('register')
            ->assertHasErrors('whatsapp')
            ->set('whatsapp', '081234567890')
            ->assertHasNoErrors('whatsapp');
    }

    public function test_closed_registration_shows_notice_instead_of_form(): void
    {
        Setting::putValue(Participant::SETTING_CLOSING_AT, '2026-10-13 23:59:00');

        $this->get(route('registration'))
            ->assertOk()
            ->assertSee('Pendaftaran ditutup.')
            ->assertSee('Batas waktu pendaftaran')
            ->assertDontSee('Kirim pendaftaran');
    }

    public function test_full_quota_shows_quota_notice(): void
    {
        Setting::putValue(Participant::SETTING_QUOTA, '1');
        Participant::factory()->create();

        Livewire::test(RegistrationForm::class)
            ->assertSee('Kuota peserta')
            ->assertDontSee('Kirim pendaftaran');
    }

    public function test_registration_is_refused_when_quota_fills_before_submit(): void
    {
        Setting::putValue(Participant::SETTING_QUOTA, '1');

        $component = $this->fillForm();

        Participant::factory()->create();

        $component->call('register')
            ->assertSet('registeredCode', null)
            ->assertSee('Pendaftaran ditutup.');

        $this->assertDatabaseCount(Participant::class, 1);
        $this->assertSame([], Storage::disk(Participant::PAYMENT_PROOF_DISK)->allFiles(Participant::PAYMENT_PROOF_DIRECTORY));
    }

    public function test_submissions_are_rate_limited_per_ip(): void
    {
        for ($attempt = 1; $attempt <= RegistrationForm::MAX_ATTEMPTS_PER_MINUTE; $attempt++) {
            $this->fillForm(['email' => "peserta{$attempt}@example.com"])
                ->call('register')
                ->assertHasNoErrors();
        }

        $this->fillForm(['email' => 'satu-lagi@example.com'])
            ->call('register')
            ->assertHasErrors('form');

        $this->assertDatabaseCount(Participant::class, RegistrationForm::MAX_ATTEMPTS_PER_MINUTE);
    }
}
