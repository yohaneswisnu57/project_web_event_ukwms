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
    | Gambar arsip di halaman depan: lukisan domain publik tentang dokter yang
    | mengajar di depan audiens; kreditnya tampil di footer. Bukan foto acara
    | atau ruangan RETINA sendiri.
    */
    'archive_images' => [
        'lecon-clinique' => [
            'file' => 'images/retina/lecon-clinique.webp',
            'alt' => 'Lukisan seorang dokter berdiri berbicara di depan ruangan penuh dokter yang mendengarkan',
            'caption' => 'André Brouillet, Pelajaran Klinis di Salpêtrière, 1887',
            'license' => 'Domain publik',
            'source' => 'https://commons.wikimedia.org/wiki/File:Une_le%C3%A7on_clinique_%C3%A0_la_Salp%C3%AAtri%C3%A8re.jpg',
        ],
    ],

];
