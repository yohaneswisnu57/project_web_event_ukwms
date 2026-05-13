<?php

namespace App\Filament\Resources\Registrations\Pages;

use App\Filament\Resources\Registrations\RegistrationResource;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Carbon;

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
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('closing_date')
                                ->label('Tanggal Tutup')
                                ->default(function () {
                                    $value = Setting::where('key', 'registration_closing_at')->value('value');

                                    return $value ? Carbon::parse($value)->format('Y-m-d') : now()->format('Y-m-d');
                                })
                                ->required(),
                            TimePicker::make('closing_time')
                                ->label('Jam Tutup')
                                ->default(function () {
                                    $value = Setting::where('key', 'registration_closing_at')->value('value');

                                    return $value ? Carbon::parse($value)->format('H:i:s') : '23:59:59';
                                })
                                ->required(),
                        ]),
                ])
                ->action(function (array $data) {
                    $combined = Carbon::parse($data['closing_date'].' '.$data['closing_time'])->format('Y-m-d H:i:s');

                    Setting::updateOrCreate(
                        ['key' => 'registration_closing_at'],
                        ['value' => $combined]
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
