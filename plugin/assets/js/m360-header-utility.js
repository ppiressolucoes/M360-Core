(function () {
    'use strict';
    function refreshDates() {
        document.querySelectorAll('[data-m360-header-date]').forEach(function (element) {
            var date = new Date();
            var timezone = element.getAttribute('data-timezone') || 'UTC';
            var locale = element.getAttribute('data-locale') || 'pt-BR';
            var format = element.getAttribute('data-format') || 'full';
            var options = { year: 'numeric', month: format === 'short' ? '2-digit' : (format === 'medium' ? 'short' : 'long'), day: format === 'short' ? '2-digit' : 'numeric', timeZone: timezone };
            if (element.getAttribute('data-weekday') === 'true') { options.weekday = 'long'; }
            try {
                new Intl.DateTimeFormat(locale, options);
            } catch (error) {
                // WordPress also permits a fixed UTC offset instead of an IANA zone.
                date = new Date(date.getTime() + Number(element.getAttribute('data-offset') || 0) * 60000);
                timezone = options.timeZone = 'UTC';
            }
            element.textContent = new Intl.DateTimeFormat(locale, options).format(date);
            var parts = new Intl.DateTimeFormat('en-US', { year: 'numeric', month: '2-digit', day: '2-digit', timeZone: timezone }).formatToParts(date);
            var values = {};
            parts.forEach(function (part) { values[part.type] = part.value; });
            element.setAttribute('datetime', values.year + '-' + values.month + '-' + values.day);
        });
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', refreshDates); }
    else { refreshDates(); }
    window.addEventListener('pageshow', refreshDates);
    document.addEventListener('visibilitychange', function () { if (!document.hidden) { refreshDates(); } });
    window.setInterval(refreshDates, 60000);
}());
