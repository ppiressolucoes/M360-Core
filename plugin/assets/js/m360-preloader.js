(function () {
    'use strict';

    var selector = '[data-m360-preloader]';
    var hiding = false;

    function preloader() {
        return document.querySelector(selector);
    }

    function show() {
        var element = preloader();
        if (!element) { return; }
        hiding = false;
        document.documentElement.classList.add('m360-preloader-active');
        element.classList.remove('is-leaving');
        element.setAttribute('aria-hidden', 'false');
    }

    function hide() {
        var element = preloader();
        if (!element || hiding) {
            document.documentElement.classList.remove('m360-preloader-active');
            return;
        }
        hiding = true;
        element.classList.add('is-leaving');
        element.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('m360-preloader-active');
    }

    function eligibleLink(link, event) {
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) { return false; }
        if (link.target && link.target.toLowerCase() !== '_self') { return false; }
        if (link.hasAttribute('download') || link.hasAttribute('data-m360-no-preloader')) { return false; }
        var href = link.getAttribute('href') || '';
        if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0 || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) { return false; }
        try {
            var destination = new URL(link.href, window.location.href);
            if (destination.origin !== window.location.origin) { return false; }
            if (destination.pathname === window.location.pathname && destination.search === window.location.search && destination.hash) { return false; }
        } catch (error) {
            return false;
        }
        return true;
    }

    document.addEventListener('click', function (event) {
        var link = event.target.closest ? event.target.closest('a[href]') : null;
        if (eligibleLink(link, event)) { show(); }
    }, false);

    if (document.readyState === 'complete') {
        hide();
    } else {
        window.addEventListener('load', hide, { once: true });
    }
    window.addEventListener('pageshow', hide);
    window.setTimeout(hide, 4000);
}());
