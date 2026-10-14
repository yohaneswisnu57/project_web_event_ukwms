<?php

/**
 * Konten landing page acara. Dipakai oleh view `welcome` dan `registration-form`.
 *
 * Blok dengan `is_placeholder` / `photo_is_placeholder` => true masih data
 * contoh (mock) dan diberi label "Contoh data" di halaman. Hapus flag-nya
 * (dan ganti isinya) setelah data final tersedia.
 */
return [
    'name' => 'WIMATES Leaders Talk',

    'topic' => 'We are the Impactful Leaders—Steering the Ship in Turbulent Times: Principles of Sustainable Leadership Amidst Global Instability.',

    'starts_at' => '2026-05-16 14:00:00',
    'ends_at' => '2026-05-16 17:00:00',
    'arrive_early_minutes' => 30,

    'venue' => [
        'name' => 'Auditorium B, Lantai 4',
        'institution' => 'Universitas Katolik Widya Mandala Surabaya',
        'address' => 'Jl. Dinoyo No. 42-44, Keputran, Kec. Tegalsari, Surabaya, Jawa Timur 60265',
        'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Universitas+Katolik+Widya+Mandala+Surabaya+Dinoyo',
        'photo' => '/images/event/venue-auditorium.jpg',
        'photo_credit' => 'Foto: set.sj / Unsplash',
        'photo_is_placeholder' => true,
    ],

    'speakers' => [
        [
            'role' => 'Speaker',
            'name' => 'Ignasius Jonan',
            'full_name' => 'Dr. (H.C). Drs. Ignasius Jonan, Ak., M.A., C.P.A., C.A., C.M.A., Asean C.P.A.',
            'title' => 'Ketua Umum Yayasan Widya Mandala Surabaya',
            'photo' => '/images/pak jonan.png',
        ],
        [
            'role' => 'Opening Remarks',
            'name' => 'Mgr. Agustinus Tri Budi Utomo',
            'full_name' => null,
            'title' => 'Uskup Keuskupan Surabaya',
            'photo' => '/images/uskup surabaya.png',
        ],
    ],

    'agenda' => [
        'is_placeholder' => true,
        'photo' => '/images/event/networking.jpg',
        'photo_credit' => 'Foto: Unsplash',
        'items' => [
            ['time' => '13.30', 'title' => 'Registrasi ulang', 'note' => 'Tunjukkan QR code undangan ke petugas di meja check-in.'],
            ['time' => '14.00', 'title' => 'Opening remarks', 'note' => 'Mgr. Agustinus Tri Budi Utomo, Uskup Keuskupan Surabaya.'],
            ['time' => '14.20', 'title' => 'Leaders Talk', 'note' => 'Ignasius Jonan membawakan tema acara.'],
            ['time' => '15.30', 'title' => 'Tanya jawab', 'note' => null],
            ['time' => '16.15', 'title' => 'Rehat & networking', 'note' => null],
            ['time' => '17.00', 'title' => 'Acara selesai', 'note' => null],
        ],
    ],
];
