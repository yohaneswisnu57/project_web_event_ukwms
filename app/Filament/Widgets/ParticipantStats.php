<?php

namespace App\Filament\Widgets;

use App\Enums\ParticipantStatus;
use App\Models\Participant;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ParticipantStats extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected ?string $pollingInterval = '10s';

    protected ?string $heading = 'RETINA 2026';

    protected function getStats(): array
    {
        $counts = Participant::query()
            ->selectRaw('count(case when status != ? then 1 end) as registered', [ParticipantStatus::Rejected->value])
            ->selectRaw('count(case when status = ? then 1 end) as pending', [ParticipantStatus::Pending->value])
            ->selectRaw('count(case when status = ? then 1 end) as verified', [ParticipantStatus::Verified->value])
            ->selectRaw('count(case when status = ? and checked_in_at is not null then 1 end) as attended', [ParticipantStatus::Verified->value])
            ->selectRaw('count(case when status = ? and food_allergy is not null then 1 end) as with_allergy', [ParticipantStatus::Verified->value])
            ->first();

        $quota = Participant::quota();
        $closesAt = Participant::registrationClosesAt();

        return [
            Stat::make('Pendaftar', $quota ? "{$counts->registered} / {$quota}" : (string) $counts->registered)
                ->description($closesAt ? 'Tutup '.$closesAt->translatedFormat('d M Y H:i') : 'Tanpa batas waktu')
                ->descriptionIcon('heroicon-m-user-group')
                ->color(Participant::isRegistrationOpen() ? 'primary' : 'danger'),

            Stat::make('Menunggu Verifikasi', $counts->pending)
                ->description('Bukti bayar belum dicek')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Terverifikasi', $counts->verified)
                ->description(Number::currency($counts->verified * Participant::REGISTRATION_FEE, 'IDR', 'id', 0).' dana masuk')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Hadir', "{$counts->attended} / {$counts->verified}")
                ->description("{$counts->with_allergy} peserta punya alergi makanan")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
        ];
    }
}
