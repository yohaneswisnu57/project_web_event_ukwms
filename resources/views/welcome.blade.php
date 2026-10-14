@extends('layouts.retina')

@use('App\Models\Participant')
@use('Illuminate\Support\Number')
@use('Illuminate\Support\Str')
@use('Illuminate\Support\Facades\Storage')

@php
    $fee = Number::currency(Participant::REGISTRATION_FEE, 'IDR', 'id', 0);
    $dateLabel = $startsAt->locale('id')->translatedFormat('l, j F Y');
    $timeLabel = $startsAt->format('H.i').($endsAt ? '–'.$endsAt->format('H.i') : '').' WIB';
    $speaker = config('retina.speaker');
    $images = config('retina.archive_images');
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query='.urlencode(config('retina.venue.maps_query'));
    $whatsappUrl = filled(config('retina.contact.whatsapp'))
        ? 'https://wa.me/'.preg_replace('/^0/', '62', preg_replace('/\D/', '', config('retina.contact.whatsapp')))
        : null;

    /** Panel-panel yang lewat di bawah judul RETINA yang terpasang. */
    $panels = [
        ['image' => 'anatomy-lesson-tulp', 'text' => 'Sejak 1632, dokter mengajar di depan penonton. Yang diingat bukan cuma isinya, tapi jeda, suara, dan sikap tubuhnya.'],
        ['image' => 'agnew-clinic', 'text' => $startsAt->locale('id')->translatedFormat('j F').', panggungnya '.config('retina.venue.room').'. Yang berdiri di depan: kamu.'],
    ];
@endphp

@section('content')
    {{-- ============ PANGGUNG: judul terpasang, lukisan lewat di bawahnya ============ --}}
    <header class="r-stage">
        @include('partials.retina.nav', ['showCta' => $isRegistrationOpen])

        <div class="r-stage__pin">
            <h1 class="r-stage__title">RETINA<span class="r-sr-only"> {{ $startsAt->year }}</span></h1>
        </div>

        <div class="r-stage__panels">
            <section id="konten" class="r-panel r-panel--hero" aria-label="Ringkasan acara">
                <figure class="r-panel__figure">
                    <img class="r-drift" src="{{ asset($images['lecon-clinique']['file']) }}" alt="{{ $images['lecon-clinique']['alt'] }}" width="960" height="1200" fetchpriority="high">
                </figure>

                <div class="r-panel__left">
                    <p class="r-panel__theme r-rise r-d1">{{ config('retina.theme') }}</p>
                    <p class="r-panel__when r-rise r-d2">{{ $dateLabel }}<br>{{ config('retina.venue.room') }}, UKWMS</p>
                    <p class="r-panel__text r-rise r-d3">Satu hari berlatih bicara di depan publik untuk insan kesehatan, bersama {{ $speaker['name'] }}.</p>
                </div>

                <div class="r-panel__right r-rise r-d4">
                    @if ($isRegistrationOpen)
                        <a class="r-btn r-btn--gold r-btn--lg" href="{{ route('registration') }}">Amankan kursimu <span class="r-arrow" aria-hidden="true">→</span></a>
                        <p class="r-panel__status">
                            {{ $fee }} ·
                            @if ($remainingQuota !== null)
                                Pendaftaran dibuka · Sisa {{ $remainingQuota }} kursi
                            @elseif ($registrationClosesAt)
                                Pendaftaran dibuka hingga {{ $registrationClosesAt->locale('id')->translatedFormat('j F') }}
                            @else
                                Pendaftaran dibuka · Kuota terbatas
                            @endif
                        </p>
                    @else
                        <p class="r-panel__closed">Pendaftaran ditutup</p>
                    @endif
                    <p class="r-panel__caption">{{ $images['lecon-clinique']['caption'] }}</p>
                </div>
            </section>

            @foreach ($panels as $panel)
                <section class="r-panel r-panel--{{ $loop->iteration }}" aria-label="{{ $images[$panel['image']]['caption'] }}">
                    <figure class="r-panel__figure">
                        <img src="{{ asset($images[$panel['image']]['file']) }}" alt="{{ $images[$panel['image']]['alt'] }}" width="960" height="1200" loading="lazy">
                    </figure>
                    <p class="r-panel__left r-panel__statement" data-reveal>{{ $panel['text'] }}</p>
                    <p class="r-panel__right r-panel__caption" data-reveal style="--reveal-delay: 90ms">{{ $images[$panel['image']]['caption'] }}</p>
                </section>
            @endforeach
        </div>
    </header>

    <main>
        {{-- ============ MATERI & PEMBICARA ============ --}}
        <section id="materi" class="r-programme" aria-labelledby="materi-title">
            <div class="r-container">
                <h2 id="materi-title" class="r-h2" data-reveal>{{ Str::ucfirst(Number::spell(count(config('retina.sessions')), 'id')) }} babak, satu panggung.</h2>
                <p class="r-programme__meta" data-reveal style="--reveal-delay: 70ms">{{ $dateLabel }} · {{ $timeLabel }} · {{ config('retina.venue.room') }}</p>

                <ol class="r-lineup">
                    @foreach (config('retina.sessions') as $session)
                        <li class="r-lineup__row" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms">
                            <h3 class="r-lineup__title">{{ $session['title'] }}</h3>
                            <p class="r-lineup__text">{{ $session['description'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div id="pembicara" class="r-container r-speaker {{ filled($speaker['photo']) ? 'r-speaker--photo' : '' }}">
                @if (filled($speaker['photo']))
                    <figure class="r-speaker__media" data-reveal>
                        <img src="{{ Storage::disk('public')->url($speaker['photo']) }}" alt="Foto {{ $speaker['name'] }}" width="800" height="1000" loading="lazy">
                    </figure>
                @endif

                <div class="r-speaker__body">
                    <h2 class="r-speaker__name" data-reveal>Bersama {{ $speaker['name'] }}</h2>
                    @if (filled($speaker['role']))
                        <p class="r-speaker__role" data-reveal style="--reveal-delay: 70ms">{{ $speaker['role'] }}</p>
                    @endif
                    @if (filled($speaker['bio']))
                        <p class="r-speaker__bio" data-reveal style="--reveal-delay: 140ms">{{ $speaker['bio'] }}</p>
                    @endif
                    <blockquote class="r-speaker__quote" data-reveal style="--reveal-delay: 210ms">
                        <p>{{ config('retina.slogan') }}</p>
                    </blockquote>
                </div>
            </div>
        </section>

        {{-- ============ CARA DAFTAR: satu kata kerja per layar ============ --}}
        <section id="cara-daftar" class="r-verbs" aria-labelledby="cara-daftar-title">
            <h2 id="cara-daftar-title" class="r-sr-only">Cara daftar</h2>

            <ol class="r-verbs__list">
                <li class="r-verb r-verb--paper">
                    <h3 class="r-verb__word">Isi</h3>
                    <div class="r-verb__body">
                        <p class="r-verb__text" data-reveal>Isi formulir: nama, email aktif, nomor WhatsApp, dan data akademik. Terbuka untuk mahasiswa UKWMS dan kampus lain.</p>
                    </div>
                </li>
                <li class="r-verb r-verb--gold">
                    <h3 class="r-verb__word">Bayar</h3>
                    <div class="r-verb__body">
                        <p class="r-verb__text" data-reveal>Transfer {{ $fee }} ke rekening panitia, lalu unggah foto bukti transfernya di formulir.</p>
                        <div data-reveal style="--reveal-delay: 90ms">
                            @include('partials.retina.bank')
                        </div>
                    </div>
                </li>
                <li class="r-verb r-verb--navy">
                    <h3 class="r-verb__word">Tunggu</h3>
                    <div class="r-verb__body">
                        <p class="r-verb__text" data-reveal>Panitia mengecek pembayaranmu. Hasilnya dikirim lewat email, jadi cek juga folder spam.</p>
                    </div>
                </li>
                <li class="r-verb r-verb--tint">
                    <h3 class="r-verb__word">Masuk</h3>
                    <div class="r-verb__body">
                        <p class="r-verb__text" data-reveal>Tunjukkan tiket QR dari email saat registrasi ulang di depan {{ config('retina.venue.room') }}.</p>

                        <div class="r-try" data-reveal style="--reveal-delay: 90ms">
                            <label class="r-label" for="ticket-name">Tulis namamu, lihat tiketmu</label>
                            <input id="ticket-name" class="r-input" type="text" maxlength="60" autocomplete="name" placeholder="Nama lengkap" data-ticket-name>
                            @include('partials.retina.ticket', ['startsAt' => $startsAt, 'showHolder' => true])
                            @if ($isRegistrationOpen)
                                <a class="r-btn r-btn--navy" href="{{ route('registration') }}" data-ticket-link>Daftar dengan nama ini <span class="r-arrow" aria-hidden="true">→</span></a>
                            @endif
                        </div>
                    </div>
                </li>
            </ol>
        </section>

        {{-- ============ LOKASI: nama ruangan sebagai huruf besar ============ --}}
        <section id="lokasi" class="r-venue" aria-labelledby="lokasi-title">
            <div class="r-container r-venue__inner">
                <p class="r-venue__intro" data-reveal>Ruangannya di {{ config('retina.venue.building') }}, kampus UKWMS Kalisari.</p>

                <h2 id="lokasi-title" class="r-venue__name" data-reveal style="--reveal-delay: 80ms">
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener">{{ config('retina.venue.room') }}</a>
                </h2>

                <div class="r-venue__foot">
                    <dl class="r-venue__facts" data-reveal style="--reveal-delay: 160ms">
                        <div><dt>Tanggal</dt><dd>{{ $dateLabel }}</dd></div>
                        <div><dt>Waktu</dt><dd>{{ $timeLabel }}</dd></div>
                        <div>
                            <dt>Biaya</dt>
                            <dd>{{ $fee }}@if (filled(config('retina.perks'))) · {{ config('retina.perks') }}@endif</dd>
                        </div>
                        <div><dt>Alamat</dt><dd>{{ config('retina.venue.campus') }}, {{ config('retina.venue.address') }}</dd></div>
                    </dl>
                    <div class="r-venue__links" data-reveal style="--reveal-delay: 240ms">
                        <a class="r-link" href="{{ $mapsUrl }}" target="_blank" rel="noopener">Buka di Google Maps ↗</a>
                        <a class="r-link" href="{{ $calendarUrl }}" target="_blank" rel="noopener">Tambah ke Google Calendar ↗</a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ FAQ ============ --}}
        <section id="faq" class="r-faq" aria-labelledby="faq-title">
            <div class="r-container r-faq__grid">
                <div class="r-faq__main">
                    <h2 id="faq-title" class="r-h2" data-reveal>Sebelum kamu daftar.</h2>

                    @php
                        $faqs = [
                            'Siapa saja yang boleh ikut?' => 'Mahasiswa UKWMS maupun mahasiswa dari kampus lain. Peserta eksternal cukup menuliskan nama universitasnya di formulir.',
                            'Kapan pendaftaran ditutup?' => $registrationClosesAt
                                ? 'Pada '.$registrationClosesAt->locale('id')->translatedFormat('l, j F Y \p\u\k\u\l H.i').' WIB, atau lebih awal jika kuota sudah terpenuhi.'
                                : 'Pendaftaran ditutup begitu kuota peserta terpenuhi, jadi jangan menunggu terlalu lama.',
                            'Kapan tiket saya dikirim?' => 'Setelah panitia memverifikasi bukti transfer, tiket berisi kode QR dikirim ke email yang kamu daftarkan. Cek juga folder spam.',
                            'Bagaimana jika pendaftaran saya ditolak?' => 'Alasan penolakan dikirim lewat email. Silakan daftar ulang dari awal menggunakan alamat email yang berbeda.',
                            'Apakah saya mendapat sertifikat?' => 'Ya. Nama lengkap yang kamu isi di formulir akan dicetak di sertifikat, jadi pastikan penulisannya benar.',
                            'Saya punya alergi makanan.' => 'Tuliskan di kolom alergi makanan pada formulir agar panitia bisa menyiapkannya.',
                        ];
                    @endphp

                    <div class="r-faq__list" data-reveal style="--reveal-delay: 80ms">
                        @foreach ($faqs as $question => $answer)
                            <details class="r-faq__item" @if ($loop->first) open @endif>
                                <summary>
                                    {{ $question }}
                                    <svg class="r-faq__chevron" aria-hidden="true" width="16" height="16" viewBox="0 0 16 16"><path d="M3 6l5 5 5-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </summary>
                                <p>{{ $answer }}</p>
                            </details>
                        @endforeach
                    </div>

                    <div class="r-faq__actions" data-reveal style="--reveal-delay: 160ms">
                        @if ($isRegistrationOpen)
                            <a class="r-btn r-btn--navy" href="{{ route('registration') }}">Daftar sekarang <span class="r-arrow" aria-hidden="true">→</span></a>
                        @endif
                        @if ($whatsappUrl)
                            <a class="r-link r-link--ink" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">Tanya panitia lewat WhatsApp</a>
                        @endif
                    </div>
                </div>

                <div class="r-faq__media">
                    <figure class="r-faq__tall" data-reveal>
                        <img src="{{ asset($images['anatomical-theatre-cambridge']['file']) }}" alt="{{ $images['anatomical-theatre-cambridge']['alt'] }}" width="900" height="1200" loading="lazy">
                    </figure>
                    <figure class="r-faq__square" data-reveal style="--reveal-delay: 120ms">
                        <img src="{{ asset($images['retina-drawing']['file']) }}" alt="{{ $images['retina-drawing']['alt'] }}" width="800" height="800" loading="lazy">
                    </figure>
                </div>
            </div>
        </section>

        {{-- ============ CTA: satu kata adalah tombolnya ============ --}}
        @if ($isRegistrationOpen)
            <a class="r-band" href="{{ route('registration') }}">
                <span class="r-band__note">Isi formulir, transfer {{ $fee }}, dan tiket QR menyusul lewat email.</span>
                <svg class="r-band__arrow" aria-hidden="true" width="48" height="48" viewBox="0 0 48 48"><path d="M12 36 36 12M16 12h20v20" fill="none" stroke="currentColor" stroke-width="4"/></svg>
                <span class="r-band__word">Daftar</span>
            </a>
        @else
            <div class="r-band r-band--closed">
                <span class="r-band__note">Terima kasih atas antusiasmenya. Sampai jumpa di kesempatan berikutnya.</span>
                <span class="r-band__word">Ditutup</span>
            </div>
        @endif
    </main>

    @if ($isRegistrationOpen)
        <a class="r-btn r-btn--gold r-dock" href="{{ route('registration') }}">Daftar <span class="r-arrow" aria-hidden="true">→</span></a>
    @endif
@endsection

@section('credits')
    <p class="r-footer__credits">
        Gambar: lukisan dan gravur arsip tentang pengajaran kedokteran, bukan dokumentasi acara RETINA.
        @foreach ($images as $image)
            <a href="{{ $image['source'] }}" target="_blank" rel="noopener">{{ $image['caption'] }}</a> ({{ $image['license'] }}){{ $loop->last ? '.' : ';' }}
        @endforeach
    </p>
@endsection
