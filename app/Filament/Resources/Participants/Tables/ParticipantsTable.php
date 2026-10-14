<?php

namespace App\Filament\Resources\Participants\Tables;

use App\Enums\ParticipantOrigin;
use App\Enums\ParticipantStatus;
use App\Filament\Exports\ParticipantExporter;
use App\Filament\Resources\Participants\Actions\ApproveParticipantAction;
use App\Filament\Resources\Participants\Actions\CheckInParticipantAction;
use App\Filament\Resources\Participants\Actions\RejectParticipantAction;
use App\Filament\Resources\Participants\Actions\ResendTicketAction;
use App\Filament\Resources\Participants\Actions\ResetCheckInAction;
use App\Filament\Resources\Participants\Actions\ToggleGoodieBagAction;
use App\Models\Participant;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->description(fn (Participant $record): string => $record->email)
                    ->searchable(['full_name', 'email'])
                    ->sortable(),

                TextColumn::make('origin')
                    ->label('Asal')
                    ->badge()
                    ->sortable(),

                TextColumn::make('institution')
                    ->label('Universitas/Lembaga')
                    ->state(fn (Participant $record): string => $record->origin === ParticipantOrigin::Internal ? 'UKWMS' : $record->institution)
                    ->description(fn (Participant $record): string => $record->faculty)
                    ->searchable(['institution', 'faculty'])
                    ->toggleable(),

                TextColumn::make('student_number')
                    ->label('NRP/NIM')
                    ->description(fn (Participant $record): string => "Angkatan {$record->cohort_year}")
                    ->searchable(),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->url(fn (Participant $record): string => 'https://wa.me/'.preg_replace('/^0/', '62', $record->whatsapp))
                    ->openUrlInNewTab()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('food_allergy')
                    ->label('Alergi Makanan')
                    ->placeholder('Tidak ada')
                    ->badge()
                    ->color('danger')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('checked_in_at')
                    ->label('Check-in')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum hadir')
                    ->color('success')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('goodie_bag_received_at')
                    ->label('Goodie Bag')
                    ->boolean(fn ($state): bool => filled($state))
                    ->default(false)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ParticipantStatus::class),

                SelectFilter::make('origin')
                    ->label('Asal')
                    ->options(ParticipantOrigin::class),

                TernaryFilter::make('checked_in_at')
                    ->label('Kehadiran')
                    ->nullable()
                    ->trueLabel('Sudah hadir')
                    ->falseLabel('Belum hadir'),

                TernaryFilter::make('food_allergy')
                    ->label('Alergi Makanan')
                    ->trueLabel('Punya alergi')
                    ->falseLabel('Tidak ada alergi')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('food_allergy'),
                        false: fn (Builder $query) => $query->whereNull('food_allergy'),
                    ),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Ekspor')
                    ->exporter(ParticipantExporter::class),
            ])
            ->recordActions([
                ApproveParticipantAction::make(),
                RejectParticipantAction::make(),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    CheckInParticipantAction::make(),
                    ToggleGoodieBagAction::make(),
                    ResendTicketAction::make(),
                    ResetCheckInAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(ParticipantExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
