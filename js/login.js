$(function () {

    setTimeout(function () {
        var $redirect = $('#cas-login-redirect');
        if ($redirect.length > 0) {
            if ($redirect.is('a')) {
                window.location.href = $redirect.attr('href');
            } else if ($redirect.is('form')) {
                $redirect.submit();
            }
        }
    }, 1000);

});
