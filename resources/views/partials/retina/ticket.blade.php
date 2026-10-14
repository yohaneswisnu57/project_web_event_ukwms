@php
    /** @var \Illuminate\Support\Carbon $startsAt */
    $secondsLeft = max(0, (int) now()->diffInSeconds($startsAt, false));
    $countdown = [
        'days' => intdiv($secondsLeft, 86400),
        'hours' => intdiv($secondsLeft % 86400, 3600),
        'minutes' => intdiv($secondsLeft % 3600, 60),
    ];
    $units = ['days' => 'Hari', 'hours' => 'Jam', 'minutes' => 'Menit'];
@endphp

<article class="r-ticket" aria-label="Ringkasan tiket acara">
    <div class="r-ticket__top">
        <div class="r-ticket__head r-ticket__label">
            <span>Tiket masuk</span>
            <span>{{ $startsAt->format('d.m.Y') }}</span>
        </div>
        <p class="r-ticket__name">{{ config('retina.name') }}</p>
        <p class="r-ticket__tagline">{{ config('retina.tagline') }}</p>

        <dl class="r-ticket__rows">
            <div class="r-ticket__row">
                <dt class="r-ticket__label">Tanggal</dt>
                <dd>{{ $startsAt->locale('id')->translatedFormat('l, j F Y') }}</dd>
            </div>
            <div class="r-ticket__row">
                <dt class="r-ticket__label">Waktu</dt>
                <dd>{{ $startsAt->format('H.i') }} WIB</dd>
            </div>
            <div class="r-ticket__row">
                <dt class="r-ticket__label">Tempat</dt>
                <dd>{{ config('retina.venue.room') }}<span>{{ config('retina.venue.building') }}, UKWMS</span></dd>
            </div>
        </dl>
    </div>

    <div class="r-perforation" aria-hidden="true"><span></span></div>

    <div class="r-ticket__bottom">
        <p class="r-ticket__label">Tirai dibuka dalam</p>
        <div class="r-countdown" data-countdown="{{ $startsAt->toIso8601String() }}" role="timer" aria-label="Waktu menuju acara">
            @foreach ($units as $unit => $label)
                <div class="r-countdown__cell">
                    <div class="r-countdown__value" data-unit="{{ $unit }}">{{ str_pad((string) $countdown[$unit], 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="r-countdown__unit">{{ strtoupper($label) }}</div>
                </div>
            @endforeach
        </div>
        <div class="r-ticket__price">
            <span>Biaya pendaftaran</span>
            <span>{{ \Illuminate\Support\Number::currency(\App\Models\Participant::REGISTRATION_FEE, 'IDR', 'id', 0) }}</span>
        </div>
    </div>
</article>
