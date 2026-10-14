<?php

namespace App\Filament\Pages;

use App\Models\Participant;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ScanPresence extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'Scan Kehadiran';

    protected static ?string $title = 'Scan QR Code Kehadiran';

    protected static ?string $slug = 'scan-presence';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.scan-presence';

    public ?Participant $scannedParticipant = null;

    public bool $isScanning = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(Participant::query()->whereNotNull('checked_in_at')->latest('checked_in_at'))
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama Peserta'),
                TextColumn::make('code')
                    ->label('Kode'),
                TextColumn::make('checked_in_at')
                    ->label('Waktu Hadir')
                    ->dateTime('H:i:s')
                    ->color('success'),
            ])
            ->paginated(false);
    }

    /**
     * Handle the check-in from the scanner via Livewire.
     */
    public function checkIn(string $scannedContent): void
    {
        if (! preg_match('/^[a-f0-9-]{36}$/i', $scannedContent)) {
            Notification::make()
                ->title('QR Tidak Valid')
                ->body('Format QR Code tidak dikenali.')
                ->danger()
                ->send();

            return;
        }

        $participant = Participant::query()->where('uuid', $scannedContent)->first();

        if (! $participant) {
            Notification::make()
                ->title('Error')
                ->body('Data peserta tidak ditemukan.')
                ->danger()
                ->send();

            return;
        }

        if (! $participant->isVerified()) {
            Notification::make()
                ->title('Belum Terverifikasi')
                ->body("Pembayaran {$participant->full_name} belum diverifikasi panitia.")
                ->warning()
                ->send();

            return;
        }

        $this->scannedParticipant = $participant;
        $this->isScanning = false;
    }

    public function confirmPresence(): void
    {
        if (! $this->scannedParticipant) {
            return;
        }

        if ($this->scannedParticipant->checked_in_at) {
            Notification::make()
                ->title('Gagal')
                ->body('Peserta ini sudah melakukan check-in sebelumnya.')
                ->warning()
                ->send();
        } else {
            $this->scannedParticipant->update(['checked_in_at' => now()]);

            Notification::make()
                ->title('Check-in Berhasil')
                ->body("Selamat datang, {$this->scannedParticipant->full_name}")
                ->success()
                ->send();
        }

        $this->resetScan();
    }

    public function resetScan(): void
    {
        $this->scannedParticipant = null;
        $this->isScanning = true;
    }
}
