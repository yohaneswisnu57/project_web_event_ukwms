<?php

/*
|--------------------------------------------------------------------------
| RETINA 2026 — konten halaman publik
|--------------------------------------------------------------------------
|
| Fakta acara yang tampil di landing page dan formulir pendaftaran. Kuota dan
| batas waktu pendaftaran tidak di sini: keduanya diatur panitia dari
| backoffice (lihat Participant::SETTING_QUOTA dan SETTING_CLOSING_AT).
| Nilai null membuat bagian terkait disembunyikan dari halaman.
|
| Pembicara (jabatan, bio, foto), materi, jam selesai, fasilitas, dan
| narahubung hanyalah nilai awal: admin bisa mengubahnya di halaman
| "Konten Landing Page", dan isian admin selalu didahulukan
| (lihat App\Http\Middleware\ApplyLandingContent). Foto disimpan di disk public.
|
*/

return [

    'name' => 'RETINA 2026',

    'tagline' => 'Redaction Training in Art',

    'theme' => 'Healthcare Public Speaking & Communication Training',

    'slogan' => 'Speak up, stand out, and make an impact.',

    'starts_at' => '2026-10-31 09:00:00',

    'ends_at' => null,

    'venue' => [
        'room' => 'Ruang Teater Barat',
        'building' => 'Tower Barat Lt. 1',
        'campus' => 'Universitas Katolik Widya Mandala Surabaya',
        'address' => 'Jl. Kalisari Selatan 1, Surabaya',
        'maps_query' => 'Universitas Katolik Widya Mandala Surabaya, Jl. Kalisari Selatan No.1, Surabaya',
    ],

    'speaker' => [
        'name' => 'Henry Christian Tjandra',
        'role' => null,
        'bio' => null,
        'photo' => null,
        // Dipakai selama admin belum mengunggah foto: potongan dari banner resmi.
        // Ganti atau kosongkan jika pembicaranya berubah.
        'photo_default' => 'images/retina/speaker-henry.webp',
    ],

    'sessions' => [
        [
            'title' => 'Menyusun pesan',
            'description' => 'Merangkai informasi medis menjadi pesan yang runtut dan mudah dipahami awam.',
        ],
        [
            'title' => 'Suara & sikap tubuh',
            'description' => 'Mengelola intonasi, jeda, dan gestur agar tampil tenang dan meyakinkan.',
        ],
        [
            'title' => 'Komunikasi empatik',
            'description' => 'Menyampaikan kabar sulit dengan jelas, tanpa kehilangan empati pada pendengar.',
        ],
    ],

    'perks' => 'Termasuk sertifikat & goodie bag',

    'bank' => [
        'name' => 'Bank Mandiri',
        'account_number' => '1410021783519',
        'account_holder' => 'Yayasan Widya Mandala Surabaya',
    ],

    'contact' => [
        'name' => null,
        'whatsapp' => null,
    ],

    /*
    | Link undangan grup WhatsApp peserta, hanya dikirim di email tiket.
    | Disimpan di .env karena repositori ini publik; kosongkan untuk
    | menyembunyikan tombolnya.
    */
    'whatsapp_group_url' => env('RETINA_WHATSAPP_GROUP_URL'),

    /*
    | Banner resmi acara: dipasang utuh sebagai hero halaman depan dan dipakai
    | sebagai gambar pratinjau saat tautan dibagikan. Ganti file-nya untuk
    | memperbarui banner; teks alternatif harus ikut menyebutkan isinya.
    */
    'banner' => [
        'file' => 'images/retina/banner-retina-2026.webp',
        'alt' => 'Poster RETINA 2026: Sabtu, 31 Oktober, 09.00 WIB, Ruang Teater Barat UKWMS Kampus Pakuwon City, guest speaker Henry Christian Tjandra, didukung Bank Mandiri dan EGC',
    ],

    /*
    | Foto gedung Kampus Pakuwon City, tempat acara berlangsung, di bagian
    | lokasi. Foto resmi dari situs UKWMS; sumbernya dicantumkan di footer.
    */
    'venue_image' => [
        'file' => 'images/retina/kampus-pakuwon-city.webp',
        'alt' => 'Gedung Kampus Pakuwon City Universitas Katolik Widya Mandala Surabaya dengan papan nama universitas dan taman di depannya',
        'caption' => 'Kampus Pakuwon City, Universitas Katolik Widya Mandala Surabaya',
        'credit' => 'ukwms.ac.id',
        'source' => 'https://ukwms.ac.id/ukwms-terapkan-pembelajaran-daring-1-6-september-2025/',
    ],

];
