function OmekaMap(mapDivId, center, options) {
    this.mapDivId = mapDivId;
    this.center = center;
    this.options = options;
}

OmekaMap.prototype = {
    infoWindow: null,
    map: null,
    mapDivId: null,
    mapSize: 'small',
    markers: [],
    options: {},
    center: null,
    
    addMarker: function (lat, lng, options, bindHtml)
    {        
        if (!options) {
            options = {};
        }
        options.position = new google.maps.LatLng(lat, lng);
        options.map = this.map;
        var rootURL = location.protocol + '//' + location.host;
        options.icon = rootURL + '/themes/MobileHistorical/images/map-icn.png';
        options.shadow = rootURL + '/themes/MobileHistorical/images/map-icn-shadow.png';
          
          
        var marker = new google.maps.Marker(options);
        
        //if (bindHtml) {
            google.maps.event.addListener(marker, 'click', function () {
                if (infoWindow){ infoWindow.close();}
                infoWindow.setContent("<div style='width:200px'>"+bindHtml+"</div>");
  				//infoWindow.open(this, marker);
                infoWindow.open(marker.getMap(), marker);
            });
            //return marker;
       // }
               
        //this.markers.push(marker);
        return marker;
    },
    
    initMap: function () {
        infoWindow = new google.maps.InfoWindow({
			content: "..."
		});
       // Build the map.
        var mapOptions = {
            zoom: this.center.zoomLevel,
            center: new google.maps.LatLng(this.center.latitude, this.center.longitude),
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            navigationControl: true,
            mapTypeControl: true,
            streetViewControl: false,
            zoomControl: true,
      		zoomControlOptions: {
          		style: google.maps.ZoomControlStyle.SMALL,
          		position: google.maps.ControlPosition.RIGHT_TOP
      		},
        };  
        switch (this.mapSize) {
        case 'small':
            mapOptions.navigationControlOptions = {
                style: google.maps.NavigationControlStyle.SMALL
            };
            break;
        case 'large':
        default:
            mapOptions.navigationControlOptions = {
                style: google.maps.NavigationControlStyle.DEFAULT
            };
        }

        this.map = new google.maps.Map(document.getElementById(this.mapDivId), mapOptions); 

        if (!this.center) {
            alert('Error: The center of the map has not been set!');
            return;
        }

        // Show the center marker if we have that enabled.
        if (this.center.show) {
            this.addMarker(this.center.latitude, 
                           this.center.longitude, 
                           {title: "(" + this.center.latitude + ',' + this.center.longitude + ")"}, 
                           this.center.markerHtml);
        }
    }
};

function OmekaMapBrowse(mapDivId, center, options) {
    var omekaMap = new OmekaMap(mapDivId, center, options);
    jQuery.extend(true, this, omekaMap);
    this.initMap();

    //XML loads asynchronously, so need to call for further config only after it has executed
    this.loadKmlIntoMap(this.options.uri, this.options.params);
}

OmekaMapBrowse.prototype = {
    
    afterLoadItems: function () {
        var listDiv = jQuery('#' + this.options.list);

        if (!listDiv.size()) {
            alert('Error: You have no map links div!');
        } else {
            //Create HTML links for each of the markers
            this.buildListLinks(listDiv);
        }
    },
    
    /* Need to parse KML manually b/c Google Maps API cannot access the KML 
       behind the admin interface */
    loadKmlIntoMap: function (kmlUrl, params) {
        var that = this;
        jQuery.ajax({
            type: 'GET',
            dataType: 'xml',
            url: kmlUrl,
            data: params,
            success: function(data) {
                var xml = jQuery(data);
        
                /* KML can be parsed as:
                    kml - root element
                        Placemark
                            namewithlink
                            description
                            Point - longitude,latitude
                */
                var placeMarks = xml.find('Placemark');
        
                // If we have some placemarks, load them
                if (placeMarks.size()) {
                    // Retrieve the balloon styling from the KML file
                    that.browseBalloon = that.getBalloonStyling(xml);
                
                    // Build the markers from the placemarks
                    jQuery.each(placeMarks, function (index, placeMark) {
                        placeMark = jQuery(placeMark);
                        that.buildMarkerFromPlacemark(placeMark);
                    });
            
                    // We have successfully loaded some map points, so continue setting up the map object
                    return that.afterLoadItems();
                } else {
                    // @todo Elaborate with an error message
                    return false;
                }            
            }
        });        
    },
    
    getBalloonStyling: function (xml) {
        return xml.find('BalloonStyle text').text();        
    },
    
    // Build a marker given the KML XML Placemark data
    // I wish we could use the KML file directly, but it's behind the admin interface so no go
    buildMarkerFromPlacemark: function (placeMark) {
        // Get the info for each location on the map
        var title = placeMark.find('name').text();
        var titleWithLink = placeMark.find('namewithlink').text();
        var body = placeMark.find('description').text();
        var snippet = placeMark.find('Snippet').text();
            
        // Extract the lat/long from the KML-formatted data
        var coordinates = placeMark.find('Point coordinates').text().split(',');
        var longitude = coordinates[0];
        var latitude = coordinates[1];
        
        // Use the KML formatting (do some string sub magic)
        var balloon = this.browseBalloon;
        balloon = balloon.replace('$[namewithlink]', titleWithLink).replace('$[description]', body).replace('$[Snippet]', snippet);

        // Build a marker, add HTML for it
        this.addMarker(latitude, longitude, {title: title}, balloon);
    },
    
    // Calculate the zoom level given the 'range' value
    // Not currently used by this class, but possibly useful
    // http://throwless.wordpress.com/2008/02/23/gmap-geocoding-zoom-level-and-accuracy/
    calculateZoom: function (range, width, height) {
        var zoom = 18 - Math.log(3.3 * range / Math.sqrt(width * width + height * height)) / Math.log(2);
        return zoom;
    },
    
    buildListLinks: function (container) {
        var that = this;
        var list = jQuery('<ul></ul>');
        list.appendTo(container);

        // Loop through all the markers
        jQuery.each(this.markers, function (index, marker) {
            var listElement = jQuery('<li></li>');

            // Make an <a> tag, give it a class for styling
            var link = jQuery('<a></a>');
            link.addClass('item-link');

            // Links open up the markers on the map, clicking them doesn't actually go anywhere
            link.attr('href', 'javascript:void(0);');

            // Each <li> starts with the title of the item            
            link.html(marker.getTitle());

            // Clicking the link should take us to the map
            link.bind('click', {}, function (event) {
                google.maps.event.trigger(marker, 'click');
                that.map.panTo(marker.getPosition()); 
            });     

            link.appendTo(listElement);
            listElement.appendTo(list);
        });
    }
};

function OmekaMapSingle(mapDivId, center, options) {
    var omekaMap = new OmekaMap(mapDivId, center, options);
    jQuery.extend(true, this, omekaMap);
    this.initMap();
}
OmekaMapSingle.prototype = {
    mapSize: 'small'
};

function OmekaMapForm(mapDivId, center, options) {
    var that = this;
    var omekaMap = new OmekaMap(mapDivId, center, options);
    jQuery.extend(true, this, omekaMap);
    this.initMap();
    
    this.formDiv = jQuery('#' + this.options.form.id);       
        
    // Make the map clickable to add a location point.
    google.maps.event.addListener(this.map, 'click', function (event) {
        // If we are clicking a new spot on the map
        if (!that.options.confirmLocationChange || that.markers.length === 0 || confirm('Are you sure you want to change the location of the item?')) {
            var point = event.latLng;
            var marker = that.setMarker(point);
            jQuery('#geolocation_address').val('');
        }
    });
	
    // Make the map update on zoom changes.
    google.maps.event.addListener(this.map, 'zoom_changed', function () {
        that.updateZoomForm();
    });

    // Make the Find By Address button lookup the geocode of an address and add a marker.
    jQuery('#geolocation_find_location_by_address').bind('click', function (event) {
        var address = jQuery('#geolocation_address').val();
        that.findAddress(address);

        //Don't submit the form
        event.stopPropagation();
        return false;
    });
	
    // Make the return key in the geolocation address input box click the button to find the address.
    jQuery('#geolocation_address').bind('keydown', function (event) {
        if (event.which == 13) {
            jQuery('#geolocation_find_location_by_address').click();
            event.stopPropagation();
            return false;
        }
    });

    // Add the existing map point.
    if (this.options.point) {
        this.map.setZoom(this.options.point.zoomLevel);

        var point = new google.maps.LatLng(this.options.point.latitude, this.options.point.longitude);
        var marker = this.setMarker(point);
        this.map.setCenter(marker.getPosition());
    }
}

OmekaMapForm.prototype = {
    mapSize: 'large',
    
    /* Get the geolocation of the address and add marker. */
    findAddress: function (address) {
        var that = this;
        if (!this.geocoder) {
            this.geocoder = new google.maps.Geocoder();
        }    
        this.geocoder.geocode({'address': address}, function (results, status) {
            // If the point was found, then put the marker on that spot
            if (status == google.maps.GeocoderStatus.OK) {
                var point = results[0].geometry.location;

                // If required, ask the user if they want to add a marker to the geolocation point of the address.
                // If so, add the marker, otherwise clear the address.
                if (!that.options.confirmLocationChange || that.markers.length === 0 || confirm('Are you sure you want to change the location of the item?')) {
                    var marker = that.setMarker(point);
                } else {
                    jQuery('#geolocation_address').val('');
                    jQuery('#geolocation_address').focus();
                }
            } else {
                // If no point was found, give us an alert
                alert('Error: "' + address + '" was not found!');
                return null;
            }
        });
    },
    
    /* Set the marker to the point. */   
    setMarker: function (point) {
        var that = this;
        
        // Get rid of existing markers.
        this.clearForm();
        
        // Add the marker
        var marker = this.addMarker(point.lat(), point.lng());
        
        // Pan the map to the marker
        that.map.panTo(point);
        
        //  Make the marker clear the form if clicked.
        google.maps.event.addListener(marker, 'click', function (event) {
            if (!that.options.confirmLocationChange || confirm('Are you sure you want to remove the location of the item?')) {
                that.clearForm();
            }
        });
        
        this.updateForm(point);
        return marker;
    },
    
    /* Update the latitude, longitude, and zoom of the form. */
    updateForm: function (point) {
        var latElement = document.getElementsByName('geolocation[0][latitude]')[0];
        var lngElement = document.getElementsByName('geolocation[0][longitude]')[0];
        var zoomElement = document.getElementsByName('geolocation[0][zoom_level]')[0];
        
        // If we passed a point, then set the form to that. If there is no point, clear the form
        if (point) {
            latElement.value = point.lat();
            lngElement.value = point.lng();
            zoomElement.value = this.map.getZoom();          
        } else {
            latElement.value = '';
            lngElement.value = '';
            zoomElement.value = this.map.getZoom();          
        }        
    },
    
    /* Update the zoom input of the form to be the current zoom on the map. */
    updateZoomForm: function () {
        var zoomElement = document.getElementsByName('geolocation[0][zoom_level]')[0];
        zoomElement.value = this.map.getZoom();
    },
    
    /* Clear the form of all markers. */
    clearForm: function () {
        // Remove the markers from the map
        for (var i = 0; i < this.markers.length; i++) {
            this.markers[i].setMap(null);
        }
        
        // Clear the markers array
        this.markers = [];
        
        // Update the form
        this.updateForm();
    },
    
    /* Resize the map and center it on the first marker. */
    resize: function () {
        google.maps.event.trigger(this.map, 'resize');
        var point;
        if (this.markers.length) {
            var marker = this.markers[0];
            point = marker.getPosition();
        } else {
            point = new google.maps.LatLng(this.center.latitude, this.center.longitude);
        }
        this.map.setCenter(point);
    }
};
