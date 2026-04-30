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
                
                \Filament\Tables\Columns\IconColumn::make('validated_at')
                    ->label('Status Validasi')
                    ->boolean(fn ($state) => filled($state))
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

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
                \Filament\Tables\Filters\TernaryFilter::make('validated_at')
                    ->label('Hanya Terpilih')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Validasi')
                    ->falseLabel('Belum Validasi')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('validated_at'),
                        false: fn ($query) => $query->whereNull('validated_at'),
                    ),
            ])
            ->actions([
                \Filament\Actions\Action::make('validate')
                    ->label('Validasi & Kirim Tiket')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->validated_at)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['validated_at' => now()]);
                        
                        \Illuminate\Support\Facades\Mail::to($record->email)
                            ->cc('adminlpks@ukwms.ac.id')
                            ->send(new \App\Mail\RegistrationConfirmedMail($record));

                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil Validasi')
                            ->body('Data pendaftaran divalidasi dan email tiket telah dikirim.')
                            ->success()
                            ->send();
                    }),

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
