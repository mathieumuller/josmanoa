/*global $, window, document*/
require('lightgallery');
require('lg-fullscreen');
require('lg-share');
require('lg-autoplay');
require('lg-zoom');
$(function () {
    $("#lightgallery").lightGallery({
        mode: 'lg-slide-circular',
        selector: '.lg-select'
    });
});