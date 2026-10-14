/* RETINA 2026 — perilaku kecil halaman publik: scroll reveal, hitung mundur, salin nomor rekening. */
(function () {
    var root = document.documentElement;

    /* Scroll reveal: konten hanya disembunyikan setelah observer siap, jadi tanpa JS semuanya tetap terlihat. */
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
        var targets = document.querySelectorAll('[data-reveal]');

        if (targets.length) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });

            root.classList.add('r-reveal-ready');
            targets.forEach(function (el) { observer.observe(el); });
        }
    }

    /* Hitung mundur: angka awal sudah dirender server, script ini hanya menyegarkannya. */
    var countdowns = document.querySelectorAll('[data-countdown]');

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        countdowns.forEach(function (el) {
            var diff = Math.max(0, Date.parse(el.getAttribute('data-countdown')) - Date.now());
            var parts = {
                days: Math.floor(diff / 86400000),
                hours: Math.floor((diff % 86400000) / 3600000),
                minutes: Math.floor((diff % 3600000) / 60000),
            };

            Object.keys(parts).forEach(function (unit) {
                var cell = el.querySelector('[data-unit="' + unit + '"]');
                if (cell) { cell.textContent = pad(parts[unit]); }
            });
        });
    }

    if (countdowns.length) {
        tick();
        setInterval(tick, 30000);
    }

    /* Salin ke clipboard, dengan umpan balik singkat di tombol. */
    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-copy]');

        if (!button) { return; }

        var label = button.querySelector('[data-copy-label]') || button;
        var original = button.dataset.originalLabel || (button.dataset.originalLabel = label.textContent);

        function done() {
            button.setAttribute('data-copied', '');
            label.textContent = 'Tersalin ✓';
            clearTimeout(button._copyTimer);
            button._copyTimer = setTimeout(function () {
                button.removeAttribute('data-copied');
                label.textContent = original;
            }, 1800);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(button.getAttribute('data-copy')).then(done, function () {});
        }
    });
})();
