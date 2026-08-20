(function () {
    'use strict';
    var config = window.M360ConsentConfig || {};
    var bootstrap = window.M360ConsentBootstrap || {};
    var namespace = window.M360Consent || {};
    var root = null;
    var launcher = null;
    var categories = ['preferences', 'analytics', 'advertising', 'external_media'];
    var state = Object.assign({ necessary: true, preferences: false, analytics: false, advertising: false, external_media: false }, bootstrap.state || config.state || {});

    function diagnostic(event, detail) {
        if (!config.debug || !window.console || typeof window.console.info !== 'function') { return; }
        window.console.info('[M360 Consent]', event, detail);
    }

    function googleSignals(value) {
        var granted = function (key) { return value[key] ? 'granted' : 'denied'; };
        return { ad_storage: granted('advertising'), ad_user_data: granted('advertising'), ad_personalization: granted('advertising'), analytics_storage: granted('analytics'), functionality_storage: granted('preferences'), personalization_storage: granted('preferences'), security_storage: 'granted' };
    }

    function normalize(value) {
        var next = Object.assign({}, state, { necessary: true });
        categories.forEach(function (key) { if (Object.prototype.hasOwnProperty.call(value || {}, key)) { next[key] = !!value[key]; } });
        return next;
    }

    function statesEqual(left, right) {
        return ['necessary'].concat(categories).every(function (key) { return !!left[key] === !!right[key]; });
    }

    function persist(value, source) {
        var previousState = Object.assign({}, state);
        state = normalize(value);
        if (statesEqual(previousState, state) && config.hasStoredDecision) {
            diagnostic('consent update skipped (unchanged)', { state: state, source: source || 'user_update' });
            closeAll();
            return;
        }
        var payload = { version: 1, updatedAt: new Date().toISOString(), categories: state };
        var secure = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = config.cookieName + '=' + encodeURIComponent(JSON.stringify(payload)) + '; Path=/; Max-Age=' + (Number(config.cookieDays || 180) * 86400) + '; SameSite=Lax' + secure;
        config.hasStoredDecision = true;
        var signals = googleSignals(state);
        if (config.consentModeV2 && typeof window.gtag === 'function') { window.gtag('consent', 'update', signals); }
        var detail = { state: Object.assign({}, state), previousState: previousState, source: source || 'user_update', signals: signals };
        window.dispatchEvent(new CustomEvent('m360:consent:update', { detail: detail }));
        diagnostic('consent update', detail);
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

    window.M360Consent = Object.assign(namespace, {
        getState: function () { return Object.assign({}, state); },
        has: function (category) { return !!state[category]; },
        update: persist,
        openPreferences: openPanel,
        googleSignals: function () { return googleSignals(state); }
    });

    function consumeQueuedUpdates() {
        var queued = window.M360Consent._queue || [];
        window.M360Consent._queue = [];
        queued.forEach(function (value) { persist(value, 'cmp'); });
    }

    function initInterface() {
        root = document.querySelector('[data-m360-consent-root]');
        launcher = document.querySelector('[data-m360-consent-launcher]');
        if (!root) { return; }
        if (!config.hasStoredDecision && config.adapterMode === 'local_foundation') { openBanner(); }
        root.querySelector('[data-m360-consent-accept]').addEventListener('click', function () { persist({ preferences: true, analytics: true, advertising: true, external_media: true }); });
        root.querySelector('[data-m360-consent-reject]').addEventListener('click', function () { persist({ preferences: false, analytics: false, advertising: false, external_media: false }); });
        root.querySelector('[data-m360-consent-manage]').addEventListener('click', openPanel);
        root.querySelector('[data-m360-consent-close]').addEventListener('click', closeAll);
        root.querySelector('[data-m360-consent-form]').addEventListener('submit', function (event) {
            event.preventDefault(); var next = {};
            categories.forEach(function (key) { var input = root.querySelector('[name="' + key + '"]'); next[key] = !!(input && input.checked); }); persist(next);
        });
        if (launcher) { launcher.addEventListener('click', openPanel); }
    }

    diagnostic('bootstrap', { state: Object.assign({}, state), source: bootstrap.source || config.initialSource || 'default', signals: googleSignals(state) });
    consumeQueuedUpdates();

    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initInterface); }
    else { initInterface(); }
}());
