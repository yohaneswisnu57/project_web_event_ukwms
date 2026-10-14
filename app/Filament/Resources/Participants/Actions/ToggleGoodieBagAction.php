<?php

namespace App\Filament\Resources\Participants\Actions;

use App\Models\Participant;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class ToggleGoodieBagAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'toggleGoodieBag';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (Participant $record): string => filled($record->goodie_bag_received_at) ? 'Batalkan Goodie Bag' : 'Serahkan Goodie Bag')
            ->icon(Heroicon::OutlinedGift)
            ->color('info')
            ->visible(fn (Participant $record): bool => $record->isVerified())
            ->requiresConfirmation()
            ->action(function (Participant $record): void {
                $record->update([
                    'goodie_bag_received_at' => filled($record->goodie_bag_received_at) ? null : now(),
                ]);

                Notification::make()
                    ->title(filled($record->goodie_bag_received_at) ? 'Goodie bag tercatat diserahkan' : 'Penyerahan goodie bag dibatalkan')
                    ->success()
                    ->send();
            });
    }
}
