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
    $(function(){ checkSizeWarning(); });
})(jQuery);
