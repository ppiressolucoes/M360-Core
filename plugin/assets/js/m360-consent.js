(function () {
    'use strict';
    var config = window.M360ConsentConfig || {};
    var root = null;
    var launcher = null;
    var state = Object.assign({ necessary: true, preferences: false, analytics: false, advertising: false, external_media: false }, config.state || {});

    function googleSignals(value) {
        var granted = function (key) { return value[key] ? 'granted' : 'denied'; };
        return { ad_storage: granted('advertising'), ad_user_data: granted('advertising'), ad_personalization: granted('advertising'), analytics_storage: granted('analytics'), functionality_storage: granted('preferences'), personalization_storage: granted('preferences'), security_storage: 'granted' };
    }

    function persist(value) {
        state = Object.assign({}, state, value, { necessary: true });
        var payload = { version: 1, updatedAt: new Date().toISOString(), categories: state };
        var secure = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = config.cookieName + '=' + encodeURIComponent(JSON.stringify(payload)) + '; Path=/; Max-Age=' + (Number(config.cookieDays || 180) * 86400) + '; SameSite=Lax' + secure;
        if (config.consentModeV2 && typeof window.gtag === 'function') { window.gtag('consent', 'update', googleSignals(state)); }
        window.dispatchEvent(new CustomEvent('m360:consent:update', { detail: { state: Object.assign({}, state) } }));
        closeAll();
    }

    function closeAll() {
        if (!root) { return; }
        root.hidden = true;
        root.querySelector('.m360-consent__banner').hidden = false;
        root.querySelector('.m360-consent__panel').hidden = true;
    }

    function openBanner() { if (root) { root.hidden = false; root.querySelector('.m360-consent__banner').hidden = false; root.querySelector('.m360-consent__panel').hidden = true; } }
    function openPanel() {
        if (!root) { return; }
        root.hidden = false; root.querySelector('.m360-consent__banner').hidden = true; root.querySelector('.m360-consent__panel').hidden = false;
        Object.keys(state).forEach(function (key) { var input = root.querySelector('[name="' + key + '"]'); if (input && !input.disabled) { input.checked = !!state[key]; } });
    }

    window.M360Consent = {
        getState: function () { return Object.assign({}, state); },
        has: function (category) { return !!state[category]; },
        update: persist,
        openPreferences: openPanel
    };

    window.addEventListener('m360:cmp:consent', function (event) {
        var detail = event && event.detail ? event.detail : {};
        var categories = detail.categories || detail;
        if (categories && typeof categories === 'object') { persist(categories); }
    });

    function initInterface() {
        root = document.querySelector('[data-m360-consent-root]');
        launcher = document.querySelector('[data-m360-consent-launcher]');
        if (!root) { return; }
        var hasDecision = document.cookie.split('; ').some(function (part) { return part.indexOf(config.cookieName + '=') === 0; });
        if (!hasDecision && config.adapterMode === 'local_foundation') { openBanner(); }
        root.querySelector('[data-m360-consent-accept]').addEventListener('click', function () { persist({ preferences: true, analytics: true, advertising: true, external_media: true }); });
        root.querySelector('[data-m360-consent-reject]').addEventListener('click', function () { persist({ preferences: false, analytics: false, advertising: false, external_media: false }); });
        root.querySelector('[data-m360-consent-manage]').addEventListener('click', openPanel);
        root.querySelector('[data-m360-consent-close]').addEventListener('click', closeAll);
        root.querySelector('[data-m360-consent-form]').addEventListener('submit', function (event) {
            event.preventDefault(); var next = {};
            ['preferences', 'analytics', 'advertising', 'external_media'].forEach(function (key) { var input = root.querySelector('[name="' + key + '"]'); next[key] = !!(input && input.checked); }); persist(next);
        });
        if (launcher) { launcher.addEventListener('click', openPanel); }
    }

    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initInterface); }
    else { initInterface(); }
}());
