/*global $, MapManager*/
$(function () {
    'use strict';
    $('.dropzone').on('success', function(a, b) {
        console.log(a, b);
    });
});