<?php

namespace App\Filament\Resources\Participants\Actions;

use App\Filament\Resources\Participants\Schemas\DuplicateParticipantCallout;
use App\Mail\ParticipantVerifiedMail;
use App\Models\Participant;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Number;

class ApproveParticipantAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'approve';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Terima')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->color('success')
            ->visible(fn (Participant $record): bool => $record->isPending())
            ->modalHeading('Verifikasi Pembayaran')
            ->modalDescription(fn (Participant $record): string => 'Pastikan nominal transfer '.Number::currency(Participant::REGISTRATION_FEE, 'IDR', 'id', 0).' ke rekening Bank Mandiri 1410021783519 a.n. Yayasan Widya Mandala Surabaya.')
            ->modalSubmitActionLabel('Terima & Kirim Tiket')
            ->modalWidth('2xl')
            ->schema([
                DuplicateParticipantCallout::make(),
                Grid::make(2)
                    ->schema([
                        TextEntry::make('full_name')
                            ->label('Nama Lengkap'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('origin')
                            ->label('Asal')
                            ->badge(),
                        TextEntry::make('student_number')
                            ->label('NRP/NIM'),
                    ]),
                ImageEntry::make('payment_proof_path')
                    ->label('Bukti Pembayaran')
                    ->disk(Participant::PAYMENT_PROOF_DISK)
                    ->visibility('private')
                    ->imageHeight(420)
                    ->placeholder('Tidak ada bukti pembayaran.'),
            ])
            ->action(function (Participant $record): void {
                $record->markAsVerified(auth()->user());

                Mail::to($record->email)->send(new ParticipantVerifiedMail($record));

                Notification::make()
                    ->title('Pembayaran diterima')
                    ->body("Tiket QR telah dikirim ke {$record->email}.")
                    ->success()
                    ->send();
            });
    }
}
