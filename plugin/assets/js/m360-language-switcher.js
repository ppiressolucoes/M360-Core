(function () {
    'use strict';

    var config = window.M360LanguageSwitcherConfig || {};

    function createSwitcher() {
        var wrapper = document.createElement('div');
        wrapper.className = 'm360-floating-lang-toggle';
        wrapper.setAttribute('data-m360-language-switcher', 'automatic');
        wrapper.innerHTML = '<a class="m360-lang-toggle"><span class="m360-lang-toggle__flag" aria-hidden="true"></span><span class="m360-lang-toggle__code"></span></a>';
        document.body.appendChild(wrapper);
        return wrapper.querySelector('.m360-lang-toggle');
    }

    function normalize(link) {
        if (!link || !config.available || !config.url) { return; }
        link.href = config.url;
        link.hreflang = config.locale || '';
        link.title = config.title || '';
        link.setAttribute('aria-label', config.ariaLabel || config.title || '');
        link.setAttribute('data-m360-language-switcher', 'managed');

        var flag = link.querySelector('.m360-lang-toggle__flag, .m360-lang-flag');
        var code = link.querySelector('.m360-lang-toggle__code, .m360-lang-code');
        if (flag) { flag.textContent = config.flag || ''; }
        if (code) { code.textContent = config.code || ''; }

        link.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            window.location.assign(config.url);
        }, true);
    }

    function init() {
        var links = Array.prototype.slice.call(document.querySelectorAll('.m360-lang-toggle'));

        if (!config.available) {
            if (config.singular) {
                links.forEach(function (link) {
                    var wrapper = link.closest('.m360-floating-lang-toggle');
                    (wrapper || link).hidden = true;
                });
            }
            return;
        }

        if (!links.length) { links.push(createSwitcher()); }
        links.forEach(normalize);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
}());
