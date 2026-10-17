<?php

namespace Tests\Feature\Filament;

use App\Enums\ParticipantStatus;
use App\Filament\Exports\ParticipantExporter;
use App\Filament\Resources\Participants\Pages\EditParticipant;
use App\Filament\Resources\Participants\Pages\ListParticipants;
use App\Filament\Resources\Participants\Pages\ViewParticipant;
use App\Filament\Widgets\ParticipantStats;
use App\Mail\ParticipantRejectedMail;
use App\Mail\ParticipantVerifiedMail;
use App\Models\Participant;
use App\Models\Setting;
use App\Models\User;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ParticipantResourceTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_guest_cannot_access_participant_list(): void
    {
        auth()->logout();

        $this->get(route('filament.admin.resources.participants.index'))
            ->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_list_page_shows_participants(): void
    {
        $participants = Participant::factory()->count(3)->create();

        Livewire::test(ListParticipants::class)
            ->assertOk()
            ->assertCanSeeTableRecords($participants);
    }

    public function test_table_can_be_filtered_by_status(): void
    {
        $pending = Participant::factory()->create();
        $verified = Participant::factory()->verified()->create();

        Livewire::test(ListParticipants::class)
            ->filterTable('status', ParticipantStatus::Pending->value)
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$verified]);
    }

    public function test_table_can_be_searched_by_student_number(): void
    {
        $target = Participant::factory()->create(['student_number' => '1923021001']);
        $other = Participant::factory()->create(['student_number' => '1923021999']);

        Livewire::test(ListParticipants::class)
            ->searchTable('1923021001')
            ->assertCanSeeTableRecords([$target])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_admin_can_approve_pending_participant_and_ticket_is_mailed(): void
    {
        Mail::fake();
        $participant = Participant::factory()->create(['email' => 'peserta@example.com']);

        Livewire::test(ListParticipants::class)
            ->callAction(TestAction::make('approve')->table($participant))
            ->assertHasNoFormErrors()
            ->assertNotified();

        $participant->refresh();
        $this->assertSame(ParticipantStatus::Verified, $participant->status);
        $this->assertTrue($participant->reviewer->is($this->admin));
        $this->assertNotNull($participant->reviewed_at);

        Mail::assertQueued(ParticipantVerifiedMail::class, fn (ParticipantVerifiedMail $mail): bool => $mail->hasTo('peserta@example.com')
            && $mail->participant->is($participant));
    }

    public function test_review_actions_are_hidden_once_participant_is_reviewed(): void
    {
        $verified = Participant::factory()->verified()->create();
        $rejected = Participant::factory()->rejected()->create();

        Livewire::test(ListParticipants::class)
            ->assertActionHidden(TestAction::make('approve')->table($verified))
            ->assertActionHidden(TestAction::make('reject')->table($verified))
            ->assertActionHidden(TestAction::make('approve')->table($rejected))
            ->assertActionHidden(TestAction::make('reject')->table($rejected));
    }

    public function test_rejecting_requires_a_reason(): void
    {
        Mail::fake();
        $participant = Participant::factory()->create();

        Livewire::test(ListParticipants::class)
            ->callAction(TestAction::make('reject')->table($participant), data: [
                'rejection_reason' => '',
            ])
            ->assertHasFormErrors(['rejection_reason' => 'required']);

        $this->assertSame(ParticipantStatus::Pending, $participant->refresh()->status);
        Mail::assertNothingQueued();
    }

    public function test_admin_can_reject_participant_with_reason_and_email_is_sent(): void
    {
        Mail::fake();
        $participant = Participant::factory()->create(['email' => 'peserta@example.com']);

        Livewire::test(ListParticipants::class)
            ->callAction(TestAction::make('reject')->table($participant), data: [
                'rejection_reason' => 'Nominal transfer tidak sesuai.',
            ])
            ->assertHasNoFormErrors();

        $participant->refresh();
        $this->assertSame(ParticipantStatus::Rejected, $participant->status);
        $this->assertSame('Nominal transfer tidak sesuai.', $participant->rejection_reason);
        $this->assertTrue($participant->reviewer->is($this->admin));

        Mail::assertQueued(ParticipantRejectedMail::class, fn (ParticipantRejectedMail $mail): bool => $mail->hasTo('peserta@example.com'));
    }

    public function test_rejection_email_asks_to_register_again_with_another_email(): void
    {
        $participant = Participant::factory()->rejected('Bukti tidak terbaca.')->create();

        (new ParticipantRejectedMail($participant))
            ->assertSeeInHtml('Bukti tidak terbaca.')
            ->assertSeeInHtml('alamat email yang berbeda');
    }

    public function test_verified_email_contains_event_details(): void
    {
        $participant = Participant::factory()->verified()->create(['full_name' => 'Budi Santoso']);

        (new ParticipantVerifiedMail($participant))
            ->assertSeeInHtml('Budi Santoso')
            ->assertSeeInHtml('31 Oktober 2026')
            ->assertSeeInHtml('Ruang Teater Barat');
    }

    public function test_admin_can_resend_ticket_to_verified_participant(): void
    {
        Mail::fake();
        $pending = Participant::factory()->create();
        $verified = Participant::factory()->verified()->create();

        Livewire::test(ListParticipants::class)
            ->assertActionHidden(TestAction::make('resendTicket')->table($pending))
            ->callAction(TestAction::make('resendTicket')->table($verified));

        Mail::assertQueued(ParticipantVerifiedMail::class, 1);
    }

    public function test_admin_can_check_in_and_reset_verified_participant(): void
    {
        $pending = Participant::factory()->create();
        $verified = Participant::factory()->verified()->create();

        Livewire::test(ListParticipants::class)
            ->assertActionHidden(TestAction::make('checkIn')->table($pending))
            ->callAction(TestAction::make('checkIn')->table($verified));

        $this->assertNotNull($verified->refresh()->checked_in_at);

        Livewire::test(ListParticipants::class)
            ->assertActionHidden(TestAction::make('checkIn')->table($verified))
            ->callAction(TestAction::make('resetCheckIn')->table($verified));

        $this->assertNull($verified->refresh()->checked_in_at);
    }

    public function test_registration_settings_can_be_saved_and_cleared(): void
    {
        Livewire::test(ListParticipants::class)
            ->callAction('registrationSettings', data: [
                'closing_date' => '2026-10-28',
                'closing_time' => '23:59:00',
                'quota' => 150,
            ])
            ->assertHasNoFormErrors();

        $this->assertSame('2026-10-28 23:59:00', Setting::getValue(Participant::SETTING_CLOSING_AT));
        $this->assertSame(150, Participant::quota());

        Livewire::test(ListParticipants::class)
            ->callAction('registrationSettings', data: [
                'closing_date' => null,
                'quota' => null,
            ])
            ->assertHasNoFormErrors();

        $this->assertNull(Participant::registrationClosesAt());
        $this->assertNull(Participant::quota());
    }

    public function test_registration_settings_reject_invalid_quota(): void
    {
        Livewire::test(ListParticipants::class)
            ->callAction('registrationSettings', data: [
                'quota' => 0,
            ])
            ->assertHasFormErrors(['quota' => 'min']);
    }

    public function test_view_page_shows_details_and_duplicate_warning(): void
    {
        Storage::fake(Participant::PAYMENT_PROOF_DISK);
        $participant = Participant::factory()->withAllergy('Kacang')->create([
            'full_name' => 'Citra Lestari',
            'student_number' => '1923021001',
        ]);
        Participant::factory()->create([
            'full_name' => 'Citra Duplikat',
            'student_number' => '1923021001',
        ]);

        Livewire::test(ViewParticipant::class, ['record' => $participant->uuid])
            ->assertOk()
            ->assertSee('Citra Lestari')
            ->assertSee('Kacang')
            ->assertSee('Kemungkinan pendaftaran ganda')
            ->assertSee('Citra Duplikat')
            ->assertActionVisible('approve')
            ->assertActionVisible('reject');
    }

    public function test_edit_switching_to_internal_clears_institution(): void
    {
        $participant = Participant::factory()->external()->create();

        Livewire::test(EditParticipant::class, ['record' => $participant->uuid])
            ->fillForm(['origin' => 'internal'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($participant->refresh()->institution);
    }

    public function test_edit_requires_institution_for_external_and_unique_email(): void
    {
        Participant::factory()->create(['email' => 'dipakai@example.com']);
        $participant = Participant::factory()->create();

        Livewire::test(EditParticipant::class, ['record' => $participant->uuid])
            ->fillForm([
                'origin' => 'external',
                'institution' => null,
                'email' => 'dipakai@example.com',
            ])
            ->call('save')
            ->assertHasFormErrors([
                'institution' => 'required',
                'email' => 'unique',
            ]);
    }

    public function test_admin_can_upload_payment_proof_on_edit(): void
    {
        Storage::fake(Participant::PAYMENT_PROOF_DISK);
        $participant = Participant::factory()->create(['payment_proof_path' => null]);

        Livewire::test(EditParticipant::class, ['record' => $participant->uuid])
            ->fillForm([
                'payment_proof_path' => UploadedFile::fake()->image('bukti.jpg'),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $path = $participant->refresh()->payment_proof_path;
        $this->assertStringStartsWith(Participant::PAYMENT_PROOF_DIRECTORY.'/', $path);
        Storage::disk(Participant::PAYMENT_PROOF_DISK)->assertExists($path);
    }

    public function test_deleting_participant_removes_payment_proof(): void
    {
        Storage::fake(Participant::PAYMENT_PROOF_DISK);
        $path = UploadedFile::fake()->image('bukti.jpg')->store(Participant::PAYMENT_PROOF_DIRECTORY, Participant::PAYMENT_PROOF_DISK);
        $participant = Participant::factory()->create(['payment_proof_path' => $path]);

        $participant->delete();

        Storage::disk(Participant::PAYMENT_PROOF_DISK)->assertMissing($path);
    }

    public function test_export_respects_table_filters(): void
    {
        Storage::fake(Participant::PAYMENT_PROOF_DISK);
        Participant::factory()->checkedIn()->create();
        Participant::factory()->verified()->create();

        Livewire::test(ListParticipants::class)
            ->filterTable('checked_in_at', true)
            ->callAction(TestAction::make('export')->table())
            ->assertHasNoFormErrors();

        $export = Export::query()->sole();
        $this->assertSame(ParticipantExporter::class, $export->exporter);
        $this->assertSame(1, $export->total_rows);
    }

    public function test_stats_widget_summarises_participants(): void
    {
        Participant::factory()->count(2)->create();
        Participant::factory()->checkedIn()->withAllergy()->create();
        Participant::factory()->verified()->create();
        Participant::factory()->rejected()->create();

        Livewire::test(ParticipantStats::class)
            ->assertOk()
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('60.000 dana masuk')
            ->assertSee('1 / 2')
            ->assertSee('1 peserta punya alergi makanan');
    }
}
