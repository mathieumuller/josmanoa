/*global $, toastr*/
module.exports = {
    request: function (url, data, method, callbacks) {
        method = method || "POST";
        data = data || {};
        callbacks = callbacks || {};

        $.ajax(
            {
                url: url,
                type: method,
                data: data,
                cache: false
            }
        ).done(
            function (response, status, xhr) {
                if (response.notify && response.status) {
                    toastr[response.status](response.message);
                }
                if (typeof callbacks.done === 'function') {
                    callbacks.done(response, status, xhr);
                }
            }
        ).fail(
            function (xhr, status, error) {
                toastr.error(error);
                if (typeof callbacks.fail === 'function') {
                    callbacks.fail(error, status, xhr);
                }
            }
        ).always(
            function (response, status, xhr) {
                if (typeof callbacks.always === 'function') {
                    callbacks.always(response, status, xhr);
                }
            }
        );
    },

    post: function (url, data, callbacks) {
        this.request(url, data, "POST", callbacks);
    },

    get: function (url, data, callbacks) {
        this.request(url, data, "GET", callbacks);
    },

    delete: function (url, data, callbacks) {
        this.request(url, data, "DELETE", callbacks);
    },

    put: function (url, data, callbacks) {
        this.request(url, data, "PUT", callbacks);
    },

    submitForm: function (selector, callbacks) {
        var $form = $(selector);
        if ($form !== 'undefined') {
            this.request(
                $form.attr('action'),
                $form.serialize(),
                $form.attr('method'),
                callbacks
            );
        }
    }
};