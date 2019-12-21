(function($) {
    'use strict';

    function createCookie(name, value, days) {
        var expires = "";
        document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
    }

    function redirect($el) {
        var redirectUri = $el.data('redirect-uri');

        createCookie('auth2redirect', window.location.href);
        createCookie('auth2user', 'yes');

        window.location = redirectUri;
    }

    $(function() {
        $("#petjeaf_login_button").click(function(event) {
            event.preventDefault();
            redirect($(this));
        });

        $("#petjeaf_connect_button").click(function(event) {
            event.preventDefault();
            redirect($(this));
        });
    });

})(jQuery);