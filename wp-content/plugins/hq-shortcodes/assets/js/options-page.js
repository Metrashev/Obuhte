// Wait DOM
jQuery(document).ready(function ($) {

    // ########## About screen ##########

    $('.hq-demo-video').magnificPopup({
        type: 'iframe',
        callbacks: {
            open: function () {
                // Change z-index
                $('body').addClass('hq-mfp-shown');
            },
            close: function () {
                // Change z-index
                $('body').removeClass('hq-mfp-shown');
            }
        }
    });

    // ########## Custom CSS screen ##########

    $('.hq-custom-css-originals a').magnificPopup({
        type: 'iframe',
        callbacks: {
            open: function () {
                // Change z-index
                $('body').addClass('hq-mfp-shown');
            },
            close: function () {
                // Change z-index
                $('body').removeClass('hq-mfp-shown');
            }
        }
    });

    // Enable ACE editor
    if ($('#sunrise-field-custom-css-editor').length > 0) {
        var editor = ace.edit('hqnrise-field-custom-css-editor'),
                $textarea = $('#sunrise-field-custom-css').hide();
        editor.getSession().setValue($textarea.val());
        editor.getSession().on('change', function () {
            $textarea.val(editor.getSession().getValue());
        });
        editor.getSession().setMode('ace/mode/css');
        editor.setTheme('ace/theme/monokai');
        editor.getSession().setUseWrapMode(true);
        editor.getSession().setWrapLimitRange(null, null);
        editor.renderer.setShowPrintMargin(null);
        editor.session.setUseSoftTabs(null);
    }

    // ########## Add-ons screen ##########

    var addons_timer = 0;
    $('.hq-addons-item').each(function () {
        var $item = $(this),
                delay = 300;
        $item.click(function (e) {
            window.open($(this).data('url'));
            e.preventDefault();
        });
        addons_timer = addons_timer + delay;
        window.setTimeout(function () {
            $item.addClass('animated bounceIn').css('visibility', 'visible');
        }, addons_timer);
    });

    // ########## Examples screen ##########

    // Disable all buttons
    $('#hq-examples-preview').on('click', '.hq-button', function (e) {
        if ($(this).hasClass('hq-example-button-clicked'))
            alert(hq_options_page.not_clickable);
        else
            $(this).addClass('hq-example-button-clicked');
        e.preventDefault();
    });

    var examples_timer = 0,
            open = $('#hq_open_example').val(),
            $example_window = $('#hq-examples-window'),
            $example_preview = $('#hq-examples-preview');
    $('.hq-examples-group-title, .hq-examples-item').each(function () {
        var $item = $(this),
                delay = 200;
        if ($item.hasClass('hq-examples-item')) {
            $item.on('click', function (e) {
                var code = $(this).data('code'),
                        id = $(this).data('id');
                $item.magnificPopup({
                    type: 'inline',
                    alignTop: true,
                    callbacks: {
                        open: function () {
                            // Change z-index
                            $('body').addClass('hq-mfp-shown');
                        },
                        close: function () {
                            // Change z-index
                            $('body').removeClass('hq-mfp-shown');
                            $example_preview.html('');
                        }
                    }
                });
                var hq_example_preview = $.ajax({
                    url: ajaxurl,
                    type: 'get',
                    dataType: 'html',
                    data: {
                        action: 'hq_example_preview',
                        code: code,
                        id: id
                    },
                    beforeSend: function () {
                        if (typeof hq_example_preview === 'object')
                            hq_example_preview.abort();
                        $example_window.addClass('hq-ajax');
                        $item.magnificPopup('open');
                    },
                    success: function (data) {
                        $example_preview.html(data);
                        $example_window.removeClass('hq-ajax');
                    }
                });
                e.preventDefault();
            });
            // Open preselected example
            if ($item.data('id') === open)
                $item.trigger('click');
        }
        examples_timer = examples_timer + delay;
        window.setTimeout(function () {
            $item.addClass('animated fadeInDown').css('visibility', 'visible');
        }, examples_timer);
    });
    $('#hq-examples-window').on('click', '.hq-examples-get-code', function (e) {
        $(this).hide();
        $(this).parent('.hq-examples-code').children('textarea').slideDown(300);
        e.preventDefault();
    });

    // ########## Cheatsheet screen ##########
    $('.hq-cheatsheet-switch').on('click', function (e) {
        $('body').toggleClass('hq-print-cheatsheet');
        e.preventDefault();
    });
});