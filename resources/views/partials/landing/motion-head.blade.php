{{-- Tanpa JS / reduced motion: semua konten langsung terlihat. State tersembunyi hanya berlaku saat html.reveal-ready. --}}
<script>
    (function () {
        var root = document.documentElement;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
            return;
        }
        root.classList.add('reveal-ready');
        // Jaring pengaman: kalau script reveal gagal jalan, jangan biarkan konten tetap tersembunyi.
        setTimeout(function () {
            if (!root.hasAttribute('data-reveal-ok')) {
                root.classList.remove('reveal-ready');
            }
        }, 3000);
    })();
</script>

<style>
    :root {
        --ease-out: cubic-bezier(0.22, 1, 0.36, 1);
    }

    :focus-visible {
        outline: 2px solid var(--color-wm-red, #a8191f);
        outline-offset: 3px;
    }

    @media (prefers-reduced-motion: no-preference) {
        /* ---- Scroll reveal: satu kali, hanya opacity + transform (+ clip-path) ---- */
        html.reveal-ready [data-reveal] {
            opacity: 0;
            transform: translate3d(0, 24px, 0);
            transition:
                opacity var(--reveal-duration, 600ms) var(--ease-out) var(--reveal-delay, 0ms),
                transform var(--reveal-duration, 600ms) var(--ease-out) var(--reveal-delay, 0ms),
                clip-path var(--reveal-duration, 600ms) var(--ease-out) var(--reveal-delay, 0ms);
        }

        html.reveal-ready [data-reveal="left"]  { transform: translate3d(-20px, 0, 0); }
        html.reveal-ready [data-reveal="fade"]  { transform: none; }
        html.reveal-ready [data-reveal="soft"]  { transform: translate3d(0, 14px, 0); --reveal-duration: 450ms; }
        html.reveal-ready [data-reveal="slow"]  { --reveal-duration: 1000ms; transform: translate3d(0, 32px, 0); }
        html.reveal-ready [data-reveal="wipe"]  {
            --reveal-duration: 900ms;
            opacity: 1;
            transform: none;
            clip-path: inset(100% 0 0 0);
        }
        html.reveal-ready [data-reveal="drift"] {
            --reveal-duration: 1400ms;
            transform: scale(1.06);
        }
        html.reveal-ready [data-reveal="line"] {
            --reveal-duration: 800ms;
            opacity: 1;
            transform: scaleX(0);
            transform-origin: left center;
        }

        html.reveal-ready [data-reveal].is-revealed {
            opacity: 1;
            transform: none;
            clip-path: inset(0 0 0 0);
        }

        /* ---- Banner saat halaman dimuat ---- */
        .wm-hero-in {
            animation: wm-hero-in 1.1s var(--ease-out) both;
        }

        /* ---- Kartu state success / closed muncul saat Livewire mengganti tampilan ---- */
        .wm-pop {
            animation: wm-pop 600ms var(--ease-out) both;
        }

        /* ---- Pesan error validasi ---- */
        .wm-err {
            display: block;
            animation: wm-err 280ms var(--ease-out) both;
        }
    }

    @keyframes wm-hero-in {
        from { opacity: 0; transform: scale(1.03); }
        to   { opacity: 1; transform: none; }
    }
    @keyframes wm-pop {
        from { opacity: 0; transform: translate3d(0, 20px, 0) scale(0.98); }
        to   { opacity: 1; transform: none; }
    }
    @keyframes wm-err {
        from { opacity: 0; transform: translate3d(0, -4px, 0); }
        to   { opacity: 1; transform: none; }
    }
</style>
