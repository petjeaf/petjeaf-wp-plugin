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
     * get url parameter
     * @param {The parameter to get} sParam 
     */
    function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
    }

    /**
     * Redirect to the authorize page
     * @param {The element to use as $this} $el 
     */
    function redirect($el) {
        var auth2redirect = getUrlParameter('r') ? getUrlParameter('r') : window.location.href

        createCookie('auth2redirect', auth2redirect)
        createCookie('auth2user', 'yes');

        $.ajax({
            url: petjeaf_vars.ajaxurl,
            type: 'post',
            data: {
                action: 'petjeaf_get_authorize_url'
            },
            beforeSend: function() {
                $('.petjeaf-connect-button').append('<div class="petjeaf-connect-button__loader"></div>');
            },
            success: function(response) {

                if (response.success) {
                    window.location = response.data.redirect_uri;
                }

                if (!response.success) {
                    $('.petjeaf-connect-button__loader').remove();
                    $('.petjeaf-connect-button').after(response.data.message);
                }
            }
        });
    }

    
    $(function() {
        $(".petjeaf-connect-button").click(function(event) {
            event.preventDefault();
            redirect($(this));
        });
    });

})(jQuery);