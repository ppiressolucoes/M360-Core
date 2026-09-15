(function () {
    'use strict';

    function initializeSearchWidgets() {
        document.querySelectorAll('[data-m360-search-form]').forEach(function (component) {
            var widget = component.closest('.elementor-widget');
            var container = component.closest('.elementor-widget-container');
            if (component.closest('[data-m360-search-toggle]')) {
                if (widget) { widget.classList.add('m360-search-toggle-widget'); }
                return;
            }
            if (widget) { widget.classList.add('m360-search-widget'); }
            if (container) { container.classList.add('m360-search-widget__container'); }
        });
    }

    function closeToggle(toggle, restoreFocus) {
        var button = toggle.querySelector('.m360-search-toggle__trigger');
        var panel = toggle.querySelector('[data-m360-search-toggle-panel]');
        if (!button || !panel || panel.hidden) { return; }
        button.setAttribute('aria-expanded', 'false');
        panel.classList.remove('is-open');
        if (panel.m360CloseTimer) { window.clearTimeout(panel.m360CloseTimer); }
        panel.m360CloseTimer = window.setTimeout(function () { panel.hidden = true; panel.m360CloseTimer = 0; }, 180);
        if (restoreFocus) { button.focus(); }
    }

    function positionPanel(button, panel) {
        var rect = button.getBoundingClientRect();
        panel.style.top = Math.round(rect.bottom + 10) + 'px';
        panel.style.right = Math.round(Math.max(12, window.innerWidth - rect.right)) + 'px';
    }

    function initializeSearchToggles() {
        document.querySelectorAll('[data-m360-search-toggle]').forEach(function (toggle) {
            if (toggle.getAttribute('data-m360-search-toggle-ready') === 'true') { return; }
            toggle.setAttribute('data-m360-search-toggle-ready', 'true');
            var button = toggle.querySelector('.m360-search-toggle__trigger');
            var panel = toggle.querySelector('[data-m360-search-toggle-panel]');
            if (!button || !panel) { return; }
            button.addEventListener('click', function () {
                var opening = panel.hidden;
                document.querySelectorAll('[data-m360-search-toggle]').forEach(function (other) {
                    if (other !== toggle) { closeToggle(other, false); }
                });
                if (!opening) { closeToggle(toggle, false); return; }
                if (panel.m360CloseTimer) { window.clearTimeout(panel.m360CloseTimer); panel.m360CloseTimer = 0; }
                positionPanel(button, panel);
                panel.hidden = false;
                window.requestAnimationFrame(function () {
                    panel.classList.add('is-open');
                    button.setAttribute('aria-expanded', 'true');
                    var input = panel.querySelector('[data-m360-search-input]');
                    if (input) { input.focus(); }
                });
            });
            panel.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') { closeToggle(toggle, true); }
            });
        });
    }

    function initialize() {
        initializeSearchWidgets();
        initializeSearchToggles();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    document.addEventListener('click', function (event) {
        document.querySelectorAll('[data-m360-search-toggle]').forEach(function (toggle) {
            if (!toggle.contains(event.target)) { closeToggle(toggle, false); }
        });
    });

    window.addEventListener('resize', function () {
        document.querySelectorAll('[data-m360-search-toggle]').forEach(function (toggle) { closeToggle(toggle, false); });
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.closest('[data-m360-search-form]')) { return; }
        var component = form.closest('[data-m360-search-form]');
        var input = form.querySelector('[data-m360-search-input]');
        var message = form.querySelector('[data-m360-search-message]');
        if (!(input instanceof HTMLInputElement)) { return; }
        input.value = input.value.trim();
        var invalid = input.value.length === 0;
        component.classList.toggle('is-invalid', invalid);
        input.setAttribute('aria-invalid', invalid ? 'true' : 'false');
        if (!invalid) { if (message) { message.hidden = true; message.textContent = ''; } return; }
        event.preventDefault();
        if (message) {
            message.textContent = window.m360SearchFormI18n && window.m360SearchFormI18n.empty ? window.m360SearchFormI18n.empty : 'Enter a search term.';
            message.hidden = false;
        }
        input.focus();
    });
})();
