@extends('layouts.guest')

@section('content')
@php
    $venue = config('event.venue');
    $agenda = config('event.agenda');
@endphp
<div class="text-ink">
    {{-- Section 1: Hero --}}
    @include('partials.landing.hero')

    {{-- Section 2: Event Details (ringkasan teks dari banner) --}}
    @include('partials.landing.event-details')

    {{-- Section 3: Tema --}}
    <section class="bg-ink text-white">
        <div class="max-w-6xl mx-auto px-6 py-20 md:py-28 grid md:grid-cols-12 gap-8">
            <div class="md:col-span-2">
                <span data-reveal="left" class="inline-block text-[0.7rem] font-semibold uppercase tracking-[0.2em] text-wm-yellow">Tema</span>
            </div>
            <blockquote data-reveal="slow" style="--reveal-delay: 120ms" class="md:col-span-10 font-display text-3xl md:text-5xl leading-[1.15] text-balance">
                <span class="text-wm-yellow">&ldquo;</span>{{ config('event.topic') }}<span class="text-wm-yellow">&rdquo;</span>
            </blockquote>
        </div>
    </section>

    {{-- Section 4: Speakers --}}
    <section id="speakers" class="bg-paper py-20 md:py-28 scroll-mt-20">
        <div class="max-w-6xl mx-auto px-6">
            <h2 data-reveal class="font-display text-4xl md:text-5xl mb-12 md:mb-16">Speakers</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-14">
                @foreach (config('event.speakers') as $speaker)
                    <article class="group" style="--card-delay: {{ $loop->index * 140 }}ms">
                        <div data-reveal="wipe" style="--reveal-delay: var(--card-delay)" class="relative aspect-[4/5] overflow-hidden bg-[#e4e1dc]">
                            {{-- multiply: latar putih PNG melebur ke warna kartu --}}
                            <img src="{{ $speaker['photo'] }}"
                                 alt="{{ $speaker['name'] }}"
                                 loading="lazy"
                                 class="absolute inset-0 w-full h-full object-cover object-[50%_20%] scale-[1.35] origin-[50%_28%] mix-blend-multiply transition-transform duration-700 group-hover:scale-[1.4]">
                            <span class="absolute left-0 bottom-0 bg-wm-red text-white text-xs font-semibold uppercase tracking-[0.16em] px-4 py-2">
                                {{ $speaker['role'] }}
                            </span>
                        </div>
                        <div data-reveal="soft" style="--reveal-delay: calc(var(--card-delay) + 350ms)" class="pt-6 border-b border-ink/15 pb-6">
                            <h3 class="font-display text-3xl md:text-4xl leading-tight">{{ $speaker['name'] }}</h3>
                            <p class="mt-2 text-sm font-semibold uppercase tracking-[0.12em] text-wm-red">{{ $speaker['title'] }}</p>
                            @if ($speaker['full_name'])
                                <p class="mt-3 text-sm text-ink/60 leading-relaxed">{{ $speaker['full_name'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Section 5: Agenda --}}
    <section id="agenda" class="bg-white py-20 md:py-28 scroll-mt-20">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-10">
                    <div data-reveal class="flex items-baseline gap-4 mb-8">
                        <h2 class="font-display text-4xl md:text-5xl">Rundown</h2>
                        @if ($agenda['is_placeholder'])
                            <span class="text-[0.65rem] font-semibold uppercase tracking-[0.16em] text-ink/50 border border-ink/20 px-2 py-1">Contoh data</span>
                        @endif
                    </div>
                    <figure data-reveal="wipe" style="--reveal-delay: 150ms">
                        <img src="{{ $agenda['photo'] }}" alt="" loading="lazy"
                             class="w-full aspect-[4/3] object-cover grayscale contrast-110">
                        <figcaption class="mt-2 text-[0.7rem] text-ink/40">{{ $agenda['photo_credit'] }}</figcaption>
                    </figure>
                </div>
            </div>

            <ol class="lg:col-span-7">
                @foreach ($agenda['items'] as $item)
                    <li data-reveal="soft" class="group/row relative grid grid-cols-[5rem_1fr] md:grid-cols-[7rem_1fr] gap-4 py-6 md:py-7">
                        <span data-reveal="line" class="absolute inset-x-0 top-0 h-px bg-ink/15"></span>
                        @if ($loop->last)
                            <span data-reveal="line" style="--reveal-delay: 150ms" class="absolute inset-x-0 bottom-0 h-px bg-ink/15"></span>
                        @endif
                        <time class="font-display text-3xl md:text-4xl leading-none tabular-nums text-wm-red transition-transform duration-300 ease-out group-hover/row:translate-x-1">{{ $item['time'] }}</time>
                        <div>
                            <h3 class="text-lg md:text-xl font-semibold leading-snug">{{ $item['title'] }}</h3>
                            @if ($item['note'])
                                <p class="mt-1 text-sm text-ink/60 leading-relaxed">{{ $item['note'] }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Section 6: Venue --}}
    <section id="lokasi" class="relative bg-ink text-white scroll-mt-20">
        <div data-reveal="drift" class="absolute inset-0 overflow-hidden">
            <img src="{{ $venue['photo'] }}" alt="" loading="lazy"
                 class="w-full h-full object-cover opacity-40">
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/80 to-transparent"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-20 md:py-32">
            <div class="max-w-xl" data-reveal-group>
                <span data-reveal class="inline-block text-[0.7rem] font-semibold uppercase tracking-[0.2em] text-wm-yellow">Lokasi</span>
                <h2 data-reveal class="font-display text-4xl md:text-6xl mt-3 leading-[1.05]">{{ $venue['name'] }}</h2>
                <p data-reveal class="mt-5 text-white/80 leading-relaxed">
                    {{ $venue['institution'] }}<br>{{ $venue['address'] }}
                </p>

                <ul data-reveal class="mt-8 space-y-3 text-sm text-white/70">
                    <li class="flex gap-3"><span class="mt-2 h-px w-5 bg-wm-yellow shrink-0"></span>Hadir {{ config('event.arrive_early_minutes') }} menit sebelum acara dimulai.</li>
                    <li class="flex gap-3"><span class="mt-2 h-px w-5 bg-wm-yellow shrink-0"></span>Bawa QR code undangan yang dikirim ke email setelah registrasi divalidasi.</li>
                </ul>

                <div data-reveal class="mt-10">
                <a href="{{ $venue['maps_url'] }}" target="_blank" rel="noopener"
                   class="group/map inline-flex items-center gap-3 bg-wm-yellow text-ink font-semibold px-7 py-4 hover:bg-white transition-colors duration-300">
                    Buka di Google Maps
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-300 ease-out group-hover/map:translate-x-0.5 group-hover/map:-translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" /></svg>
                </a>
                </div>
            </div>

            @if ($venue['photo_is_placeholder'])
                <div data-reveal="fade" class="mt-16 flex flex-wrap items-center gap-x-4 gap-y-1 text-[0.7rem] text-white/40">
                    <span class="font-semibold uppercase tracking-[0.16em] border border-white/25 px-2 py-1">Contoh foto</span>
                    <span>{{ $venue['photo_credit'] }}</span>
                </div>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    @include('partials.landing.footer')
</div>
@endsection
