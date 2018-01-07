/*global $, Ajax, window, toastr*/
$(function () {
    "use strict";
    $('#btn-login').on('click', function () {
        Ajax.submitForm('#form-login', {
            'done': function (response) {
                window.location.replace(response.referer);
            }
        });
    });
});