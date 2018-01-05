/*global window, document*/
var $ = require('jquery');
window.Tether = require('tether');
window.Popper = require('popper.js');
require('bootstrap');
require('bootstrap-datepicker');
require('select2');
window.toastr = require('toastr');
window.Dropzone = require('dropzone');
window.Ajax = require('./ajax');

$(function () {
    'use strict';
    $('.bootstrap-datepicker').datepicker({format: 'dd/mm/yyyy'});
    $('.select2').select2({});
    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();
});