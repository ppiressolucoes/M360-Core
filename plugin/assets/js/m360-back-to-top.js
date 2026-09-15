(function () {
    'use strict';

    function initialize(button) {
        if (!button || button.getAttribute('data-m360-back-to-top-ready') === 'true') { return; }
        button.setAttribute('data-m360-back-to-top-ready', 'true');
        button.hidden = false;

        var ticking = false;
        var animationFrame = 0;

        function update() {
            ticking = false;
            var visible = window.scrollY >= 420;
            button.classList.toggle('is-visible', visible);
            button.setAttribute('aria-hidden', visible ? 'false' : 'true');
            button.tabIndex = visible ? 0 : -1;
        }

        function requestUpdate() {
            if (ticking) { return; }
            ticking = true;
            window.requestAnimationFrame(update);
        }

        function scrollToTop() {
            var start = window.scrollY;
            if (start <= 0) { return; }
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                window.scrollTo(0, 0);
                return;
            }
            if (animationFrame) { window.cancelAnimationFrame(animationFrame); }
            var startedAt = window.performance.now();
            var duration = Math.min(1050, Math.max(680, start * .32));
            function step(now) {
                var progress = Math.min(1, (now - startedAt) / duration);
                var eased = progress < .5
                    ? 16 * Math.pow(progress, 5)
                    : 1 - Math.pow(-2 * progress + 2, 5) / 2;
                window.scrollTo(0, Math.round(start * (1 - eased)));
                animationFrame = progress < 1 ? window.requestAnimationFrame(step) : 0;
            }
            animationFrame = window.requestAnimationFrame(step);
        }

        button.addEventListener('click', scrollToTop);
        window.addEventListener('scroll', requestUpdate, { passive: true });
        window.requestAnimationFrame(function () { window.requestAnimationFrame(update); });
    }

    function init() {
        document.querySelectorAll('[data-m360-back-to-top]').forEach(initialize);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
}());
