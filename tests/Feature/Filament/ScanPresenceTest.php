<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\ScanPresence;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class ScanPresenceTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_scanning_verified_participant_shows_details(): void
    {
        $participant = Participant::factory()->verified()->create(['full_name' => 'Dewi Anggraini']);

        Livewire::test(ScanPresence::class)
            ->call('checkIn', $participant->uuid)
            ->assertSet('isScanning', false)
            ->assertSet('scannedParticipant.id', $participant->id)
            ->assertSee('Dewi Anggraini')
            ->assertSee($participant->code);
    }

    public function test_confirming_presence_records_check_in_time(): void
    {
        $participant = Participant::factory()->verified()->create();

        Livewire::test(ScanPresence::class)
            ->call('checkIn', $participant->uuid)
            ->call('confirmPresence')
            ->assertNotified('Check-in Berhasil')
            ->assertSet('isScanning', true);

        $this->assertNotNull($participant->refresh()->checked_in_at);
    }

    public function test_confirming_presence_twice_keeps_first_check_in_time(): void
    {
        $firstCheckIn = Carbon::parse('2026-10-31 08:45:00');
        $participant = Participant::factory()->verified()->create(['checked_in_at' => $firstCheckIn]);

        Livewire::test(ScanPresence::class)
            ->call('checkIn', $participant->uuid)
            ->call('confirmPresence')
            ->assertNotified('Gagal');

        $this->assertTrue($participant->refresh()->checked_in_at->equalTo($firstCheckIn));
    }

    public function test_unverified_participant_cannot_check_in(): void
    {
        $pending = Participant::factory()->create();
        $rejected = Participant::factory()->rejected()->create();

        Livewire::test(ScanPresence::class)
            ->call('checkIn', $pending->uuid)
            ->assertNotified('Belum Terverifikasi')
            ->assertSet('isScanning', true)
            ->assertSet('scannedParticipant', null)
            ->call('checkIn', $rejected->uuid)
            ->assertSet('scannedParticipant', null);
    }

    public function test_invalid_or_unknown_qr_is_rejected(): void
    {
        Livewire::test(ScanPresence::class)
            ->call('checkIn', 'https://example.com/not-a-ticket')
            ->assertNotified('QR Tidak Valid')
            ->call('checkIn', '9b2f1c7e-0000-4000-8000-000000000000')
            ->assertNotified('Error')
            ->assertSet('scannedParticipant', null);
    }

    public function test_checked_in_participants_are_listed(): void
    {
        $attended = Participant::factory()->checkedIn()->create();
        $absent = Participant::factory()->verified()->create();

        Livewire::test(ScanPresence::class)
            ->assertCanSeeTableRecords([$attended])
            ->assertCanNotSeeTableRecords([$absent]);
    }
}
