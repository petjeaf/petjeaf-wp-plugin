(function($) {
    'use strict';

    /**
     * Disconnect form Petje.af
     */
    function disconnect() {
        $.ajax({
            url: petjeaf_vars.ajaxurl,
            type: 'post',
            data: {
                action: 'petjeaf_disconnect',
                user: 'yes'
            },
            beforeSend: function() {
                $('.petjeaf-disconnect-button').addClass('petjeaf-button--loading');
                $('.petjeaf-disconnect-button').append('<div class="petjeaf-button__loader"></div>');
            },
            success: function(response) {

                if (response.success) {
                    location.reload();
                }

                if (!response.success) {
                    $('.petjeaf-disconnect-button').removeClass('petjeaf-button--loading');
                    $('.petjeaf-button__loader').remove();
                    $('.petje-af-account').addClass('petje-af-account--error');
                    $('.petje-af-account__error').text(response.data.message);
                }
            }
        })
    }

    $(function() {
        $(".petjeaf-disconnect-button").click(function(event) {
            event.preventDefault();
            disconnect($(this));
        });
    });

})(jQuery);