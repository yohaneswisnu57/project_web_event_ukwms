<footer class="r-footer">
    <div class="r-container">
        <p class="r-footer__name" aria-hidden="true">RETINA</p>

        <div class="r-footer__row">
            <ul class="r-footer__places">
                <li><a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(config('retina.venue.maps_query')) }}" target="_blank" rel="noopener">{{ config('retina.venue.room') }}</a></li>
                @if (filled(config('retina.contact.whatsapp')))
                    <li>
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', config('retina.contact.whatsapp'))) }}" target="_blank" rel="noopener">
                            Narahubung{{ filled(config('retina.contact.name')) ? ': '.config('retina.contact.name') : '' }}
                        </a>
                    </li>
                @endif
                <li><a href="https://www.ukwms.ac.id" target="_blank" rel="noopener">ukwms.ac.id</a></li>
            </ul>

            <ul class="r-footer__links">
                <li><a class="r-link" href="{{ route('home') }}#materi">Materi</a></li>
                <li><a class="r-link" href="{{ route('home') }}#cara-daftar">Cara daftar</a></li>
                <li><a class="r-link" href="{{ route('home') }}#lokasi">Lokasi</a></li>
                <li><a class="r-link" href="{{ route('home') }}#faq">FAQ</a></li>
            </ul>
        </div>

        @yield('credits')

        <div class="r-footer__legal">
            <p><strong>{{ config('retina.name') }}</strong> · {{ config('retina.tagline') }} · {{ config('retina.venue.campus') }}</p>
            <a class="r-link" href="#">Kembali ke atas ↑</a>
        </div>
    </div>
</footer>
