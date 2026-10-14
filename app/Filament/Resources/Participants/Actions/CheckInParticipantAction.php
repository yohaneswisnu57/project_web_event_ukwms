<?php

namespace App\Filament\Resources\Participants\Actions;

use App\Models\Participant;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class CheckInParticipantAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'checkIn';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Tandai Hadir')
            ->icon(Heroicon::OutlinedUserPlus)
            ->color('success')
            ->visible(fn (Participant $record): bool => $record->isVerified() && blank($record->checked_in_at))
            ->requiresConfirmation()
            ->modalDescription('Gunakan ini jika QR code peserta tidak dapat dipindai.')
            ->action(function (Participant $record): void {
                $record->update(['checked_in_at' => now()]);

                Notification::make()
                    ->title('Check-in berhasil')
                    ->body("{$record->full_name} tercatat hadir.")
                    ->success()
                    ->send();
            });
    }
}
