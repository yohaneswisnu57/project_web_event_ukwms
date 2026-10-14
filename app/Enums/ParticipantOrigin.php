<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ParticipantOrigin: string implements HasColor, HasLabel
{
    case Internal = 'internal';
    case External = 'external';

    public function getLabel(): string
    {
        return match ($this) {
            self::Internal => 'UKWMS',
            self::External => 'Eksternal',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Internal => 'info',
            self::External => 'gray',
        };
    }
}
