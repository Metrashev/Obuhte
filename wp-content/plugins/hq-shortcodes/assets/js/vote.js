jQuery(document).ready(function ($) {
    $('#wpwrap').before($('.hq-vote').slideDown());
    $('.hq-vote-action').on('click', function (e) {
        var $this = $(this);
        e.preventDefault();
        $.ajax({
            type: 'get',
            url: $this.attr('href'),
            beforeSend: function () {
                $('.hq-vote').slideUp();
                if (typeof $this.data('action') !== 'undefined')
                    window.open($this.data('action'));
            },
            success: function (data) {
            }
        });
    });
});