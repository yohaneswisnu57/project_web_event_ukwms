<?php

namespace App\Filament\Resources\Registrations\Tables;

use App\Mail\RegistrationConfirmedMail;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['invitation']))
            ->columns([
                TextColumn::make('invitation.name')
                    ->label('Undangan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attendance_count')
                    ->label('Total Hadir')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label('Nomor HP')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('validated_at')
                    ->label('Status Validasi')
                    ->boolean(fn ($state) => filled($state))
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('checked_in_at')
                    ->label('Waktu Check-in')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum Hadir')
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

            ])
            ->filters([
                TernaryFilter::make('validated_at')
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
                Action::make('validate')
                    ->label('Validasi & Kirim Tiket')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->validated_at)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['validated_at' => now()]);

                        Mail::to($record->email)
                            ->cc('adminlpks@ukwms.ac.id')
                            ->send(new RegistrationConfirmedMail($record));

                        Notification::make()
                            ->title('Berhasil Validasi')
                            ->body('Data pendaftaran divalidasi dan email tiket telah dikirim.')
                            ->success()
                            ->send();
                    }),

                ActionGroup::make([
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
