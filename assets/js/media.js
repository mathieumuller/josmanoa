/*global $, MapManager*/
var MapManager = require('./mapManager');

function getMapCenter() {
    "use strict";
    if ($('.latitude-input').length > 0 && $('.longitude-input').length > 0) {
        var lat = $('.latitude-input').val(),
            lng = $('.longitude-input').val();

        if (null !== lat && null !== lng) {
            return {
                "lat": parseFloat(lat),
                "lng": parseFloat(lng)
            };
        }
    }

    return;
}

$(function () {
    'use strict';
    var map = new MapManager('map', getMapCenter());
    map.initGeocoderType('map', 'pac-input', true);
});