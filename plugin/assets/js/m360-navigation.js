(function () {
    'use strict';

    function initStickyNavigation(target) {
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

    function initMobileNavigation(shell) {
        if (!shell || shell.getAttribute('data-m360-mobile-nav-managed') === 'true') { return; }

        var toggle = shell.querySelector('.m360-mobile-nav-toggle');
        var button = shell.querySelector('.m360-mobile-nav-button');
        var navigation = shell.querySelector('.m360-main-navigation');
        if (!toggle || !button || !navigation) { return; }

        shell.setAttribute('data-m360-mobile-nav-managed', 'true');
        var ticking = false;

        function isMobile() {
            return window.matchMedia('(max-width: 1024px)').matches;
        }

        function updatePosition() {
            ticking = false;
            button.setAttribute('aria-expanded', toggle.checked ? 'true' : 'false');

            if (!isMobile() || !toggle.checked) {
                navigation.style.removeProperty('--m360-mobile-nav-top');
                return;
            }

            var header = shell.closest('.m360-header-topbar') || shell;
            var top = Math.max(0, Math.min(window.innerHeight, header.getBoundingClientRect().bottom));
            navigation.style.setProperty('--m360-mobile-nav-top', Math.round(top) + 'px');
        }

        function requestPositionUpdate() {
            if (ticking) { return; }
            ticking = true;
            window.requestAnimationFrame(updatePosition);
        }

        toggle.addEventListener('change', updatePosition);
        window.addEventListener('scroll', requestPositionUpdate, { passive: true });
        window.addEventListener('resize', function () {
            if (!isMobile()) { toggle.checked = false; }
            requestPositionUpdate();
        });
        document.addEventListener('click', function (event) {
            if (toggle.checked && !shell.contains(event.target)) {
                toggle.checked = false;
                updatePosition();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && toggle.checked) {
                toggle.checked = false;
                updatePosition();
                button.focus();
            }
        });

        updatePosition();
    }

    function init() {
        document.querySelectorAll('.m360-header-topbar').forEach(initStickyNavigation);
        document.querySelectorAll('.m360-navigation-shell').forEach(initMobileNavigation);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
}());
