(function($) {
    'use strict';

    function disconnect() {
        $.ajax({
            url: petjeaf_vars.ajaxurl,
            type: 'post',
            data: {
                action: 'petjeaf_disconnect',
                user: 'yes'
            },
            beforeSend: function() {
                $('.petje-af-account').append('<div class="petje-af-account__loader"></div>');
            },
            success: function(response) {

                if (response.success) {
                    location.reload();
                }

                if (!response.success) {
                    $('.petje-af-account__loader').remove();
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