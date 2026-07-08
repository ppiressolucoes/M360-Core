(function($){
    'use strict';

    function setPreview($input, url){
        var $preview = $input.closest('td').find('.m360-ads-media-preview');
        if (!$preview.length) { return; }
        $preview.html(url ? '<img src="' + url + '" alt="">' : '');
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
        });

        frame.open();
    });

    $(document).on('change', '.m360-ads-media-url', function(){
        setPreview($(this), $(this).val());
    });

    $(document).on('change', '.m360-ad-size-preset', function(){
        var value = $(this).val();
        if (value === 'custom') { return; }
        if (value === 'responsive') {
            $('#width').val('');
            $('#height').val('');
            return;
        }
        var parts = value.split('x');
        if (parts.length === 2) {
            $('#width').val(parts[0]);
            $('#height').val(parts[1]);
        }
    });
})(jQuery);
