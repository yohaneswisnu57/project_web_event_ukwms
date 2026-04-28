<?php

namespace App\Filament\Resources\InvitationResource\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationRelationManager extends RelationManager
{
    protected static string $relationship = 'registration';

    protected static ?string $title = 'Data Pendaftaran';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('attendance_count')
                    ->label('Jumlah Hadir')
                    ->numeric()
                    ->required(),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),

                TextInput::make('phone')
                    ->label('Nomor Handphone')
                    ->tel()
                    ->required(),

                TextInput::make('position')
                    ->label('Jabatan')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attendance_count')
                    ->label('Jumlah Hadir'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Nomor HP'),

                Tables\Columns\TextColumn::make('position')
                    ->label('Jabatan'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    ViewAction::make(),
                ]),
            ])
            ->bulkActions([]);
    }
}
