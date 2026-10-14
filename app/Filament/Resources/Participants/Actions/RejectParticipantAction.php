<?php

namespace App\Filament\Resources\Participants\Actions;

use App\Mail\ParticipantRejectedMail;
use App\Models\Participant;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;

class RejectParticipantAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reject';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Tolak')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->visible(fn (Participant $record): bool => $record->isPending())
            ->modalHeading('Tolak Pendaftaran')
            ->modalDescription('Peserta akan menerima email berisi alasan penolakan dan diminta mendaftar ulang dengan email yang berbeda.')
            ->modalSubmitActionLabel('Tolak & Kirim Email')
            ->schema([
                Textarea::make('rejection_reason')
                    ->label('Alasan Penolakan')
                    ->placeholder('Contoh: Nominal transfer tidak sesuai / bukti pembayaran tidak terbaca.')
                    ->required()
                    ->maxLength(500)
                    ->rows(3),
            ])
            ->action(function (Participant $record, array $data): void {
                $record->markAsRejected(auth()->user(), $data['rejection_reason']);

                Mail::to($record->email)->send(new ParticipantRejectedMail($record));

                Notification::make()
                    ->title('Pendaftaran ditolak')
                    ->body("Email pemberitahuan telah dikirim ke {$record->email}.")
                    ->success()
                    ->send();
            });
    }
}
