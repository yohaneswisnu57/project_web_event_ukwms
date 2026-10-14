<?php

namespace Tests\Feature;

use App\Models\Participant;
use App\Models\Setting;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ParticipantRegistrationWindowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_registration_is_open_without_any_settings(): void
    {
        $this->assertNull(Participant::quota());
        $this->assertNull(Participant::remainingQuota());
        $this->assertTrue(Participant::isRegistrationOpen());
    }

    public function test_registration_closes_after_closing_time(): void
    {
        Setting::putValue(Participant::SETTING_CLOSING_AT, '2026-10-28 23:59:00');

        Carbon::setTestNow(Carbon::create(2026, 10, 28, 23, 59, 0, 'Asia/Jakarta'));
        $this->assertTrue(Participant::isRegistrationOpen());

        Carbon::setTestNow(Carbon::create(2026, 10, 28, 23, 59, 1, 'Asia/Jakarta'));
        $this->assertFalse(Participant::isRegistrationOpen());
    }

    public function test_registration_closes_when_quota_is_full(): void
    {
        Setting::putValue(Participant::SETTING_QUOTA, '2');
        Participant::factory()->create();

        $this->assertSame(1, Participant::remainingQuota());
        $this->assertTrue(Participant::isRegistrationOpen());

        Participant::factory()->verified()->create();

        $this->assertSame(0, Participant::remainingQuota());
        $this->assertFalse(Participant::isRegistrationOpen());
    }

    public function test_rejected_participants_free_up_quota(): void
    {
        Setting::putValue(Participant::SETTING_QUOTA, '1');
        Participant::factory()->rejected()->create();

        $this->assertSame(1, Participant::remainingQuota());
        $this->assertTrue(Participant::isRegistrationOpen());
    }

    public function test_possible_duplicates_ignore_self_and_rejected_registrations(): void
    {
        $participant = Participant::factory()->create(['student_number' => '111', 'whatsapp' => '081111111111']);
        $sameNumber = Participant::factory()->create(['student_number' => '111']);
        $sameWhatsapp = Participant::factory()->verified()->create(['whatsapp' => '081111111111']);
        Participant::factory()->rejected()->create(['student_number' => '111']);
        Participant::factory()->create();

        $this->assertEqualsCanonicalizing(
            [$sameNumber->id, $sameWhatsapp->id],
            $participant->possibleDuplicates()->modelKeys(),
        );
    }

    public function test_email_must_be_unique_across_all_registrations(): void
    {
        Participant::factory()->rejected()->create(['email' => 'sama@example.com']);

        $this->expectException(UniqueConstraintViolationException::class);

        Participant::factory()->create(['email' => 'sama@example.com']);
    }
}
