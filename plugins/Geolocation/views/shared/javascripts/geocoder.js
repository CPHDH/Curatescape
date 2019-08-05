function OmekaGeocoder(geocoder) {
    function photon(query) {
        var url = 'https://photon.komoot.de/api/';
        return jQuery.getJSON(url, {q: query, limit: 1})
            .then(function (data) {
                if (!data.features.length) {
                    return jQuery.Deferred().reject('No results found');
                }

                return data.features[0].geometry.coordinates.reverse();
            });
    }

    function nominatim(query) {
        var url = 'https://nominatim.openstreetmap.org/search';
        return jQuery.getJSON(url, {q: query, limit: 1, format: 'json'})
            .then(function (data) {
                if (!data.length) {
                    return jQuery.Deferred().reject('No results found');
                }

                return [data[0].lat, data[0].lon];
            });
    }

    switch (geocoder) {
        case 'photon':
            this.geocoder = photon;
            break;
        case 'nominatim':
            this.geocoder = nominatim;
            break;
        default:
            console.log('Unknown geocoder specified');
    }
}

OmekaGeocoder.prototype.geocode = function (query) {
    function matchLiteralCoords(query) {
        var latLngMatch = query.trim().match(/^(-?[\d]+(?:\.[\d]+)?)[\s]*[,;][\s]*(-?[\d]+(?:\.[\d]+)?)$/);
        if (latLngMatch && Math.abs(latLngMatch[1]) <= 90 && Math.abs(latLngMatch[2]) <= 180) {
            return [latLngMatch[1], latLngMatch[2]];
        }
        return false;
    }
    var literalCoords = matchLiteralCoords(query);
    if (literalCoords) {
        return jQuery.Deferred().resolve(literalCoords);
    }
    return this.geocoder(query);
};
