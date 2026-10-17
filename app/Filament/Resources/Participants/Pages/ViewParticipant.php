<?php

namespace App\Filament\Resources\Participants\Pages;

use App\Filament\Resources\Participants\Actions\ApproveParticipantAction;
use App\Filament\Resources\Participants\Actions\CheckInParticipantAction;
use App\Filament\Resources\Participants\Actions\RejectParticipantAction;
use App\Filament\Resources\Participants\Actions\ResendTicketAction;
use App\Filament\Resources\Participants\Actions\ResetCheckInAction;
use App\Filament\Resources\Participants\ParticipantResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewParticipant extends ViewRecord
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ApproveParticipantAction::make(),
            RejectParticipantAction::make(),
            CheckInParticipantAction::make(),
            EditAction::make(),
            ActionGroup::make([
                ResendTicketAction::make(),
                ResetCheckInAction::make(),
            ]),
        ];
    }
}
