<footer class="r-footer">
    <div class="r-container">
        <div class="r-footer__row">
            <div class="r-footer__brand">
                <img class="r-footer__logo" src="{{ asset('images/retina/logo-ukwms.png') }}" alt="Logo Universitas Katolik Widya Mandala Surabaya" width="51" height="60" loading="lazy">
                <strong>{{ config('retina.name') }}</strong>
                <p>{{ config('retina.tagline') }} · {{ config('retina.venue.campus') }}</p>
            </div>

            <ul class="r-footer__links">
                <li><a class="r-link" href="{{ route('home') }}#materi">Materi</a></li>
                <li><a class="r-link" href="{{ route('home') }}#cara-daftar">Cara daftar</a></li>
                <li><a class="r-link" href="{{ route('home') }}#lokasi">Lokasi</a></li>
                <li><a class="r-link" href="{{ route('home') }}#faq">FAQ</a></li>
                @if (filled(config('retina.contact.whatsapp')))
                    <li>
                        <a class="r-link" href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', config('retina.contact.whatsapp'))) }}" target="_blank" rel="noopener">
                            Narahubung{{ filled(config('retina.contact.name')) ? ': '.config('retina.contact.name') : '' }}
                        </a>
                    </li>
                @endif
                <li><a class="r-link" href="https://www.ukwms.ac.id" target="_blank" rel="noopener">ukwms.ac.id</a></li>
            </ul>
        </div>

        @yield('credits')

        <div class="r-footer__legal">
            <p>© {{ \Illuminate\Support\Carbon::parse(config('retina.starts_at'))->year }} Panitia {{ config('retina.name') }}</p>
            <a class="r-link" href="#">Kembali ke atas ↑</a>
        </div>
    </div>
</footer>
