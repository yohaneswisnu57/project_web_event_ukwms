<?php

namespace App\Filament\Resources\Participants\Schemas;

use App\Enums\ParticipantOrigin;
use App\Enums\ParticipantStatus;
use App\Models\Participant;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ParticipantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Data Peserta')
                        ->schema([
                            TextEntry::make('full_name')
                                ->label('Nama Lengkap'),
                            TextEntry::make('email')
                                ->label('Email')
                                ->copyable(),
                            TextEntry::make('whatsapp')
                                ->label('WhatsApp')
                                ->url(fn (Participant $record): string => 'https://wa.me/'.preg_replace('/^0/', '62', $record->whatsapp))
                                ->openUrlInNewTab(),
                            TextEntry::make('food_allergy')
                                ->label('Alergi Makanan')
                                ->placeholder('Tidak ada'),
                        ])
                        ->columns(2),

                    Section::make('Data Akademik')
                        ->schema([
                            TextEntry::make('origin')
                                ->label('Asal')
                                ->badge(),
                            TextEntry::make('institution')
                                ->label('Universitas/Lembaga')
                                ->visible(fn (Participant $record): bool => $record->origin === ParticipantOrigin::External),
                            TextEntry::make('faculty')
                                ->label('Fakultas'),
                            TextEntry::make('student_number')
                                ->label('NRP/NIM')
                                ->copyable(),
                            TextEntry::make('cohort_year')
                                ->label('Angkatan'),
                        ])
                        ->columns(2),

                    Section::make('Pembayaran')
                        ->schema([
                            DuplicateParticipantCallout::make()
                                ->columnSpanFull(),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge(),
                            TextEntry::make('reviewer.name')
                                ->label('Diverifikasi oleh')
                                ->placeholder('-'),
                            TextEntry::make('reviewed_at')
                                ->label('Waktu Verifikasi')
                                ->dateTime('d M Y H:i')
                                ->placeholder('-'),
                            TextEntry::make('rejection_reason')
                                ->label('Alasan Penolakan')
                                ->visible(fn (Participant $record): bool => $record->status === ParticipantStatus::Rejected),
                            ImageEntry::make('payment_proof_path')
                                ->label('Bukti Pembayaran')
                                ->disk(Participant::PAYMENT_PROOF_DISK)
                                ->visibility('private')
                                ->imageHeight(480)
                                ->placeholder('Tidak ada bukti pembayaran.')
                                ->columnSpanFull(),
                        ])
                        ->columns(3),
                ])
                    ->columnSpan(2),

                Section::make('Tiket & Kehadiran')
                    ->schema([
                        ViewEntry::make('qr_code')
                            ->view('filament.resources.participants.qr-code-entry')
                            ->hiddenLabel()
                            ->visible(fn (Participant $record): bool => $record->isVerified()),
                        TextEntry::make('id')
                            ->label('Kode Peserta')
                            ->formatStateUsing(fn (int $state): string => 'RETINA-'.str_pad($state, 4, '0', STR_PAD_LEFT))
                            ->weight('bold'),
                        TextEntry::make('checked_in_at')
                            ->label('Waktu Check-in')
                            ->dateTime('d M Y H:i')
                            ->placeholder('Belum hadir'),
                        IconEntry::make('goodie_bag_received_at')
                            ->label('Goodie Bag Diterima')
                            ->boolean(fn ($state): bool => filled($state))
                            ->default(false),
                        TextEntry::make('created_at')
                            ->label('Tanggal Daftar')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
