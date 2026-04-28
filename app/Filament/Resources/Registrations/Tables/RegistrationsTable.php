<?php

namespace App\Filament\Resources\Registrations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['invitation']))
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('invitation.name')
                    ->label('Undangan')
                    ->searchable()
                    ->sortable(),
                
                \Filament\Tables\Columns\TextColumn::make('attendance_count')
                    ->label('Total Hadir')
                    ->sortable(),
                
                \Filament\Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                
                \Filament\Tables\Columns\TextColumn::make('phone')
                    ->label('Nomor HP'),
                
                \Filament\Tables\Columns\TextColumn::make('checked_in_at')
                    ->label('Waktu Check-in')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum Hadir')
                    ->color('success'),
                
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
