<?php

namespace App\Filament\Exports;

use App\Models\Invitation;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InvitationExporter extends Exporter
{
    protected static ?string $model = Invitation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
                ->label('Nama Undangan'),
            ExportColumn::make('unique_code')
                ->label('Kode Unik'),
            ExportColumn::make('registration_link')
                ->label('Link Pendaftaran'),
            ExportColumn::make('is_used')
                ->label('Sudah Digunakan')
                ->formatStateUsing(fn ($state): string => $state ? 'Ya' : 'Belum'),
            ExportColumn::make('created_at')
                ->label('Dibuat Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor undangan Anda telah selesai dan ' . number_format($export->successful_rows) . ' ' . str('baris')->plural($export->successful_rows) . ' telah diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diekspor.';
        }

        return $body;
    }
}
