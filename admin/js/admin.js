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
            var redirectUri = $(this).data('redirect-uri');

            createCookie('auth2redirect', window.location.href);
            createCookie('auth2user', 'no');

            window.location = redirectUri;
        });
    });

})(jQuery);