<?php

namespace App\Filament\Resources\Participants\Pages;

use App\Filament\Resources\Participants\ParticipantResource;
use App\Models\Participant;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;

class ListParticipants extends ListRecords
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('registrationSettings')
                ->label('Pengaturan Pendaftaran')
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->color('info')
                ->modalDescription(fn (): string => 'Saat ini terisi '.Participant::query()->occupyingQuota()->count().' pendaftar (menunggu verifikasi + terverifikasi). Pendaftar yang ditolak tidak dihitung.')
                ->fillForm(function (): array {
                    $closesAt = Participant::registrationClosesAt();

                    return [
                        'closing_date' => $closesAt?->format('Y-m-d'),
                        'closing_time' => $closesAt?->format('H:i:s') ?? '23:59:00',
                        'quota' => Participant::quota(),
                    ];
                })
                ->schema([
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('closing_date')
                                ->label('Tanggal Tutup')
                                ->helperText('Kosongkan jika tidak ada batas waktu.'),
                            TimePicker::make('closing_time')
                                ->label('Jam Tutup')
                                ->required(fn (Get $get): bool => filled($get('closing_date'))),
                        ]),
                    TextInput::make('quota')
                        ->label('Kuota Peserta')
                        ->helperText('Kosongkan jika kuota tidak dibatasi.')
                        ->numeric()
                        ->minValue(1)
                        ->integer(),
                ])
                ->action(function (array $data): void {
                    $closesAt = filled($data['closing_date'])
                        ? Carbon::parse($data['closing_date'].' '.$data['closing_time'])->format('Y-m-d H:i:s')
                        : null;

                    Setting::putValue(Participant::SETTING_CLOSING_AT, $closesAt);
                    Setting::putValue(Participant::SETTING_QUOTA, filled($data['quota']) ? (string) $data['quota'] : null);

                    Notification::make()
                        ->title('Pengaturan pendaftaran diperbarui')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
