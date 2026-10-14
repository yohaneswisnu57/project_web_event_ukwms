<?php

namespace App\Filament\Resources\Participants\Schemas;

use App\Enums\ParticipantOrigin;
use App\Models\Participant;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Peserta')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->helperText('Nama ini akan dicetak di sertifikat.')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('whatsapp')
                            ->label('No. WhatsApp Aktif')
                            ->tel()
                            ->required()
                            ->regex('/^(\+62|62|0)8[0-9]{7,12}$/')
                            ->maxLength(20),
                        TextInput::make('food_allergy')
                            ->label('Alergi Makanan')
                            ->placeholder('Kosongkan jika tidak ada')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Data Akademik')
                    ->schema([
                        Radio::make('origin')
                            ->label('Asal')
                            ->options(ParticipantOrigin::class)
                            ->default(ParticipantOrigin::Internal)
                            ->inline()
                            ->required()
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('institution')
                            ->label('Universitas/Lembaga')
                            ->required(fn (Get $get): bool => self::isExternal($get))
                            ->visible(fn (Get $get): bool => self::isExternal($get))
                            ->maxLength(255),
                        TextInput::make('faculty')
                            ->label('Fakultas')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('student_number')
                            ->label(fn (Get $get): string => self::isExternal($get) ? 'NIM/NRP' : 'NRP')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('cohort_year')
                            ->label('Angkatan')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue((int) now()->format('Y'))
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Pembayaran')
                    ->schema([
                        FileUpload::make('payment_proof_path')
                            ->label('Bukti Pembayaran')
                            ->disk(Participant::PAYMENT_PROOF_DISK)
                            ->directory(Participant::PAYMENT_PROOF_DIRECTORY)
                            ->visibility('private')
                            ->image()
                            ->maxSize(4096)
                            ->openable(),
                    ]),
            ])
            ->columns(1);
    }

    protected static function isExternal(Get $get): bool
    {
        $origin = $get('origin');

        if (! $origin instanceof ParticipantOrigin) {
            $origin = ParticipantOrigin::tryFrom((string) $origin);
        }

        return $origin === ParticipantOrigin::External;
    }
}
