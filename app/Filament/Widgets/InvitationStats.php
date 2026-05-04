<?php

namespace App\Filament\Widgets;

use App\Models\Invitation;
use App\Models\Registration;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvitationStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '3s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Undangan', Invitation::count())
                ->description('Total instansi yang diundang')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('info'),

            Stat::make('Registrasi Masuk', Registration::count())
                ->description('Instansi yang sudah mengisi form')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),

            Stat::make('Belum Divalidasi', Registration::whereNull('validated_at')->count())
                ->description('User menunggu validasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Tamu Sudah Hadir', Registration::whereNotNull('checked_in_at')->sum('attendance_count'))
                ->description('Jumlah orang yang sudah scan QR')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
        ];
    }
}
