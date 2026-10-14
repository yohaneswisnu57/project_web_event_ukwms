<?php

namespace App\Filament\Exports;

use App\Enums\ParticipantOrigin;
use App\Models\Participant;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ParticipantExporter extends Exporter
{
    protected static ?string $model = Participant::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('Kode Peserta')
                ->formatStateUsing(fn (int $state): string => 'RETINA-'.str_pad($state, 4, '0', STR_PAD_LEFT)),
            ExportColumn::make('full_name')
                ->label('Nama Lengkap'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('whatsapp')
                ->label('WhatsApp'),
            ExportColumn::make('origin')
                ->label('Asal')
                ->formatStateUsing(fn (ParticipantOrigin $state): string => $state->getLabel()),
            ExportColumn::make('institution')
                ->label('Universitas/Lembaga')
                ->state(fn (Participant $record): ?string => $record->origin === ParticipantOrigin::Internal ? 'Universitas Katolik Widya Mandala Surabaya' : $record->institution),
            ExportColumn::make('faculty')
                ->label('Fakultas'),
            ExportColumn::make('student_number')
                ->label('NRP/NIM'),
            ExportColumn::make('cohort_year')
                ->label('Angkatan'),
            ExportColumn::make('food_allergy')
                ->label('Alergi Makanan')
                ->state(fn (Participant $record): string => $record->food_allergy ?? 'Tidak ada'),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state): string => $state->getLabel()),
            ExportColumn::make('reviewed_at')
                ->label('Waktu Verifikasi'),
            ExportColumn::make('checked_in_at')
                ->label('Waktu Check-in'),
            ExportColumn::make('goodie_bag_received_at')
                ->label('Goodie Bag')
                ->state(fn (Participant $record): string => filled($record->goodie_bag_received_at) ? 'Sudah' : 'Belum'),
            ExportColumn::make('created_at')
                ->label('Tanggal Daftar'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor peserta telah selesai dan '.number_format($export->successful_rows).' baris telah diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' baris gagal diekspor.';
        }

        return $body;
    }
}
