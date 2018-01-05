/*global google, document, window, $, navigator*/
module.exports = function (container, coordinates) {
    "use strict";
    var self = this;
    self.map = null;
    self.container = container;
    self.zoom = 4;
    self.center = coordinates;
    self.centerFallback = {"lat": -25.363, "lng": 131.044};
    self.markers = [];
    self.infowindow = null;

    /**
     * Simple map initialisation
     * @param  {string} containerId
     * @param  {boolean} geolocation Initalise on user location or not
     */
    self.initMap = function (containerId, geolocation) {
        geolocation = geolocation || false;
        self.container = containerId || self.container;
        self.map = new google.maps.Map(document.getElementById(self.container), {
            'zoom': self.zoom,
            'center': self.center
        });
        self.infowindow = new google.maps.InfoWindow();

        self.initMapCenter(coordinates);

    };

    self.initMapCenter = function (geocoding) {
        if (!self.validatePosition(self.center)) {
            if (geocoding) {
                self.getUserLocation();
                return;
            }

            self.center = self.centerFallback;
        }

        self.focusOnPosition(self.center);
    };

    self.validatePosition = function (position) {
        return typeof position === "object"
            && $.isNumeric(position.lat || false)
            && $.isNumeric(position.lng || false);
    };

    /**
     * Map and pac input initialisation
     * @param  {string} containerId
     * @param  {string} pacInputId
     * @param  {boolean} geolocation Initalise on user location or not
     */
    self.initMapAndPacInput = function (containerId, pacInputId, geolocation) {
        self.initMap(containerId, geolocation);

        pacInputId = pacInputId || 'pac-input';
        var $input = document.getElementById(pacInputId);
        self.map.controls[google.maps.ControlPosition.TOP_LEFT].push($input);
        self.initAutocomplete($input);
    };

    /**
     * GeocoderType initilisation
     * @param  {string} containerId
     * @param  {string} pacInputId
     * @param  {boolean} geolocation Initalise on user location or not
     */
    self.initGeocoderType = function (containerId, pacInputId, geolocation) {
        geolocation = geolocation || false;
        self.initMapAndPacInput(containerId, pacInputId, geolocation);

        $(document).on("changePlace", function (e) {
            var position = e.coordinates;
            $(".latitude-input").val(position.lat());
            $(".longitude-input").val(position.lng());
        });
    };

    /**
     * Initailise the autocomplete place input
     * @param  {$} $input
     */
    self.initAutocomplete = function ($input) {
        var autocomplete = new google.maps.places.Autocomplete($input);
        autocomplete.bindTo('bounds', self.map);

        autocomplete.addListener('place_changed', function () {
            // remove previous content from the map
            self.clearMarkers();
            self.infowindow.close();

            var place = autocomplete.getPlace(),
                iconDefinition = {
                    'url': place.icon,
                    'size': new google.maps.Size(71, 71),
                    'origin': new google.maps.Point(0, 0),
                    'anchor': new google.maps.Point(17, 34),
                    'scaledSize': new google.maps.Size(35, 35)
                },
                marker = self.addMarker({'position': place.geometry.location, 'icon': iconDefinition});

            if (!place.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place.name + "'");
                return;
            }

            self.focusOnPlace(place);

            self.updateInfowindow(place, marker);

            $.event.trigger({
                type: "changePlace",
                coordinates: place.geometry.location
            });
        });
    };

    /**
     * Adds a marker to the map
     * @param {array} options
     */
    self.addMarker = function (options) {
        var markerOptions = Object.assign({'map': self.map}, options),
            newMarker = new google.maps.Marker(markerOptions);

        self.markers.push(newMarker);

        return newMarker;
    };

    /**
     * Clear all map markers
     */
    self.clearMarkers = function () {
        self.markers.forEach(function (marker) {
            marker.setMap(null);
        });
    };

    /**
     * Show all map markers
     */
    self.showMarkers = function () {
        self.markers.forEach(function (marker) {
            marker.setMap(self.map);
        });
    };

    /**
     * Delete all markers of the map
     */
    self.deleteMarkers = function () {
        self.clearMarkers();
        self.markers = [];
    };

    /**
     * Focus the map on the given Place object
     * @param  {Place} place
     */
    self.focusOnPlace = function (place) {
        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            self.map.fitBounds(place.geometry.viewport);
        } else {
            self.focusOnPosition(place.geometry.location);
        }
    };

    /**
     * Focus on the given Position object
     * @param  {Position} position
     */
    self.focusOnPosition = function (position) {
        self.map.setCenter(position);
        self.map.setZoom(17);
        self.addMarker({'position': position});
    };

    /**
     * Update an infowindow with the data of a Place object and open it above the given Marker
     * @param  {Place} place
     * @param  {Marker} marker
     */
    self.updateInfowindow = function (place, marker) {
        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && (place.address_components[0].short_name || '')),
                (place.address_components[1] && (place.address_components[1].short_name || '')),
                (place.address_components[2] && (place.address_components[2].short_name || ''))
            ].join(' ');
        }
        self.infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        self.infowindow.open(self.map, marker);
    };

    /**
     * Get the user location from navigator
     */
    self.getUserLocation = function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    self.center = pos;

                    self.infowindow.setPosition(pos);
                    self.infowindow.setContent('Vous êtes ici');
                    self.focusOnPosition(pos);
                    self.infowindow.open(self.map);
                },
                function () {
                    self.infowindow.close();
                }
            );
        }
    };

    return self;
};