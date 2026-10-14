@extends('layouts.retina')

@use('App\Models\Participant')
@use('Illuminate\Support\Number')
@use('Illuminate\Support\Str')
@use('Illuminate\Support\Facades\Storage')

@php
    $fee = Number::currency(Participant::REGISTRATION_FEE, 'IDR', 'id', 0);
    $dateLabel = $startsAt->locale('id')->translatedFormat('l, j F Y');
    $speaker = config('retina.speaker');
    $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI'];
    $mapsQuery = urlencode(config('retina.venue.maps_query'));
@endphp

@section('content')
    {{-- ============ HERO ============ --}}
    <header class="r-hero">
        @include('partials.retina.nav', ['showCta' => $isRegistrationOpen])

        <div id="konten" class="r-container r-hero__body">
            <div class="r-hero__copy">
                @if ($isRegistrationOpen)
                    <p class="r-pill r-rise r-d1">
                        <span class="r-dot r-pulse" aria-hidden="true"></span>
                        @if ($remainingQuota !== null)
                            Pendaftaran dibuka · Sisa {{ $remainingQuota }} kursi
                        @elseif ($registrationClosesAt)
                            Pendaftaran dibuka hingga {{ $registrationClosesAt->locale('id')->translatedFormat('j F') }}
                        @else
                            Pendaftaran dibuka · Kuota terbatas
                        @endif
                    </p>
                @else
                    <p class="r-pill r-pill--closed r-rise r-d1">
                        <span class="r-dot" aria-hidden="true"></span>
                        Pendaftaran ditutup
                    </p>
                @endif

                <h1 class="r-hero__title r-rise r-d2">RETINA<br><span class="r-hero__year">{{ $startsAt->year }}</span></h1>

                <p class="r-hero__theme r-rise r-d3">{{ config('retina.theme') }}</p>
                <p class="r-hero__lead r-rise r-d3">Satu hari berlatih berbicara di depan publik untuk insan kesehatan — dari menyusun pesan yang jernih sampai berdiri tenang di atas panggung.</p>

                <div class="r-hero__actions r-rise r-d4">
                    @if ($isRegistrationOpen)
                        <a class="r-btn r-btn--gold r-btn--lg" href="{{ route('registration') }}">Amankan kursimu <span class="r-arrow" aria-hidden="true">→</span></a>
                    @endif
                    <a class="r-btn r-btn--ghost r-btn--lg" href="#materi">Lihat materi</a>
                </div>

                <p class="r-hero__speaker r-rise r-d5">Bersama <strong>{{ $speaker['name'] }}</strong></p>
            </div>

            <div class="r-hero__ticket r-rise r-d4">
                @include('partials.retina.ticket', ['startsAt' => $startsAt])
            </div>
        </div>
    </header>

    <div class="r-strip">
        <div class="r-container">
            <span>Terbuka untuk mahasiswa UKWMS &amp; kampus lain</span>
            <span class="r-mono">{{ $startsAt->format('d.m.Y') }} — {{ Str::upper(config('retina.venue.room')) }}</span>
            <span>Tiket QR dikirim lewat email</span>
        </div>
    </div>

    <main>
        {{-- ============ MATERI ============ --}}
        <section id="materi" class="r-section">
            <div class="r-container">
                <div class="r-section__head" data-reveal>
                    <div>
                        <p class="r-eyebrow">Susunan acara</p>
                        <h2 class="r-h2">{{ Str::ucfirst(Number::spell(count(config('retina.sessions')), 'id')) }} babak,<br>satu panggung.</h2>
                    </div>
                    <p class="r-lead">Disusun seperti pertunjukan: tiap babak membangun kepercayaan diri untuk babak berikutnya.</p>
                </div>

                <div class="r-acts">
                    @foreach (config('retina.sessions') as $session)
                        <article class="r-card r-lift r-act {{ $loop->last ? 'r-act--dark' : '' }}" data-reveal style="--reveal-delay: {{ $loop->index * 90 }}ms">
                            <div class="r-act__top">
                                <span class="r-act__label">BABAK {{ $romanNumerals[$loop->index] ?? $loop->iteration }}</span>
                                <span class="r-act__num" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <h3>{{ $session['title'] }}</h3>
                            <p>{{ $session['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ============ PEMBICARA ============ --}}
        <section id="pembicara" class="r-section r-section--tight-top">
            <div class="r-container r-speaker" data-reveal>
                <div class="r-speaker__media">
                    <div class="r-portrait">
                        <svg aria-hidden="true" viewBox="0 0 400 500" preserveAspectRatio="xMidYMid slice">
                            <circle cx="200" cy="250" r="190" fill="none" stroke="#2A54B0" stroke-width="1"/>
                            <circle cx="200" cy="250" r="130" fill="none" stroke="#2A54B0" stroke-width="1"/>
                            <circle cx="200" cy="250" r="70" fill="none" stroke="#2A54B0" stroke-width="1"/>
                        </svg>
                        @if (filled($speaker['photo']))
                            <img src="{{ Storage::disk('public')->url($speaker['photo']) }}" alt="Foto {{ $speaker['name'] }}" loading="lazy">
                        @else
                            <span class="r-portrait__initials" aria-hidden="true">{{ Str::of($speaker['name'])->explode(' ')->map(fn (string $word): string => Str::substr($word, 0, 1))->take(2)->implode('') }}</span>
                        @endif
                    </div>
                    <p class="r-badge">Pembicara</p>
                </div>

                <div class="r-speaker__body">
                    <p class="r-eyebrow">Di atas panggung</p>
                    <h2 class="r-speaker__name">{{ $speaker['name'] }}</h2>
                    @if (filled($speaker['role']))
                        <p class="r-speaker__role">{{ $speaker['role'] }}</p>
                    @endif
                    @if (filled($speaker['bio']))
                        <p class="r-speaker__bio">{{ $speaker['bio'] }}</p>
                    @endif
                    <blockquote class="r-speaker__quote">
                        <p><em>Speak up</em>, stand out, and make an impact.</p>
                    </blockquote>
                </div>
            </div>
        </section>

        {{-- ============ DETAIL ============ --}}
        <section class="r-section r-details" aria-labelledby="detail-title">
            <div class="r-container">
                <div class="r-section__head" data-reveal>
                    <h2 id="detail-title" class="r-h2">Catat di kalendermu.</h2>
                    <a class="r-btn r-btn--ghost" href="{{ $calendarUrl }}" target="_blank" rel="noopener">+ Tambah ke Google Calendar</a>
                </div>
                <dl class="r-facts" data-reveal>
                    <div class="r-fact">
                        <dt>TANGGAL</dt>
                        <dd>{{ $dateLabel }}</dd>
                    </div>
                    <div class="r-fact">
                        <dt>WAKTU</dt>
                        <dd>
                            {{ $startsAt->format('H.i') }} WIB
                            @if ($endsAt)
                                <span>hingga {{ $endsAt->format('H.i') }} WIB</span>
                            @endif
                        </dd>
                    </div>
                    <div class="r-fact">
                        <dt>TEMPAT</dt>
                        <dd>{{ config('retina.venue.room') }}<span>{{ config('retina.venue.building') }}</span></dd>
                    </div>
                    <div class="r-fact">
                        <dt>BIAYA</dt>
                        <dd>
                            {{ $fee }}
                            @if (filled(config('retina.perks')))
                                <span>{{ config('retina.perks') }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </section>

        {{-- ============ CARA DAFTAR ============ --}}
        <section id="cara-daftar" class="r-section">
            <div class="r-container">
                <div class="r-section__head" data-reveal>
                    <div>
                        <p class="r-eyebrow">Cara daftar</p>
                        <h2 class="r-h2">Empat langkah<br>menuju kursimu.</h2>
                    </div>
                </div>

                <ol class="r-steps">
                    <li class="r-step" data-reveal>
                        <span class="r-step__num">01</span>
                        <h3>Isi formulir</h3>
                        <p>Nama, email aktif, nomor WhatsApp, dan data akademik — mahasiswa UKWMS atau kampus lain.</p>
                    </li>
                    <li class="r-step" data-reveal style="--reveal-delay: 90ms">
                        <span class="r-step__num">02</span>
                        <h3>Transfer &amp; unggah bukti</h3>
                        <p>Bayar {{ $fee }} ke rekening panitia, lalu unggah foto bukti transfer di formulir.</p>
                    </li>
                    <li class="r-step" data-reveal style="--reveal-delay: 180ms">
                        <span class="r-step__num">03</span>
                        <h3>Verifikasi panitia</h3>
                        <p>Panitia mengecek pembayaranmu. Hasilnya dikabarkan lewat email.</p>
                    </li>
                    <li class="r-step" data-reveal style="--reveal-delay: 270ms">
                        <span class="r-step__num">04</span>
                        <h3>Tiket QR masuk</h3>
                        <p>Tunjukkan QR dari email saat registrasi ulang di depan {{ config('retina.venue.room') }}.</p>
                    </li>
                </ol>

                <div class="r-pay" data-reveal>
                    @include('partials.retina.bank')

                    @if ($isRegistrationOpen)
                        <a class="r-cta-card" href="{{ route('registration') }}">
                            <small>Sudah siap?</small>
                            <strong>Buka formulir <span class="r-arrow" aria-hidden="true">→</span></strong>
                        </a>
                    @else
                        <div class="r-cta-card r-cta-card--closed">
                            <small>Terima kasih atas antusiasmenya</small>
                            <strong>Pendaftaran ditutup</strong>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- ============ LOKASI ============ --}}
        <section id="lokasi" class="r-section r-section--tight-top">
            <div class="r-container r-location" data-reveal>
                <div class="r-card r-location__info">
                    <p class="r-eyebrow">Lokasi</p>
                    <h2>{{ config('retina.venue.room') }}</h2>
                    <p>{{ config('retina.venue.building') }}<br>{{ config('retina.venue.campus') }}<br>{{ config('retina.venue.address') }}</p>
                    <a class="r-btn r-btn--navy" href="https://www.google.com/maps/search/?api=1&query={{ $mapsQuery }}" target="_blank" rel="noopener">Buka di Google Maps <span class="r-arrow" aria-hidden="true">↗</span></a>
                </div>
                <div class="r-map">
                    <iframe
                        title="Peta lokasi {{ config('retina.venue.campus') }}"
                        src="https://www.google.com/maps?q={{ $mapsQuery }}&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </section>

        {{-- ============ FAQ ============ --}}
        <section id="faq" class="r-section r-section--tight-top">
            <div class="r-container r-faq">
                <div class="r-faq__intro" data-reveal>
                    <p class="r-eyebrow">FAQ</p>
                    <h2 class="r-h2">Sebelum<br>kamu daftar.</h2>
                    @if (filled(config('retina.contact.whatsapp')))
                        <p>Masih ada pertanyaan? Hubungi panitia lewat <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', config('retina.contact.whatsapp'))) }}" target="_blank" rel="noopener">WhatsApp</a>.</p>
                    @endif
                </div>

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

                <div class="r-faq__list" data-reveal>
                    @foreach ($faqs as $question => $answer)
                        <details class="r-faq__item" @if ($loop->first) open @endif>
                            <summary>
                                {{ $question }}
                                <span class="r-faq__icon" aria-hidden="true">
                                    <svg width="14" height="14" viewBox="0 0 14 14"><path d="M7 1v12M1 7h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </span>
                            </summary>
                            <p>{{ $answer }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ============ CTA ============ --}}
        <section class="r-cta">
            <svg aria-hidden="true" viewBox="0 0 400 400">
                <circle cx="200" cy="200" r="190" fill="none" stroke="#0B1F4B" stroke-opacity=".14" stroke-width="1.5"/>
                <circle cx="200" cy="200" r="120" fill="none" stroke="#0B1F4B" stroke-opacity=".14" stroke-width="1.5"/>
                <circle cx="200" cy="200" r="48" fill="#0B1F4B" fill-opacity=".1"/>
            </svg>
            <div class="r-container" data-reveal>
                <div>
                    <h2>Kursi terbatas.<br>Panggungnya menunggu.</h2>
                    <p>{{ $dateLabel }} · {{ config('retina.venue.room') }}</p>
                </div>
                @if ($isRegistrationOpen)
                    <a class="r-btn r-btn--navy r-btn--lg" href="{{ route('registration') }}">Daftar sekarang <span class="r-arrow" aria-hidden="true" style="color: var(--gold)">→</span></a>
                @else
                    <span class="r-btn r-btn--navy r-btn--lg" aria-disabled="true">Pendaftaran ditutup</span>
                @endif
            </div>
        </section>
    </main>
@endsection
