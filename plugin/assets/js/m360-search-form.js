(function () {
    'use strict';

    function initializeSearchWidgets() {
        document.querySelectorAll('[data-m360-search-form]').forEach(function (component) {
            var widget = component.closest('.elementor-widget');
            var container = component.closest('.elementor-widget-container');
            if (widget) { widget.classList.add('m360-search-widget'); }
            if (container) { container.classList.add('m360-search-widget__container'); }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeSearchWidgets);
    } else {
        initializeSearchWidgets();
    }

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
