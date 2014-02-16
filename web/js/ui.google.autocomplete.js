(function($, undefined) {
    $.ui.googleautocomplete = {
        _init: function() {
            var self = this;
            this.element.addClass('error');
            this.element.data('cache', {});
            this.element.data('geocoder', new google.maps.Geocoder);
            this.element.autocomplete({
                minLength: this.options.minLength,
                source: function(request, response) { 
                    self._lookup(request, response);
                },
                select: function(event, ui) {
                    if (self.options.streetselector !== null) {
                        self._setAddressComponent(
                            ui.item.result.address_components, 
                            'route', 
                            true, 
                            self.options.streetselector);
                    }
                    if (self.options.numberselector !== null) {
                        self._setAddressComponent(
                            ui.item.result.address_components, 
                            'street_number', 
                            true, 
                            self.options.numberselector);
                    }
                    if (self.options.zipselector !== null) {
                        self._setAddressComponent(
                            ui.item.result.address_components, 
                            'postal_code', 
                            true, 
                            self.options.zipselector);
                    }
                    if (self.options.cityselector !== null) {
                        self._setAddressComponent(
                            ui.item.result.address_components, 
                            'locality', 
                            true, 
                            self.options.cityselector);
                    }
                    if (self.options.regionselector !== null) {
                        self._setAddressComponent(
                            ui.item.result.address_components, 
                            'administrative_area_level_1', 
                            true, 
                            self.options.regionselector);
                    }
                    if (self.options.countryselector !== null) {
                        self._setAddressComponent(
                            ui.item.result.address_components, 
                            'country', 
                            false, 
                            self.options.countryselector);
                    }
                    if (self.options.latitudeselector !== null) {
                        $(self.options.latitudeselector).val(ui.item.result.geometry.location.lat());
                    }
                    if (self.options.longitudeselector !== null) {
                        $(self.options.longitudeselector).val(ui.item.result.geometry.location.lng());
                    }
                    self._setMap(ui.item.result.geometry.location);
                }
            });
            this.element.data('autocomplete').renderItem = function(ul, item) {
                return $('<li></li>').data('item.autocomplete', item).append(item.label).appendTo(ul);
            };
        },
        _setAddressComponent: function(components, type, shorttext, selector) {
            this.element.removeClass('error');
            jQuery(components).each(function(key, value){
                if ($.inArray(type, value.types) !== -1) {
                    $(selector).val(shorttext ? value.short_name : value.long_name);
                    return;
                }
            });
        },
        _setMap: function(location) {
            var latlng = new google.maps.LatLng(location.lat(), location.lng());
            var options = {
                zoom: 8,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(jQuery(this.options.mapcontainer)[0], options);
            map.setCenter(location);
            new google.maps.Marker({
                map: map,
                position: location
            });
        },
        _lookup: function(request, response) {
            var cache = $(this.element).data('cache');
            if (request.term in cache) {
                response(cache[request.term]);
            }
            else {
                var self = this;
                var address = request.term;
                var geocoder = $(this.element).data('geocoder');
                geocoder.geocode(
                    {'address': address}, 
                    function(results, status) {
                        var parsed = [];
                        if (results && status && status == 'OK') {
                            var types = self.options.geocoder_types.split(',');
                            $.each(results, function(key, result) {
                                // if this is an acceptable location type with a viewport, it's a good result
                                if ($.map(result.types, function(type) {
                                    return $.inArray(type, types) != -1 ? type : null;
                                }).length && result.geometry && result.geometry.viewport) {
                                    // place is first matching segment, or first segment
                                    var place_parts = result.formatted_address.split(',');
                                    var place = place_parts[0];
                                    $.each(place_parts, function(key, part) {
                                        if (part.toLowerCase().indexOf(request.term.toLowerCase()) != -1) {
                                            place = $.trim(part);
                                            return false; // break
                                        }
                                    });
                                }
                                parsed.push({
                                        value: place,
                                        result: result,
                                        label: result.formatted_address,
                                        viewport: result.geometry.viewport,
                                        longitude: result.geometry.location.lng(),
                                        latitude: result.geometry.location.lat()
                                });
                            });
                        }
                        cache[request.term] = parsed;
                        response(parsed);
                    }
                );
            }
        },
        options: {
            geocoder_types: 'locality,political,sublocality,neighborhood,country', // https://developers.google.com/maps/documentation/javascript/geocoding#GeocodingAddressTypes
            minLength: 3,
            streetselector: null,
            numberselector: null,
            zipselector: null,
            cityselector: null,
            regionselector: null,
            countryselector: null,
            longitudeselector: null,
            latitudeselector: null,
            mapcontainer: null,
            usemap: false
        }
    };
    $.widget('ui.googleautocomplete', $.ui.googleautocomplete);
})(jQuery);
