<?php

namespace App\Filament\Imports;

use App\Models\Invitation;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class InvitationImporter extends Importer
{
    protected static ?string $model = Invitation::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nama Undangan')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Invitation
    {
        return new Invitation();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor undangan Anda telah selesai dan ' . number_format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' telah diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diimpor.';
        }

        return $body;
    }
}
