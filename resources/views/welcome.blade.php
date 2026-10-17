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
    $speakerPhotoUrl = match (true) {
        filled($speaker['photo']) => Storage::disk('public')->url($speaker['photo']),
        filled($speaker['photo_default'] ?? null) => asset($speaker['photo_default']),
        default => null,
    };
    $banner = config('retina.banner');
    $venueImage = config('retina.venue_image');
    $mapsQuery = urlencode(config('retina.venue.maps_query'));
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query='.$mapsQuery;
    $whatsappUrl = filled(config('retina.contact.whatsapp'))
        ? 'https://wa.me/'.preg_replace('/^0/', '62', preg_replace('/\D/', '', config('retina.contact.whatsapp')))
        : null;
@endphp

@push('head')
    @php
        $eventSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => config('retina.name').' — '.config('retina.tagline'),
            'description' => config('retina.theme'),
            'startDate' => $startsAt->copy()->setTimezone('Asia/Jakarta')->toIso8601String(),
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'image' => [asset($banner['file'])],
            'location' => [
                '@type' => 'Place',
                'name' => config('retina.venue.room').', '.config('retina.venue.building'),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => config('retina.venue.address'),
                    'addressLocality' => 'Surabaya',
                    'addressCountry' => 'ID',
                ],
            ],
            'organizer' => [
                '@type' => 'CollegeOrUniversity',
                'name' => config('retina.venue.campus'),
                'url' => 'https://www.ukwms.ac.id',
                'logo' => asset('images/retina/logo-ukwms.png'),
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => Participant::REGISTRATION_FEE,
                'priceCurrency' => 'IDR',
                'availability' => $isRegistrationOpen ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
            ],
        ];

        if ($isRegistrationOpen) {
            $eventSchema['offers']['url'] = route('registration');
        }

        if (filled($speaker['name'] ?? null)) {
            $eventSchema['performer'] = ['@type' => 'Person', 'name' => $speaker['name']];
        }

        if ($endsAt) {
            $eventSchema['endDate'] = $endsAt->copy()->setTimezone('Asia/Jakarta')->toIso8601String();
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($eventSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    {{-- ============ HERO ============ --}}
    <header class="r-hero r-hero--home">
        @include('partials.retina.nav', ['showCta' => $isRegistrationOpen])

        <div id="konten" class="r-container r-hero__stage">
            <h1 class="r-sr-only">RETINA {{ $startsAt->year }}: {{ config('retina.theme') }}</h1>

            <figure class="r-hero__banner r-rise r-d1">
                <img src="{{ asset($banner['file']) }}" alt="{{ $banner['alt'] }}" width="1280" height="720" fetchpriority="high">
            </figure>

            <div class="r-hero__strip r-rise r-d2">
                <div>
                    <p class="r-hero__when">{{ $dateLabel }} · {{ $timeLabel }}</p>
                    <p class="r-hero__fee">{{ config('retina.venue.room') }} · {{ $fee }}@if (filled(config('retina.perks'))), {{ Str::lcfirst(config('retina.perks')) }}@endif</p>
                </div>

                <div class="r-hero__actions">
                    @if ($isRegistrationOpen)
                        <p class="r-hero__status">
                            @if ($remainingQuota !== null)
                                Pendaftaran dibuka · Sisa {{ $remainingQuota }} kursi
                            @elseif ($registrationClosesAt)
                                Pendaftaran dibuka hingga {{ $registrationClosesAt->locale('id')->translatedFormat('j F') }}
                            @else
                                Pendaftaran dibuka · Kuota terbatas
                            @endif
                        </p>
                        <a class="r-btn r-btn--yellow r-btn--lg" href="{{ route('registration') }}">Amankan kursimu <span class="r-arrow" aria-hidden="true">→</span></a>
                    @else
                        <p class="r-hero__status r-hero__status--closed">Pendaftaran ditutup</p>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main>
        {{-- ============ MATERI ============ --}}
        <section id="materi" class="r-section" aria-labelledby="materi-title">
            <div class="r-container r-split">
                <div class="r-split__head" data-reveal>
                    <h2 id="materi-title" class="r-h2">{{ Str::ucfirst(Number::spell(count(config('retina.sessions')), 'id')) }} babak, satu panggung.</h2>
                    <p class="r-lead">Tiap babak membangun kepercayaan diri untuk babak berikutnya. {{ $dateLabel }}, {{ $timeLabel }}.</p>
                </div>

                <ol class="r-sessions">
                    @foreach (config('retina.sessions') as $session)
                        <li class="r-session" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms">
                            <h3>{{ $session['title'] }}</h3>
                            <p>{{ $session['description'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- ============ PEMBICARA ============ --}}
            <div id="pembicara" class="r-container">
                <div class="r-speaker {{ $speakerPhotoUrl ? 'r-speaker--photo' : '' }}" data-reveal>
                    @if ($speakerPhotoUrl)
                        <figure class="r-speaker__media" data-reveal style="--reveal-delay: 90ms">
                            <img src="{{ $speakerPhotoUrl }}" alt="Foto {{ $speaker['name'] }}" width="600" height="750" loading="lazy">
                        </figure>
                    @endif

                    <div class="r-speaker__body" data-reveal data-stagger>
                        <h2 class="r-speaker__name">{{ $speaker['name'] }}</h2>
                        <p class="r-speaker__role">{{ filled($speaker['role']) ? $speaker['role'] : 'Guest speaker '.config('retina.name') }}</p>
                        @if (filled($speaker['bio']))
                            <p class="r-speaker__bio">{{ $speaker['bio'] }}</p>
                        @endif
                        <blockquote class="r-speaker__quote"><p>“{{ config('retina.slogan') }}”</p></blockquote>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ CARA DAFTAR ============ --}}
        <section id="cara-daftar" class="r-section r-section--tint" aria-labelledby="cara-daftar-title">
            <div class="r-container">
                <div class="r-section__head" data-reveal>
                    <h2 id="cara-daftar-title" class="r-h2">Cara daftar</h2>
                    <p class="r-lead">Empat langkah. Tiket QR dikirim lewat email setelah pembayaranmu diverifikasi panitia.</p>
                </div>

                <div class="r-register">
                    <ol class="r-steps">
                        <li class="r-step" data-reveal>
                            <h3>Isi formulir</h3>
                            <p>Nama, email aktif, nomor WhatsApp, dan data akademik. Terbuka untuk mahasiswa UKWMS dan kampus lain.</p>
                        </li>
                        <li class="r-step" data-reveal style="--reveal-delay: 70ms">
                            <h3>Transfer &amp; unggah bukti</h3>
                            <p>Bayar {{ $fee }} ke rekening panitia, lalu unggah foto bukti transfernya di formulir.</p>
                        </li>
                        <li class="r-step" data-reveal style="--reveal-delay: 140ms">
                            <h3>Tunggu verifikasi</h3>
                            <p>Panitia mengecek pembayaranmu. Hasilnya dikirim lewat email, jadi cek juga folder spam.</p>
                        </li>
                        <li class="r-step" data-reveal style="--reveal-delay: 210ms">
                            <h3>Tunjukkan tiket QR</h3>
                            <p>Saat registrasi ulang di depan {{ config('retina.venue.room') }}.</p>
                        </li>
                    </ol>

                    <div class="r-register__side">
                        <div data-reveal>
                            @include('partials.retina.bank')
                        </div>

                        <div class="r-try" data-reveal style="--reveal-delay: 90ms">
                            <label class="r-label" for="ticket-name">Coba tulis namamu di tiket</label>
                            <input id="ticket-name" class="r-input" type="text" maxlength="60" autocomplete="name" placeholder="Nama lengkap" data-ticket-name>
                            @include('partials.retina.ticket', ['startsAt' => $startsAt, 'showHolder' => true])
                            @if ($isRegistrationOpen)
                                <a class="r-btn r-btn--blue" href="{{ route('registration') }}" data-ticket-link>Daftar dengan nama ini <span class="r-arrow" aria-hidden="true">→</span></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ LOKASI ============ --}}
        <section id="lokasi" class="r-section" aria-labelledby="lokasi-title">
            <div class="r-container r-venue">
                <div class="r-venue__info" data-reveal>
                    <h2 id="lokasi-title" class="r-h2">{{ config('retina.venue.room') }}</h2>
                    <p class="r-lead">{{ config('retina.venue.building') }}, {{ config('retina.venue.campus') }}, {{ config('retina.venue.address') }}.</p>

                    <dl class="r-venue__facts">
                        <div><dt>Tanggal</dt><dd>{{ $dateLabel }}</dd></div>
                        <div><dt>Waktu</dt><dd>{{ $timeLabel }}</dd></div>
                    </dl>

                    <div class="r-venue__links">
                        <a class="r-btn r-btn--blue" href="{{ $mapsUrl }}" target="_blank" rel="noopener">Buka di Google Maps <span class="r-arrow" aria-hidden="true">↗</span></a>
                        <a class="r-btn r-btn--soft" href="{{ $calendarUrl }}" target="_blank" rel="noopener">Tambah ke Google Calendar</a>
                    </div>

                    <figure class="r-venue__photo">
                        <img src="{{ asset($venueImage['file']) }}" alt="{{ $venueImage['alt'] }}" width="1000" height="1000" loading="lazy">
                        <figcaption>{{ $venueImage['caption'] }}</figcaption>
                    </figure>
                </div>

                <div class="r-map" data-reveal style="--reveal-delay: 90ms">
                    <iframe
                        title="Peta lokasi {{ config('retina.venue.campus') }}"
                        src="https://www.google.com/maps?q={{ $mapsQuery }}&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </section>

        {{-- ============ FAQ ============ --}}
        <section id="faq" class="r-section r-section--sun" aria-labelledby="faq-title">
            <div class="r-container r-split">
                <div class="r-split__head" data-reveal>
                    <h2 id="faq-title" class="r-h2">Sebelum kamu daftar.</h2>
                    @if ($whatsappUrl)
                        <p class="r-lead">Masih ada pertanyaan? <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener">Tanya panitia lewat WhatsApp</a>.</p>
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

                <div class="r-faq" data-reveal style="--reveal-delay: 80ms">
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
            </div>
        </section>

        {{-- ============ CTA ============ --}}
        <section class="r-cta" aria-label="Pendaftaran">
            <div class="r-container r-cta__inner" data-reveal>
                @if ($isRegistrationOpen)
                    <div>
                        <h2>Kursi terbatas.</h2>
                        <p>Panggungnya menunggu. {{ $dateLabel }} · {{ config('retina.venue.room') }} · {{ $fee }}</p>
                    </div>
                    <a class="r-btn r-btn--yellow r-btn--lg" href="{{ route('registration') }}">Daftar sekarang <span class="r-arrow" aria-hidden="true">→</span></a>
                @else
                    <div>
                        <h2>Pendaftaran ditutup.</h2>
                        <p>Terima kasih atas antusiasmenya. Sampai jumpa di kesempatan berikutnya.</p>
                    </div>
                @endif
            </div>
        </section>
    </main>
@endsection

@section('credits')
    <p class="r-footer__credits">
        Foto: {{ $venueImage['caption'] }}, sumber <a href="{{ $venueImage['source'] }}" target="_blank" rel="noopener">{{ $venueImage['credit'] }}</a>.
    </p>
@endsection
