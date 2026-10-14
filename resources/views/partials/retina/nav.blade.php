@php
    $anchorBase = request()->routeIs('home') ? '' : route('home');
@endphp

<svg class="r-rings" aria-hidden="true" viewBox="0 0 800 800">
    <circle cx="400" cy="400" r="390" fill="none" stroke="#1E4499" stroke-width="1"/>
    <circle cx="400" cy="400" r="310" fill="none" stroke="#1E4499" stroke-width="1"/>
    <g class="r-ring-slow">
        <circle cx="400" cy="400" r="235" fill="none" stroke="#F5B400" stroke-opacity=".35" stroke-width="1.5" stroke-dasharray="2 10"/>
    </g>
    <circle cx="400" cy="400" r="160" fill="none" stroke="#1E4499" stroke-width="1"/>
    <circle cx="400" cy="400" r="88" fill="none" stroke="#1E4499" stroke-width="1"/>
</svg>

<nav class="r-container r-nav" aria-label="Navigasi utama">
    <a class="r-brand" href="{{ route('home') }}">
        <svg aria-hidden="true" width="34" height="34" viewBox="0 0 34 34">
            <circle cx="17" cy="17" r="15.5" fill="none" stroke="#F5B400" stroke-width="2"/>
            <circle cx="17" cy="17" r="8.5" fill="none" stroke="#fff" stroke-opacity=".55" stroke-width="1.5"/>
            <circle cx="17" cy="17" r="4" fill="#F5B400"/>
        </svg>
        <span class="r-brand__text">
            <span class="r-brand__name">RETINA</span>
            <span class="r-brand__sub">UKWMS · {{ \Illuminate\Support\Carbon::parse(config('retina.starts_at'))->year }}</span>
        </span>
    </a>

    <div class="r-nav__links">
        <a class="r-link" href="{{ $anchorBase }}#materi">Materi</a>
        <a class="r-link" href="{{ $anchorBase }}#pembicara">Pembicara</a>
        <a class="r-link" href="{{ $anchorBase }}#cara-daftar">Cara daftar</a>
        <a class="r-link" href="{{ $anchorBase }}#lokasi">Lokasi</a>
        <a class="r-link" href="{{ $anchorBase }}#faq">FAQ</a>
    </div>

    @if ($showCta ?? true)
        <a class="r-btn r-btn--gold r-btn--pill" href="{{ route('registration') }}">Daftar <span class="r-arrow" aria-hidden="true">→</span></a>
    @elseif (! request()->routeIs('home'))
        <a class="r-btn r-btn--ghost r-btn--pill" href="{{ route('home') }}">Beranda</a>
    @endif
</nav>
