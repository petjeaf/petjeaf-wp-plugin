(function($) {
    'use strict';

    function createCookie(name, value, days) {
        var expires = "";
        document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
    }

    $(function() {
        $("#petjeaf_connect_button").click( function() {
            var redirectUri = $(this).data('redirect-uri');

            createCookie('auth2redirect', window.location.href);
            createCookie('auth2user', 'no');
            
            window.location = redirectUri;
        });
    });

})(jQuery);