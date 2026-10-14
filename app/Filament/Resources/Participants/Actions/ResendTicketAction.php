<?php

namespace App\Filament\Resources\Participants\Actions;

use App\Mail\ParticipantVerifiedMail;
use App\Models\Participant;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;

class ResendTicketAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'resendTicket';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Kirim Ulang Tiket')
            ->icon(Heroicon::OutlinedEnvelope)
            ->color('gray')
            ->visible(fn (Participant $record): bool => $record->isVerified())
            ->requiresConfirmation()
            ->modalDescription(fn (Participant $record): string => "Tiket QR akan dikirim ulang ke {$record->email}.")
            ->action(function (Participant $record): void {
                Mail::to($record->email)->send(new ParticipantVerifiedMail($record));

                Notification::make()
                    ->title('Tiket dikirim ulang')
                    ->success()
                    ->send();
            });
    }
}
