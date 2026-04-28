<?php

namespace App\Filament\Pages;

use App\Models\Registration;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class ScanPresence extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Scan Kehadiran';
    protected static ?string $title = 'Scan QR Code Kehadiran';
    protected static ?string $slug = 'scan-presence';

    protected string $view = 'filament.pages.scan-presence';

    public ?Registration $scannedRegistration = null;
    public bool $isScanning = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(Registration::query()->whereNotNull('checked_in_at')->latest('checked_in_at'))
            ->columns([
                TextColumn::make('attendees.name')
                    ->label('Nama Peserta')
                    ->listWithLineBreaks(),
                TextColumn::make('invitation.name')
                    ->label('Instansi'),
                TextColumn::make('checked_in_at')
                    ->label('Waktu Hadir')
                    ->dateTime('H:i:s')
                    ->color('success'),
            ])
            ->paginated(false);
    }

    /**
     * Handle the check-in from the scanner via Livewire
     */
    public function checkIn(string $scannedContent): void
    {
        $uuid = null;

        // Case 1: Raw UUID (The new secure standard)
        if (preg_match('/^[a-f0-9-]{36}$/i', $scannedContent)) {
            $uuid = $scannedContent;
        } 
        // Case 2: URL containing UUID (Backward compatibility for old tickets)
        elseif (preg_match('/check-in\/([a-f0-9-]+)/i', $scannedContent, $matches)) {
            $uuid = $matches[1];
        }

        if ($uuid) {
            $registration = Registration::with(['invitation', 'attendees'])
                ->where('uuid', $uuid)
                ->first();

            if (!$registration) {
                Notification::make()
                    ->title('Error')
                    ->body('Data pendaftaran tidak ditemukan.')
                    ->danger()
                    ->send();
                return;
            }

            // Set to detail state
            $this->scannedRegistration = $registration;
            $this->isScanning = false;
        } else {
            Notification::make()
                ->title('QR Tidak Valid')
                ->body('Format QR Code tidak dikenali.')
                ->danger()
                ->send();
        }
    }

    public function confirmPresence(): void
    {
        if (!$this->scannedRegistration) return;

        if ($this->scannedRegistration->checked_in_at) {
            Notification::make()
                ->title('Gagal')
                ->body('Tamu ini sudah melakukan check-in sebelumnya.')
                ->warning()
                ->send();
        } else {
            $this->scannedRegistration->update(['checked_in_at' => now()]);

            Notification::make()
                ->title('Check-in Berhasil')
                ->body("Selamat datang, " . ($this->scannedRegistration->attendees->first()->name ?? $this->scannedRegistration->invitation->name))
                ->success()
                ->send();
        }

        $this->resetScan();
    }

    public function resetScan(): void
    {
        $this->scannedRegistration = null;
        $this->isScanning = true;
    }
}
