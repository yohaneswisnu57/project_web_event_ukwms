<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class UserManual extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Panduan Pengguna';

    protected static ?string $title = 'Panduan Pengguna';

    protected static ?string $slug = 'panduan-pengguna';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.user-manual';

    /**
     * Daftar topik panduan: id anchor => judul.
     *
     * @return array<string, string>
     */
    public function getTopics(): array
    {
        return [
            'pengenalan' => '1. Pengenalan & Login',
            'dashboard' => '2. Dashboard & Statistik',
            'alur-status' => '3. Alur Status Peserta',
            'daftar-peserta' => '4. Daftar Peserta',
            'verifikasi' => '5. Verifikasi Pembayaran',
            'aksi-lain' => '6. Aksi Peserta Lainnya',
            'scan' => '7. Scan Kehadiran',
            'ekspor' => '8. Ekspor Data',
            'pengaturan' => '9. Pengaturan Pendaftaran',
            'konten' => '10. Konten Landing Page',
            'faq' => '11. Pemecahan Masalah',
        ];
    }
}
