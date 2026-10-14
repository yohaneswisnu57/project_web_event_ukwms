<footer class="r-footer">
    <div class="r-container">
        <p><strong>{{ config('retina.name') }}</strong> · {{ config('retina.tagline') }}<br>{{ config('retina.venue.campus') }}</p>

        <div class="r-footer__links">
            @if (filled(config('retina.contact.whatsapp')))
                <a class="r-link" href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', config('retina.contact.whatsapp'))) }}" target="_blank" rel="noopener">
                    Narahubung{{ filled(config('retina.contact.name')) ? ': '.config('retina.contact.name') : '' }}
                </a>
            @endif
            <a class="r-link" href="https://www.ukwms.ac.id" target="_blank" rel="noopener">ukwms.ac.id</a>
        </div>
    </div>
</footer>
