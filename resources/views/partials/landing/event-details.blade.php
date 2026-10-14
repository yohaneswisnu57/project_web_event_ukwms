@php
    $startsAt = \Illuminate\Support\Carbon::parse(config('event.starts_at'))->locale('id');
    $endsAt = \Illuminate\Support\Carbon::parse(config('event.ends_at'))->locale('id');
    $venue = config('event.venue');
@endphp

{{-- Ringkasan teks dari banner, supaya terbaca di layar kecil. --}}
<section class="bg-white border-y border-ink/10">
    <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-12 md:divide-x divide-ink/10" data-reveal-group>
        <div class="md:col-span-3 flex items-center gap-5 py-8 md:pr-8" data-reveal wire:ignore.self>
            <div class="font-display text-7xl leading-none text-ink tabular-nums">{{ $startsAt->format('j') }}</div>
            <div class="text-sm leading-snug">
                <div class="font-semibold uppercase tracking-[0.18em] text-wm-red">{{ $startsAt->translatedFormat('F') }}</div>
                <div class="text-ink/60">{{ $startsAt->translatedFormat('l') }}, {{ $startsAt->format('Y') }}</div>
            </div>
        </div>

        <div class="md:col-span-3 py-6 md:py-8 md:px-8 border-t md:border-t-0 border-ink/10" data-reveal wire:ignore.self>
            <div class="text-[0.7rem] font-semibold uppercase tracking-[0.18em] text-ink/50 mb-1">Waktu</div>
            <div class="text-xl font-semibold tabular-nums">{{ $startsAt->format('H.i') }} – {{ $endsAt->format('H.i') }} WIB</div>
            <div class="text-sm text-ink/60 mt-1">Hadir {{ config('event.arrive_early_minutes') }} menit lebih awal.</div>
        </div>

        <div class="md:col-span-4 py-6 md:py-8 md:px-8 border-t md:border-t-0 border-ink/10" data-reveal wire:ignore.self>
            <div class="text-[0.7rem] font-semibold uppercase tracking-[0.18em] text-ink/50 mb-1">Lokasi</div>
            <div class="text-xl font-semibold">{{ $venue['name'] }}</div>
            <div class="text-sm text-ink/60 mt-1">{{ $venue['institution'] }}, {{ $venue['address'] }}</div>
        </div>

        <div class="md:col-span-2 py-6 md:py-8 md:pl-8 border-t md:border-t-0 border-ink/10" data-reveal wire:ignore.self>
            <div class="text-[0.7rem] font-semibold uppercase tracking-[0.18em] text-ink/50 mb-1">Registrasi</div>
            <div class="text-sm text-ink/70 leading-snug">Lewat tautan undangan pribadi. Satu undangan untuk satu orang.</div>
        </div>
    </div>
</section>
