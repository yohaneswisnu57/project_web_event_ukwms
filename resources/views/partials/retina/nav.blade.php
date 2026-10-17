@php
    $anchorBase = request()->routeIs('home') ? '' : route('home');
@endphp

<nav class="r-container r-nav" aria-label="Navigasi utama">
    <a class="r-brand" href="{{ route('home') }}"><img src="{{ asset('images/retina/logo-ukwms.png') }}" alt="Logo Universitas Katolik Widya Mandala Surabaya" width="34" height="40">RETINA {{ \Illuminate\Support\Carbon::parse(config('retina.starts_at'))->year }}</a>

    <div class="r-nav__links">
        <a class="r-link" href="{{ $anchorBase }}#materi">Materi</a>
        <a class="r-link" href="{{ $anchorBase }}#cara-daftar">Cara daftar</a>
        <a class="r-link" href="{{ $anchorBase }}#lokasi">Lokasi</a>
        <a class="r-link" href="{{ $anchorBase }}#faq">FAQ</a>
    </div>

    @if ($showCta ?? true)
        <a class="r-btn r-btn--blue r-nav__cta" href="{{ route('registration') }}">Daftar <span class="r-arrow" aria-hidden="true">→</span></a>
    @elseif (! request()->routeIs('home'))
        <a class="r-btn r-btn--ghost" href="{{ route('home') }}">Beranda</a>
    @endif
</nav>
