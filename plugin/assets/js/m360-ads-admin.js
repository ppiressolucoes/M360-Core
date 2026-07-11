(function($){
    'use strict';

    function sizeFormat(bytes){
        bytes = parseInt(bytes || 0, 10);
        if (!bytes) { return '-'; }
        if (bytes < 1024) { return bytes + ' B'; }
        if (bytes < 1048576) { return Math.round(bytes / 1024) + ' KB'; }
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function fileName(url){
        if (!url) { return '-'; }
        return url.split('/').pop().split('?')[0] || '-';
    }

    function currentPreset(){
        return $('.m360-ad-size-preset').val() || 'custom';
    }

    function checkSizeWarning(){
        var preset = currentPreset();
        var $warning = $('.m360-ads-size-warning');
        if (!preset || preset === 'custom' || preset === 'responsive') {
            $warning.prop('hidden', true);
            return;
        }
        var expected = preset.split('x');
        var width = ($('#width').val() || '').toString();
        var height = ($('#height').val() || '').toString();
        var mismatch = expected.length === 2 && width && height && (width !== expected[0] || height !== expected[1]);
        $warning.prop('hidden', !mismatch);
    }

    function setPreview($input, url){
        var $preview = $input.closest('td').find('.m360-ads-media-preview');
        if (!$preview.length) { return; }
        $preview.html(url ? '<img src="' + url + '" alt="">' : '');
        $('[data-m360-media="file"]').text(fileName(url));
    }

    function updateMeta(attachment){
        var width = attachment.width || '';
        var height = attachment.height || '';
        var mime = attachment.mime || '-';
        var filesize = attachment.filesizeInBytes || attachment.filesize || '';
        $('[data-m360-media="size"]').text(width && height ? width + 'x' + height : '-');
        $('[data-m360-media="filesize"]').text(sizeFormat(filesize));
        $('[data-m360-media="mime"]').text(mime || '-');
        detectPreset(width, height);
        checkSizeWarning();
    }

    function detectPreset(width, height){
        if (!width || !height) { return; }
        var value = width + 'x' + height;
        var $preset = $('.m360-ad-size-preset');
        if ($preset.find('option[value="' + value + '"]').length) {
            $preset.val(value);
        } else {
            $preset.val('custom');
        }
    }

    $(document).on('click', '.m360-ads-media-pick', function(e){
        e.preventDefault();
        var $button = $(this);
        var $input = $button.closest('td').find('.m360-ads-media-url');
        var frame = wp.media({
            title: 'Selecionar criativo do anúncio',
            button: { text: 'Usar este criativo' },
            multiple: false,
            library: { type: 'image' }
        });

        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $input.val(attachment.url).trigger('change');
            $('#width').val(attachment.width || '');
            $('#height').val(attachment.height || '');
            $('#mime').val(attachment.mime || '');
            $('#filesize').val(attachment.filesizeInBytes || attachment.filesize || '');
            if (!$('#alt_text').val()) { $('#alt_text').val(attachment.alt || attachment.title || ''); }
            setPreview($input, attachment.url);
            updateMeta(attachment);
        });

        frame.open();
    });

    $(document).on('change', '.m360-ads-media-url', function(){
        setPreview($(this), $(this).val());
    });

    $(document).on('change', '.m360-ad-size-preset', function(){
        var value = $(this).val();
        if (value === 'custom') { checkSizeWarning(); return; }
        if (value === 'responsive') {
            $('#width').val('');
            $('#height').val('');
            checkSizeWarning();
            return;
        }
        var parts = value.split('x');
        if (parts.length === 2) {
            $('#width').val(parts[0]);
            $('#height').val(parts[1]);
            $('[data-m360-media="size"]').text(value);
        }
        checkSizeWarning();
    });

    $(document).on('change keyup', '#width, #height', checkSizeWarning);
    function filterSlots(){
        var query = ($('#m360-slot-search').val() || '').toString().toLowerCase().trim();
        var context = $('#m360-slot-context').val() || '';
        var runtime = $('#m360-slot-runtime').val() || '';
        var occupancy = $('#m360-slot-occupancy').val() || '';
        var visible = 0;
        $('.m360-slot-card').each(function(){
            var $card = $(this);
            var show = (!query || ($card.data('slot-search') || '').toString().indexOf(query) !== -1) && (!context || $card.data('context') === context) && (!runtime || $card.data('runtime') === runtime) && (!occupancy || $card.data('occupancy') === occupancy);
            $card.prop('hidden', !show);
            if (show) { visible++; }
        });
        $('.m360-slot-group').each(function(){
            var count = $(this).find('.m360-slot-card:not([hidden])').length;
            $(this).prop('hidden', count === 0).find('.m360-slot-group-count').text('(' + count + ')');
        });
        $('#m360-slot-result-count').text(visible + ' slot(s) exibido(s)');
    }

    function updateSlotChanges(){
        var changes = 0;
        $('#m360-slots-bulk-form select[name^="assignments"]').each(function(){
            var $select = $(this);
            var changed = $select.val().toString() !== ($select.data('original') || 0).toString();
            $select.closest('.m360-slot-card').toggleClass('is-changed', changed);
            if (changed) { changes++; }
        });
        $('#m360-slot-change-count').text(changes);
        $('.m360-slot-savebar button[type="submit"]').prop('disabled', changes === 0);
    }

    $(document).on('input change', '#m360-slot-search, #m360-slot-context, #m360-slot-runtime, #m360-slot-occupancy', filterSlots);
    $(document).on('click', '#m360-slot-clear-filters', function(){ $('#m360-slot-search').val(''); $('#m360-slot-context, #m360-slot-runtime, #m360-slot-occupancy').val(''); filterSlots(); });
    $(document).on('change', '#m360-slots-bulk-form select[name^="assignments"]', updateSlotChanges);
    $(document).on('submit', '#m360-slots-bulk-form', function(){ return window.confirm('Salvar todos os vínculos alterados?'); });
    $(function(){ checkSizeWarning(); filterSlots(); updateSlotChanges(); });
})(jQuery);
