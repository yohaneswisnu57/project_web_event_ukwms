/* RETINA 2026 — perilaku kecil halaman publik: scroll reveal, hitung mundur, salin nomor rekening, tiket atas namamu. */
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
            label.textContent = 'Tersalin';
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

    /* Tiket atas namamu: nama yang diketik langsung tampil di tiket, lalu dibawa ke formulir. */
    var nameInput = document.querySelector('[data-ticket-name]');
    var holder = document.querySelector('[data-ticket-holder]');
    var ticketLink = document.querySelector('[data-ticket-link]');

    if (nameInput && holder) {
        var baseHref = ticketLink ? ticketLink.getAttribute('href') : null;

        nameInput.addEventListener('input', function () {
            var name = nameInput.value.trim();

            holder.textContent = name || holder.getAttribute('data-placeholder');
            holder.toggleAttribute('data-empty', !name);

            if (ticketLink) {
                ticketLink.setAttribute('href', name ? baseHref + '?nama=' + encodeURIComponent(name) : baseHref);
            }
        });

        holder.setAttribute('data-empty', '');
    }

    /* Tombol daftar di ponsel disembunyikan selama tombol daftar lain sudah terlihat. */
    var dock = document.querySelector('.r-dock');
    var visibleTargets = new Set();

    if (dock && 'IntersectionObserver' in window) {
        var dockObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { visibleTargets.add(entry.target); } else { visibleTargets.delete(entry.target); }
            });
            dock.toggleAttribute('data-hidden', visibleTargets.size > 0);
        });

        document.querySelectorAll('.r-panel--hero .r-btn, .r-band, .r-try .r-btn').forEach(function (el) { dockObserver.observe(el); });
    }
})();
