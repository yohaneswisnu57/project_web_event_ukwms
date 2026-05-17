<?php

namespace App\Filament\Resources\Registrations\Tables;

use App\Mail\RegistrationConfirmedMail;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
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
            ->modifyQueryUsing(fn ($query) => $query->with(['invitation', 'attendees']))
            ->columns([
                TextColumn::make('invitation.name')
                    ->label('Undangan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attendees.name')
                    ->label('Nama Tamu')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable(),

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
                    Action::make('showQrCode')
                        ->label('Lihat QR Code')
                        ->icon('heroicon-s-qr-code')
                        ->color('gray')
                        ->visible(fn ($record) => filled($record->validated_at))
                        ->modalHeading('Tiket QR Code')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalWidth('sm')
                        ->infolist([
                            Section::make()
                                ->schema([
                                    ViewEntry::make('qr_code')
                                        ->view('filament.resources.registrations.qr-code-entry')
                                        ->hiddenLabel(),
                                    TextEntry::make('id')
                                        ->label('ID Registrasi')
                                        ->formatStateUsing(fn ($state) => 'REG-ID: '.str_pad($state, 6, '0', STR_PAD_LEFT))
                                        ->weight('black')
                                        ->alignCenter(),
                                    TextEntry::make('invitation.name')
                                        ->label('Nama Tamu')
                                        ->alignCenter(),
                                ])
                                ->columns(1),
                        ]),
                    Action::make('resetCheckIn')
                        ->label('Reset Check-in')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->visible(fn ($record) => filled($record->checked_in_at))
                        ->requiresConfirmation()
                        ->modalHeading('Reset Kehadiran?')
                        ->modalDescription('Apakah Anda yakin ingin menghapus status kehadiran tamu ini? Data waktu check-in akan dikosongkan.')
                        ->action(function ($record) {
                            $record->update(['checked_in_at' => null]);

                            Notification::make()
                                ->title('Berhasil Reset')
                                ->body('Status kehadiran tamu telah dikosongkan.')
                                ->success()
                                ->send();
                        }),
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
