<?php

namespace App\Filament\Resources\Participants\Schemas;

use App\Models\Participant;
use Filament\Schemas\Components\Callout;

class DuplicateParticipantCallout
{
    public static function make(): Callout
    {
        return Callout::make('Kemungkinan pendaftaran ganda')
            ->description(fn (Participant $record): string => 'NRP/NIM atau nomor WhatsApp yang sama juga dipakai oleh: '.$record->possibleDuplicates()->pluck('full_name')->join(', ').'.')
            ->warning()
            ->visible(fn (Participant $record): bool => $record->possibleDuplicates()->isNotEmpty());
    }
}
