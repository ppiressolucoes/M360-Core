(function () {
    'use strict';

    function initStickyNavigation(shell) {
        if (shell.closest('.m360-primary-menu-sticky')) { return; }
        var target = shell.closest('.m360-header-topbar');
        if (!target) { return; }
        if (target.getAttribute('data-m360-sticky-managed') === 'true') { return; }

        target.setAttribute('data-m360-sticky-managed', 'true');
        target.classList.add('m360-core-sticky-target');

        var placeholder = document.createElement('div');
        placeholder.className = 'm360-core-sticky-placeholder';
        placeholder.setAttribute('aria-hidden', 'true');
        target.parentNode.insertBefore(placeholder, target);

        var threshold = 0;
        var ticking = false;

        function adminOffset() {
            var adminBar = document.getElementById('wpadminbar');
            return adminBar ? adminBar.getBoundingClientRect().height : 0;
        }

        function measure() {
            if (!target.classList.contains('m360-is-fixed')) {
                threshold = target.getBoundingClientRect().top + window.scrollY;
                placeholder.style.height = '0px';
            }
        }

        function update() {
            ticking = false;
            var shouldFix = window.scrollY + adminOffset() >= threshold;
            target.classList.toggle('m360-is-fixed', shouldFix);
            target.style.setProperty('--m360-sticky-admin-offset', adminOffset() + 'px');
            placeholder.style.height = shouldFix ? target.getBoundingClientRect().height + 'px' : '0px';
        }

        function requestUpdate() {
            if (ticking) { return; }
            ticking = true;
            window.requestAnimationFrame(update);
        }

        measure();
        update();
        window.addEventListener('scroll', requestUpdate, { passive: true });
        window.addEventListener('resize', function () {
            target.classList.remove('m360-is-fixed');
            measure();
            requestUpdate();
        });
    }

    function init() {
        document.querySelectorAll('.m360-navigation-shell').forEach(initStickyNavigation);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
}());
