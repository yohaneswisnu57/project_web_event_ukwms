<?php

namespace App\Filament\Resources\Registrations\Pages;

use App\Filament\Resources\Registrations\RegistrationResource;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('setRegistrationDeadline')
                ->label('Batas Waktu Registrasi')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->form([
                    DateTimePicker::make('closing_at')
                        ->label('Tutup Registrasi Pada')
                        ->default(Setting::where('key', 'registration_closing_at')->value('value'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    Setting::updateOrCreate(
                        ['key' => 'registration_closing_at'],
                        ['value' => $data['closing_at']]
                    );

                    Notification::make()
                        ->title('Batas waktu registrasi diperbarui')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
