<x-filament-panels::page>
    <style>
        .manual-body { font-size: 0.925rem; line-height: 1.7; }
        .manual-body p { margin-bottom: 0.75rem; }
        .manual-body ol, .manual-body ul { margin: 0 0 0.75rem 1.25rem; }
        .manual-body ol { list-style: decimal; }
        .manual-body ul { list-style: disc; }
        .manual-body li { margin-bottom: 0.25rem; }
        .manual-body b { font-weight: 700; }
        .manual-tip { border-left: 4px solid #FAC43A; background: rgba(250, 196, 58, 0.12); padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 0.75rem; }
        .manual-toc a { display: block; padding: 0.25rem 0; text-decoration: underline; text-underline-offset: 3px; }
        .manual-table { width: 100%; border-collapse: collapse; margin-bottom: 0.75rem; }
        .manual-table th, .manual-table td { border: 1px solid rgba(148, 163, 184, 0.5); padding: 0.5rem 0.75rem; text-align: left; vertical-align: top; }
        .manual-table th { background: rgba(250, 196, 58, 0.18); }
    </style>

    <div class="manual-body" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <x-filament::section>
            <x-slot name="heading">Daftar Isi</x-slot>
            <nav class="manual-toc">
                @foreach ($this->getTopics() as $id => $title)
                    <a href="#{{ $id }}">{{ $title }}</a>
                @endforeach
            </nav>
        </x-filament::section>

        <x-filament::section id="pengenalan">
            <x-slot name="heading">{{ $this->getTopics()['pengenalan'] }}</x-slot>
            <p>Panel admin ini dipakai panitia <b>RETINA 2026</b> untuk mengelola pendaftaran, memverifikasi pembayaran, dan mencatat kehadiran peserta. Panduan ini hanya dapat dibuka setelah login ke panel admin.</p>
            <ul>
                <li><b>Acara:</b> 31 Oktober 2026, pukul 09:00, Ruang Teater Barat, Tower Barat Lt. 1, UKWMS Pakuwon City.</li>
                <li><b>Biaya pendaftaran:</b> Rp 30.000 per peserta (Bank Mandiri 1410021783519 a.n. Yayasan Widya Mandala Surabaya).</li>
                <li><b>Login:</b> buka alamat <code>/admin</code>, masukkan email dan kata sandi akun panitia.</li>
                <li><b>Ubah profil / kata sandi:</b> klik nama Anda di pojok kanan atas, lalu pilih <b>Profil</b>.</li>
            </ul>
            <p>Menu di sisi kiri: <b>Peserta RETINA</b>, <b>Scan Kehadiran</b>, <b>Konten Landing Page</b>, dan <b>Panduan Pengguna</b>.</p>
        </x-filament::section>

        <x-filament::section id="dashboard">
            <x-slot name="heading">{{ $this->getTopics()['dashboard'] }}</x-slot>
            <p>Dashboard menampilkan empat angka yang diperbarui otomatis setiap 10 detik:</p>
            <ul>
                <li><b>Pendaftar</b>: jumlah pendaftar dibanding kuota.</li>
                <li><b>Menunggu Verifikasi</b>: pendaftar yang bukti pembayarannya belum diperiksa.</li>
                <li><b>Terverifikasi</b>: peserta yang sudah disetujui (beserta total pemasukan Rp 30.000 per peserta).</li>
                <li><b>Hadir</b>: peserta yang sudah check-in, beserta jumlah peserta dengan alergi makanan.</li>
            </ul>
        </x-filament::section>

        <x-filament::section id="alur-status">
            <x-slot name="heading">{{ $this->getTopics()['alur-status'] }}</x-slot>
            <p>Peserta mendaftar sendiri lewat halaman <code>/daftar</code> dan mengunggah bukti pembayaran. Status peserta:</p>
            <table class="manual-table">
                <thead><tr><th>Status</th><th>Arti</th></tr></thead>
                <tbody>
                    <tr><td><b>Menunggu Verifikasi</b></td><td>Status awal setelah mendaftar. Menunggu pengecekan bukti pembayaran oleh panitia.</td></tr>
                    <tr><td><b>Terverifikasi</b></td><td>Pembayaran disetujui. E-tiket berisi QR code otomatis dikirim ke email peserta.</td></tr>
                    <tr><td><b>Ditolak</b></td><td>Pembayaran/data tidak valid. Email penolakan berisi alasan dikirim; peserta dapat mendaftar ulang dengan email berbeda.</td></tr>
                </tbody>
            </table>
            <p>Kode peserta berformat <code>RETINA-0001</code>, dan menjadi acuan saat check-in.</p>
        </x-filament::section>

        <x-filament::section id="daftar-peserta">
            <x-slot name="heading">{{ $this->getTopics()['daftar-peserta'] }}</x-slot>
            <p>Buka menu <b>Peserta RETINA</b>. Angka pada menu menunjukkan jumlah peserta yang menunggu verifikasi.</p>
            <ul>
                <li><b>Pencarian:</b> gunakan kolom cari di atas tabel (nama, email, kode, dll.).</li>
                <li><b>Filter:</b> Status, Asal (Internal UKWMS / Eksternal), Kehadiran, dan Alergi Makanan.</li>
                <li><b>Kolom:</b> Nama Lengkap, Asal, Universitas/Lembaga, NRP/NIM, WhatsApp, Alergi Makanan, Status, Check-in, Tanggal Daftar.</li>
                <li>Klik menu titik tiga pada baris untuk <b>Lihat</b>, <b>Ubah</b>, dan aksi lainnya.</li>
            </ul>
        </x-filament::section>

        <x-filament::section id="verifikasi">
            <x-slot name="heading">{{ $this->getTopics()['verifikasi'] }}</x-slot>
            <p><b>Menyetujui pendaftaran:</b></p>
            <ol>
                <li>Filter status <b>Menunggu Verifikasi</b>.</li>
                <li>Klik <b>Terima</b> pada baris peserta. Jendela <b>Verifikasi Pembayaran</b> menampilkan data peserta dan foto bukti pembayaran.</li>
                <li>Pastikan nominal Rp 30.000, tujuan rekening, dan tanggal transfer sesuai, lalu konfirmasi.</li>
                <li>Status berubah menjadi <b>Terverifikasi</b> dan e-tiket QR dikirim ke email peserta.</li>
            </ol>
            <p><b>Menolak pendaftaran:</b></p>
            <ol>
                <li>Klik <b>Tolak</b> pada baris peserta.</li>
                <li>Isi <b>Alasan Penolakan</b> dengan jelas (mis. nominal tidak sesuai, bukti tidak terbaca).</li>
                <li>Konfirmasi. Peserta menerima email penolakan berisi alasan tersebut.</li>
            </ol>
            <div class="manual-tip">Peserta yang ditolak tidak dihitung dalam kuota.</div>
        </x-filament::section>

        <x-filament::section id="aksi-lain">
            <x-slot name="heading">{{ $this->getTopics()['aksi-lain'] }}</x-slot>
            <table class="manual-table">
                <thead><tr><th>Aksi</th><th>Kegunaan</th></tr></thead>
                <tbody>
                    <tr><td><b>Kirim Ulang Tiket</b></td><td>Mengirim ulang e-tiket jika peserta tidak menerima atau kehilangan email.</td></tr>
                    <tr><td><b>Tandai Hadir</b></td><td>Check-in manual bila QR code tidak bisa dipindai (mis. layar rusak).</td></tr>
                    <tr><td><b>Reset Check-in</b></td><td>Menghapus catatan kehadiran jika salah tandai. Akan diminta konfirmasi "Reset Kehadiran?".</td></tr>
                    <tr><td><b>Hapus (massal)</b></td><td>Centang beberapa baris, lalu pilih aksi hapus. Data yang dihapus tidak dapat dikembalikan; gunakan dengan hati-hati.</td></tr>
                </tbody>
            </table>
        </x-filament::section>

        <x-filament::section id="scan">
            <x-slot name="heading">{{ $this->getTopics()['scan'] }}</x-slot>
            <ol>
                <li>Buka menu <b>Scan Kehadiran</b> dan izinkan akses kamera pada browser.</li>
                <li>Arahkan kamera ke QR code pada e-tiket peserta.</li>
                <li>Kartu detail peserta muncul. Cocokkan nama, lalu tekan tombol konfirmasi untuk mencatat kehadiran.</li>
                <li>Peserta yang sudah check-in tampil pada tabel di halaman yang sama.</li>
            </ol>
            <p>Pesan yang mungkin muncul:</p>
            <ul>
                <li><b>QR tidak valid</b>: kode bukan dari tiket RETINA.</li>
                <li><b>Peserta tidak ditemukan</b>: data tidak ada di sistem.</li>
                <li><b>Belum terverifikasi</b> (peringatan): peserta belum/tidak disetujui. Periksa statusnya di daftar peserta.</li>
            </ul>
            <div class="manual-tip">Gunakan perangkat dengan kamera baik dan koneksi internet stabil di hari acara. Cadangan: <b>Tandai Hadir</b> manual di daftar peserta.</div>
        </x-filament::section>

        <x-filament::section id="ekspor">
            <x-slot name="heading">{{ $this->getTopics()['ekspor'] }}</x-slot>
            <ul>
                <li>Tombol <b>Ekspor</b> di atas tabel mengunduh data peserta.</li>
                <li>Untuk mengekspor sebagian saja, centang baris peserta lalu gunakan aksi ekspor massal.</li>
                <li>Terapkan filter lebih dulu bila hanya butuh kelompok tertentu (mis. peserta dengan alergi makanan untuk keperluan konsumsi).</li>
            </ul>
        </x-filament::section>

        <x-filament::section id="pengaturan">
            <x-slot name="heading">{{ $this->getTopics()['pengaturan'] }}</x-slot>
            <p>Di halaman Peserta RETINA, klik <b>Pengaturan Pendaftaran</b> (kanan atas):</p>
            <ul>
                <li><b>Tanggal Tutup</b> dan <b>Jam Tutup</b>: batas akhir pendaftaran.</li>
                <li><b>Kuota Peserta</b>: jumlah maksimum pendaftar.</li>
                <li>Mengosongkan kolom berarti <b>tanpa batas</b>.</li>
            </ul>
            <p>Pendaftaran otomatis ditutup bila melewati waktu tutup atau kuota penuh.</p>
        </x-filament::section>

        <x-filament::section id="konten">
            <x-slot name="heading">{{ $this->getTopics()['konten'] }}</x-slot>
            <p>Menu <b>Konten Landing Page</b> mengatur isi halaman publik: pembicara, sesi, fasilitas, kontak, dan foto. Isian di sini menggantikan nilai bawaan. Simpan perubahan, lalu periksa halaman depan untuk memastikan tampilannya benar.</p>
        </x-filament::section>

        <x-filament::section id="faq">
            <x-slot name="heading">{{ $this->getTopics()['faq'] }}</x-slot>
            <table class="manual-table">
                <thead><tr><th>Masalah</th><th>Solusi</th></tr></thead>
                <tbody>
                    <tr><td>Peserta tidak menerima tiket</td><td>Cek email di data peserta, lalu gunakan <b>Kirim Ulang Tiket</b>. Minta peserta memeriksa folder spam.</td></tr>
                    <tr><td>QR tidak bisa dipindai</td><td>Cari peserta di daftar, gunakan <b>Tandai Hadir</b>.</td></tr>
                    <tr><td>Salah menandai kehadiran</td><td>Gunakan <b>Reset Check-in</b>.</td></tr>
                    <tr><td>Peserta tidak bisa mendaftar</td><td>Periksa <b>Pengaturan Pendaftaran</b> (kuota penuh atau melewati waktu tutup) dan pastikan email belum terdaftar.</td></tr>
                    <tr><td>Kamera scan tidak muncul</td><td>Izinkan akses kamera pada browser, muat ulang halaman, atau gunakan browser lain.</td></tr>
                </tbody>
            </table>
        </x-filament::section>
    </div>
</x-filament-panels::page>
