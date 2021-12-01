function OmekaMap(mapDivId, center, options) {
    this.mapDivId = mapDivId;
    this.center = center;
    this.options = options;
}

OmekaMap.prototype = {
    
    map: null,
    mapDivId: null,
    markers: [],
    options: {},
    center: null,
    markerBounds: null,
    clusterGroup: null,
    
    addMarker: function (latLng, options, bindHtml)
    {
        var map = this.map;
        var marker = L.marker(latLng, options);

        if (this.clusterGroup) {
            this.clusterGroup.addLayer(marker);
        } else {
            marker.addTo(map);
        }
        
        if (bindHtml) {
            marker.bindPopup(bindHtml, {autoPanPadding: [50, 50]});
            // Fit images on the map on first load
            marker.once('popupopen', function (event) {
                var popup = event.popup;
                var imgs = popup.getElement().getElementsByTagName('img');
                for (var i = 0; i < imgs.length; i++) {
                    imgs[i].addEventListener('load', function imgLoadListener(event) {
                        event.target.removeEventListener('load', imgLoadListener);
                        // Marker autopan is disabled during panning, so defer
                        if (map._panAnim && map._panAnim._inProgress) {
                            map.once('moveend', function () {
                                popup.update();
                            });
                        } else {
                            popup.update();
                        }
                    });
                }
            });
        }
               
        this.markers.push(marker);
        this.markerBounds.extend(latLng);
        return marker;
    },

    fitMarkers: function () {
        if (this.markers.length == 1) {
            this.map.panTo(this.markers[0].getLatLng());
        } else if (this.markers.length > 0) {
            this.map.fitBounds(this.markerBounds, {padding: [25, 25]});
        }
    },
    
    initMap: function () {
        if (!this.center) {
            alert('Error: The center of the map has not been set!');
            return;
        }

        this.map = L.map(this.mapDivId).setView([this.center.latitude, this.center.longitude], this.center.zoomLevel);
        this.markerBounds = L.latLngBounds();

        L.tileLayer.provider(this.options.basemap, this.options.basemapOptions).addTo(this.map);

        if (this.options.cluster) {
            this.clusterGroup = L.markerClusterGroup({
                showCoverageOnHover: false
            });
            this.map.addLayer(this.clusterGroup);
        }

        jQuery(this.map.getContainer()).trigger('o:geolocation:init_map', this);

        // Show the center marker if we have that enabled.
        if (this.center.show) {
            this.addMarker([this.center.latitude, this.center.longitude],
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
        if (this.options.fitMarkers) {
            this.fitMarkers();
        }

        if (!this.options.list) {
            return;
        }
        var listDiv = jQuery('#' + this.options.list);

        if (!listDiv.length) {
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
                if (placeMarks.length) {
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
        this.addMarker([latitude, longitude], {title: title}, balloon);
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
            link.text(marker.options.title);

            // Clicking the link should take us to the map
            link.bind('click', {}, function (event) {
                if (that.clusterGroup) {
                    that.clusterGroup.zoomToShowLayer(marker, function () {
                        marker.fire('click');
                    });
                } else {
                    that.map.once('moveend', function () {
                        marker.fire('click');
                    });
                    that.map.flyTo(marker.getLatLng());
                }
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

function OmekaMapForm(mapDivId, center, options) {
    var that = this;
    var omekaMap = new OmekaMap(mapDivId, center, options);
    jQuery.extend(true, this, omekaMap);
    this.initMap();
    
    this.formDiv = jQuery('#' + this.options.form.id);       
        
    // Make the map clickable to add a location point.
    this.map.on('click', function (event) {
        // If we are clicking a new spot on the map
        var marker = that.setMarker(event.latlng.wrap());
        if (marker) {
            jQuery('#geolocation_address').val('');
        }
    });
	
    // Make the map update on zoom changes.
    this.map.on('zoomend', function () {
        that.updateZoomForm();
    });

    // Add the existing map point.
    if (this.options.point) {
        var point = L.latLng(this.options.point.latitude, this.options.point.longitude);
        this.setMarker(point);
        this.map.setView(point, this.options.point.zoomLevel);
    }
}

OmekaMapForm.prototype = {
    /* Set the marker to the point. */   
    setMarker: function (point) {
        var that = this;

        if (this.options.confirmLocationChange
            && this.markers.length > 0
            && !confirm('Are you sure you want to change the location of the item?')
        ) {
            return false;
        }

        // Get rid of existing markers.
        this.clearForm();
        
        // Add the marker
        var marker = this.addMarker(point);
        
        // Pan the map to the marker
        this.map.panTo(point);
        
        //  Make the marker clear the form if clicked.
        marker.on('click', function (event) {
            if (!that.options.confirmLocationChange || confirm('Are you sure you want to remove the location of the item?')) {
                that.clearForm();
            }
        });
        
        this.updateForm(point);
        return marker;
    },
    
    /* Update the latitude, longitude, and zoom of the form. */
    updateForm: function (point) {
        var latElement = document.getElementsByName('geolocation[latitude]')[0];
        var lngElement = document.getElementsByName('geolocation[longitude]')[0];
        var zoomElement = document.getElementsByName('geolocation[zoom_level]')[0];
        
        // If we passed a point, then set the form to that. If there is no point, clear the form
        if (point) {
            latElement.value = point.lat;
            lngElement.value = point.lng;
            zoomElement.value = this.map.getZoom();          
        } else {
            latElement.value = '';
            lngElement.value = '';
            zoomElement.value = this.map.getZoom();          
        }        
    },
    
    /* Update the zoom input of the form to be the current zoom on the map. */
    updateZoomForm: function () {
        var zoomElement = document.getElementsByName('geolocation[zoom_level]')[0];
        zoomElement.value = this.map.getZoom();
    },
    
    /* Clear the form of all markers. */
    clearForm: function () {
        // Remove the markers from the map
        for (var i = 0; i < this.markers.length; i++) {
            this.markers[i].remove();
        }
        
        // Clear the markers array
        this.markers = [];
        
        // Update the form
        this.updateForm();
    },
    
    /* Resize the map and center it on the first marker. */
    resize: function () {
        this.map.invalidateSize();
    }
};
