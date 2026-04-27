<?php

namespace App\Filament\Resources\Registrations\Schemas;

use Filament\Schemas\Schema;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('invitation_id')
                    ->relationship('invitation', 'name')
                    ->label('Undangan')
                    ->required()
                    ->searchable(),
                
                \Filament\Forms\Components\TextInput::make('attendance_count')
                    ->label('Jumlah Hadir')
                    ->numeric()
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('email')
                    ->label('Email Pendaftar')
                    ->email()
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('phone')
                    ->label('Nomor HP')
                    ->tel()
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('position')
                    ->label('Jabatan')
                    ->required(),
            ]);
    }
}
