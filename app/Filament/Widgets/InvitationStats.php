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
                ->description('Total instansi yang diundang')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('info'),
                
            Stat::make('Instansi Terdaftar', \App\Models\Registration::count())
                ->description('Instansi yang sudah mengisi form')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Total Orang Terdaftar', \App\Models\Registration::sum('attendance_count'))
                ->description('Total pendaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Tamu Sudah Hadir', \App\Models\Registration::whereNotNull('checked_in_at')->sum('attendance_count'))
                ->description('Jumlah orang yang sudah scan QR')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
        ];
    }
}
