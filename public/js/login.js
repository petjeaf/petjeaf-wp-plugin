(function($) {
    'use strict';

    /**
     * read cookie
     * @param {Name of the cookie to read} name
     */
    function readCookie(name) {
        var nameEQ = encodeURIComponent(name) + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ')
                c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0)
                return decodeURIComponent(c.substring(nameEQ.length, c.length));
        }
        return null;
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
     * Login with AJAX
     */
    function login() {
        var user = readCookie('auth2user'),
            redirect = readCookie('auth2redirect') ? readCookie('auth2redirect') : '/',
            code = getUrlParameter('code') ? getUrlParameter('code') : null,
            state = getUrlParameter('state') ? getUrlParameter('state') : null

        if (code) {
            $.ajax({
                url: petjeaf_vars.ajaxurl,
                type: 'post',
                data: {
                    action: 'petjeaf_code_for_token',
                    user: user,
                    redirect: redirect,
                    code: code,
                    state: state
                },
                success: function (response) {

                    if (response.success) {
                        window.location = response.data.redirect
                    }

                    if (!response.success) {
                        $('.petjeaf-redirecter').addClass('petjeaf-redirector--error');
                        $('.petjeaf-redirecter__error').text(response.data.message);
                        $('.petjeaf-redirecter__loader').remove();
                    }
                }
            })
        }
    }

    $(function() {
        if (!$('#petjeaf_redirecter').hasClass('petjeaf-redirector--error')) {
            login();
        }
    });

})(jQuery);