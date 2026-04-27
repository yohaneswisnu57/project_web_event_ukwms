<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvitationStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '3s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Undangan', \App\Models\Invitation::count())
                ->description('Seluruh data undangan')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('info'),
                
            Stat::make('Sudah Registrasi', \App\Models\Invitation::where('is_used', true)->count())
                ->description('Tamu yang sudah konfirmasi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Belum Registrasi', \App\Models\Invitation::where('is_used', false)->count())
                ->description('Menunggu konfirmasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Tamu Hadir', \App\Models\Registration::sum('attendance_count'))
                ->description('Akumulasi jumlah orang')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
