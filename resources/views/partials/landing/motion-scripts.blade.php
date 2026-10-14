<script>
    (function () {
        var root = document.documentElement;
        if (!root.classList.contains('reveal-ready')) {
            return;
        }

        // Stagger: anak [data-reveal] dalam [data-reveal-group] masuk berurutan.
        document.querySelectorAll('[data-reveal-group]').forEach(function (group) {
            group.querySelectorAll('[data-reveal]').forEach(function (el, index) {
                el.style.setProperty('--reveal-delay', (index * 90) + 'ms');
            });
        });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });

        document.querySelectorAll('[data-reveal]').forEach(function (el) {
            observer.observe(el);
        });

        root.setAttribute('data-reveal-ok', '');
    })();
</script>
