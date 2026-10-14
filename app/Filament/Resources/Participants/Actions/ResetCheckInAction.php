<?php

namespace App\Filament\Resources\Participants\Actions;

use App\Models\Participant;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class ResetCheckInAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'resetCheckIn';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Reset Check-in')
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('warning')
            ->visible(fn (Participant $record): bool => filled($record->checked_in_at))
            ->requiresConfirmation()
            ->modalHeading('Reset Kehadiran?')
            ->modalDescription('Waktu check-in peserta ini akan dikosongkan.')
            ->action(function (Participant $record): void {
                $record->update(['checked_in_at' => null]);

                Notification::make()
                    ->title('Check-in direset')
                    ->success()
                    ->send();
            });
    }
}
