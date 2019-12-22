(function($) {
    'use strict';

    /**
     * Create a cookie
     * @param {name of the cookie to create} name 
     * @param {value of the cookie to create} value 
     * @param {number of days before the cookie expires} days 
     */
    function createCookie(name, value, days) {
        var expires = "";
        document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
    }

    /**
     * Connect to Petje.af on button click
     */
    $(function() {
        $("#petjeaf_connect_button").click( function() {
            createCookie('auth2redirect', window.location.href);
            createCookie('auth2user', 'no');

            $.ajax({
                url: petjeaf_vars.ajaxurl,
                type: 'post',
                data: {
                    action: 'petjeaf_get_authorize_url'
                },
                beforeSend: function() {
                    $('#petjeaf_connect_button').append('<div class="petjeaf-connect-button__loader"></div>');
                },
                success: function(response) {
    
                    if (response.success) {
                        window.location = response.data.redirect_uri;
                    }
    
                    if (!response.success) {
                        $('.petjeaf-connect-button__loader').remove();
                        $('#petjeaf_connect_button').after(response.data.message);
                    }
                }
            });
        });
    });

})(jQuery);