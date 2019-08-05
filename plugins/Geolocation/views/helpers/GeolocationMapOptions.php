<?php

/**
 * Helper used to preprocess options passed to the Geolocation JS.
 *
 * Specifies mandatory defaults if they're not present in the given options,
 * and outputs them in a format for use in JS.
 */
class Geolocation_View_Helper_GeolocationMapOptions extends Zend_View_Helper_Abstract
{
    public function geolocationMapOptions($options = array())
    {
        if (!array_key_exists('basemap', $options)) {
            $options['basemap'] = get_option('geolocation_basemap');
        }

        if ($options['basemap'] === 'MapBox') {
            $options['basemapOptions']['accessToken'] = get_option('geolocation_mapbox_access_token');

            $type = isset($options['mapType']) ? $options['mapType'] : null;
            $options['basemapOptions']['id'] = $this->_getMapboxMapId($type);
        }

        if (!array_key_exists('cluster', $options)) {
            $options['cluster'] = (bool) get_option('geolocation_cluster');
        }

        return js_escape($options);
    }

    private function _getMapboxMapId($mapType)
    {
        switch ($mapType) {
            case 'roadmap':
                return 'mapbox.streets';
            case 'satellite':
                return 'mapbox.satellite';
            case 'hybrid':
                return 'mapbox.streets-satellite';
            case 'terrain':
                return 'mapbox.outdoors';
            default:
                // empty case, fallthrough
        }

        $mapId = get_option('geolocation_mapbox_map_id');
        if (!$mapId) {
            $mapId = 'mapbox.streets';
        }
        return $mapId;
    }
}
