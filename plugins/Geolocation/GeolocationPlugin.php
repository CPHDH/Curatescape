<?php

class GeolocationPlugin extends Omeka_Plugin_AbstractPlugin
{
    const DEFAULT_LOCATIONS_PER_PAGE = 10;
    const DEFAULT_BASEMAP = 'CartoDB.Voyager';
    const DEFAULT_GEOCODER = 'nominatim';

    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'config_form',
        'config',
        'define_acl',
        'define_routes',
        'after_save_item',
        'admin_items_show_sidebar',
        'public_items_show',
        'admin_items_search',
        'public_items_search',
        'items_browse_sql',
        'public_head',
        'admin_head',
        'initialize',
        'contribution_type_form',
        'contribution_save_form'
    );

    protected $_filters = array(
        'admin_navigation_main',
        'public_navigation_main',
        'response_contexts',
        'action_contexts',
        'admin_items_form_tabs',
        'public_navigation_items',
        'api_resources',
        'api_extend_items',
        'exhibit_layouts',
        'api_import_omeka_adapters',
        'item_search_filters'
    );

    public function hookInstall()
    {
        $db = get_db();
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->Location` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `item_id` BIGINT UNSIGNED NOT NULL ,
        `latitude` DOUBLE NOT NULL ,
        `longitude` DOUBLE NOT NULL ,
        `zoom_level` INT NOT NULL ,
        `map_type` VARCHAR( 255 ) NOT NULL ,
        `address` TEXT NOT NULL ,
        INDEX (`item_id`)) ENGINE = InnoDB";
        $db->query($sql);

        set_option('geolocation_default_latitude', '38');
        set_option('geolocation_default_longitude', '-77');
        set_option('geolocation_default_zoom_level', '5');
        set_option('geolocation_per_page', self::DEFAULT_LOCATIONS_PER_PAGE);
        set_option('geolocation_add_map_to_contribution_form', '0');
        set_option('geolocation_default_radius', 10);
        set_option('geolocation_use_metric_distances', '0');
        set_option('geolocation_basemap', self::DEFAULT_BASEMAP);
        set_option('geolocation_geocoder', self::DEFAULT_GEOCODER);
    }

    public function hookUninstall()
    {
        // Delete the plugin options
        delete_option('geolocation_default_latitude');
        delete_option('geolocation_default_longitude');
        delete_option('geolocation_default_zoom_level');
        delete_option('geolocation_per_page');
        delete_option('geolocation_add_map_to_contribution_form');
        delete_option('geolocation_use_metric_distances');
        delete_option('geolocation_link_to_nav');
        delete_option('geolocation_default_radius');
        delete_option('geolocation_basemap');
        delete_option('geolocation_geocoder');
        delete_option('geolocation_auto_fit_browse');
        delete_option('geolocation_mapbox_access_token');
        delete_option('geolocation_mapbox_map_id');
        delete_option('geolocation_cluster');

        // This is for older versions of Geolocation, which used to store a Google Map API key.
        delete_option('geolocation_gmaps_key');

        // Drop the Location table
        $db = get_db();
        $db->query("DROP TABLE IF EXISTS `$db->Location`");
    }

    public function hookUpgrade($args)
    {
        if (version_compare($args['old_version'], '1.1', '<')) {
            // If necessary, upgrade the plugin options
            // Check for old plugin options, and if necessary, transfer to new options
            $options = array('default_latitude', 'default_longitude', 'default_zoom_level', 'per_page');
            foreach($options as $option) {
                $oldOptionValue = get_option('geo_' . $option);
                if ($oldOptionValue != '') {
                    set_option('geolocation_' . $option, $oldOptionValue);
                    delete_option('geo_' . $option);
                }
            }
            delete_option('geo_gmaps_key');
        }
        if (version_compare($args['old_version'], '2.2.3', '<')) {
            set_option('geolocation_default_radius', 10);
        }
        if (version_compare($args['old_version'], '3.0', '<')) {
            delete_option('geolocation_api_key');
            delete_option('geolocation_map_type');
            set_option('geolocation_basemap', self::DEFAULT_BASEMAP);
        }
        if (version_compare($args['old_version'], '3.1', '<')) {
            set_option('geolocation_geocoder', self::DEFAULT_GEOCODER);

            if (get_option('geolocation_basemap') == 'OpenStreetMap.BlackAndWhite') {
                set_option('geolocation_basemap', self::DEFAULT_BASEMAP);
            }
        }
        if (version_compare($args['old_version'], '3.2', '<')) {
            $newMapboxIds = array(
                'mapbox.streets' => 'mapbox/streets-v11',
                'mapbox.outdoors' => 'mapbox/outdoors-v11',
                'mapbox.light' => 'mapbox/light-v10',
                'mapbox.dark' => 'mapbox/dark-v10',
                'mapbox.satellite' => 'mapbox/satellite-v9',
                'mapbox.streets-satellite' => 'mapbox/satellite-streets-v11',
            );

            $oldMapboxId = get_option('geolocation_mapbox_map_id');
            if ($oldMapboxId && isset($newMapboxIds[$oldMapboxId])) {
                set_option('geolocation_mapbox_map_id', $newMapboxIds[$oldMapboxId]);
            }
        }
    }

    /**
     * Shows plugin configuration page.
     */
    public function hookConfigForm($args)
    {
        $view = $args['view'];
        include 'config_form.php';
    }

    /**
     * Saves plugin configuration page.
     *
     * @param array Options set in the config form.
     */
    public function hookConfig($args)
    {
        // Use the form to set a bunch of default options in the db
        set_option('geolocation_default_latitude', $_POST['default_latitude']);
        set_option('geolocation_default_longitude', $_POST['default_longitude']);
        set_option('geolocation_default_zoom_level', $_POST['default_zoom_level']);
        set_option('geolocation_item_map_width', $_POST['item_map_width']);
        set_option('geolocation_item_map_height', $_POST['item_map_height']);
        $perPage = (int)$_POST['per_page'];
        if ($perPage <= 0) {
            $perPage = self::DEFAULT_LOCATIONS_PER_PAGE;
        }
        set_option('geolocation_per_page', $perPage);
        set_option('geolocation_add_map_to_contribution_form', $_POST['geolocation_add_map_to_contribution_form']);
        set_option('geolocation_link_to_nav', $_POST['geolocation_link_to_nav']);
        set_option('geolocation_default_radius', $_POST['geolocation_default_radius']);
        set_option('geolocation_use_metric_distances', $_POST['geolocation_use_metric_distances']);
        set_option('geolocation_basemap', $_POST['basemap']);
        set_option('geolocation_auto_fit_browse', $_POST['auto_fit_browse']);
        set_option('geolocation_mapbox_access_token', $_POST['mapbox_access_token']);
        set_option('geolocation_mapbox_map_id', $_POST['mapbox_map_id']);
        set_option('geolocation_cluster', $_POST['cluster']);
        set_option('geolocation_geocoder', $_POST['geocoder']);
    }

    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('Locations');
        $acl->allow(null, 'Locations');
    }

    public function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $mapRoute = new Zend_Controller_Router_Route('items/map',
                        array('controller' => 'map',
                                'action'     => 'browse',
                                'module'     => 'geolocation'));
        $router->addRoute('items_map', $mapRoute);

        // Trying to make the route look like a KML file so google will eat it.
        // @todo Include page parameter if this works.
        $kmlRoute = new Zend_Controller_Router_Route_Regex('geolocation/map\.kml',
                        array('controller' => 'map',
                                'action' => 'browse',
                                'module' => 'geolocation',
                                'output' => 'kml'));
        $router->addRoute('map_kml', $kmlRoute);
    }

    public function hookAdminHead($args)
    {
        $this->_head();
    }

    public function hookPublicHead($args)
    {
        $this->_head();
    }

    private function _head()
    {
        $pluginLoader = Zend_Registry::get('plugin_loader');
        $geolocation = $pluginLoader->getPlugin('Geolocation');
        $version = $geolocation->getIniVersion();
        queue_css_file('leaflet/leaflet', null, null, 'javascripts', $version);
        queue_css_file('geolocation-marker', null, null, 'css', $version);
        queue_js_file(array('leaflet/leaflet', 'leaflet/leaflet-providers', 'map'), 'javascripts', array(), $version);

        if (get_option('geolocation_cluster')) {
            queue_css_file(array('MarkerCluster', 'MarkerCluster.Default'), null, null,
                'javascripts/leaflet-markercluster', $version);
            queue_js_file('leaflet-markercluster/leaflet.markercluster', 'javascripts', array(), $version);
        }
    }

    public function hookAfterSaveItem($args)
    {
        if (!($post = $args['post'])) {
            return;
        }

        $item = $args['record'];
        // If we don't have the geolocation form on the page, don't do anything!
        if (!isset($post['geolocation'])) {
            return;
        }

        // Find the location object for the item
        $location = $this->_db->getTable('Location')->findLocationByItem($item, true);

        // If we have filled out info for the geolocation, then submit to the db
        $geolocationPost = $post['geolocation'];
        if (!empty($geolocationPost)
            && $geolocationPost['latitude'] != ''
            && $geolocationPost['longitude'] != ''
        ) {
            if (!$location) {
                $location = new Location;
                $location->item_id = $item->id;
            }
            $location->setPostData($geolocationPost);
            $location->save();
        } else {
            // If the form is empty, then we want to delete whatever location is
            // currently stored
            if ($location) {
                $location->delete();
            }
        }
    }

    public function hookAdminItemsShowSidebar($args)
    {
        $view = $args['view'];
        $item = $args['item'];
        $location = $this->_db->getTable('Location')->findLocationByItem($item, true);

        if ($location) {
            $html = ''
                  . '<div class="geolocation panel">'
                  . '<h4>' . __('Geolocation') . '</h4>'
                  . '<div style="margin: 14px 0">'
                  . $view->geolocationMapSingle($item, '100%', '270px' )
                  . '</div></div>';
            echo $html;
        }
    }

    public function hookPublicItemsShow($args)
    {
        $view = $args['view'];
        $item = $args['item'];
        $location = $this->_db->getTable('Location')->findLocationByItem($item, true);

        if ($location) {
            $width = get_option('geolocation_item_map_width') ? get_option('geolocation_item_map_width') : '';
            $height = get_option('geolocation_item_map_height') ? get_option('geolocation_item_map_height') : '300px';
            $html = "<div id='geolocation'>";
            $html .= '<h2>'.__('Geolocation').'</h2>';
            $html .= $view->geolocationMapSingle($item, $width, $height);
            $html .= "</div>";
            echo $html;
        }
    }

    /**
     * Hook to include a form in the admin items search form.
     *
     * @internal Themed partial should go to "my_theme/map".
     */
    public function hookAdminItemsSearch($args)
    {
        $view = $args['view'];
        echo $view->partial('map/advanced-search-partial.php');
    }

    /**
     * Hook to include a form in the admin items search form.
     *
     * @internal Themed partial should go to "my_theme/map".
     */
    public function hookPublicItemsSearch($args)
    {
        $view = $args['view'];
        echo $view->partial('map/advanced-search-partial.php');
    }

    public function hookItemsBrowseSql($args)
    {
        $db = $this->_db;
        $select = $args['select'];
        $alias = $this->_db->getTable('Location')->getTableAlias();
        $isMapped = null;
        if (array_key_exists('geolocation-mapped', $args['params'])
            && $args['params']['geolocation-mapped'] !== ''
        ) {
            $isMapped = (bool) $args['params']['geolocation-mapped'];
        }

        if ($isMapped === true
            || !empty($args['params']['geolocation-address'])
        ) {
            $select->joinInner(
                array($alias => $db->Location),
                "$alias.item_id = items.id",
                array()
            );
        } else if ($isMapped === false) {
            $select->joinLeft(
                array($alias => $db->Location),
                "$alias.item_id = items.id",
                array()
            );
            $select->where("$alias.id IS NULL");
        }
        if (!empty($args['params']['geolocation-address'])) {
            // Get the address, latitude, longitude, and the radius from parameters
            $address = trim($args['params']['geolocation-address']);
            $lat = trim($args['params']['geolocation-latitude']);
            $lng = trim($args['params']['geolocation-longitude']);
            $radius = trim($args['params']['geolocation-radius']);
            // Limit items to those that exist within a geographic radius if an address and radius are provided
            if ($address != ''
                && is_numeric($lat)
                && is_numeric($lng)
                && is_numeric($radius)
            ) {
                // SELECT distance based upon haversine forumula
                if (get_option('geolocation_use_metric_distances')) {
                    $denominator = 111;
                    $earthRadius = 6371;
                } else {
                    $denominator = 69;
                    $earthRadius = 3959;
                }

                $radius = $db->quote($radius, Zend_Db::FLOAT_TYPE);
                $lat = $db->quote($lat, Zend_Db::FLOAT_TYPE);
                $lng = $db->quote($lng, Zend_Db::FLOAT_TYPE);
                $sqlMathExpression = 
                    new Zend_Db_Expr(
                        "$earthRadius * ACOS(
                        COS(RADIANS($lat)) *
                        COS(RADIANS(locations.latitude)) *
                        COS(RADIANS($lng) - RADIANS(locations.longitude))
                        +
                        SIN(RADIANS($lat)) *
                        SIN(RADIANS(locations.latitude))
                        ) AS distance");
                
                $select->columns($sqlMathExpression);

                // WHERE the distance is within radius miles/kilometers of the specified lat & long
                $locationWithinRadius = 
                    new Zend_Db_Expr(
                        "(locations.latitude BETWEEN $lat - $radius / $denominator 
                            AND $lat + $radius / $denominator)
                            AND
                        (locations.longitude BETWEEN $lng - $radius / $denominator 
                            AND $lng + $radius / $denominator)");
                $select->where($locationWithinRadius);

                // Actually use distance calculation.
                //$select->having('distance < radius');

                //ORDER by the closest distances
                $select->order('distance');
            }
        }
    }

    /**
     * Add geolocation search options to filter output.
     *
     * @param array $displayArray
     * @param array $args
     * @return array
     */
    public function filterItemSearchFilters($displayArray, $args)
    {
        $requestArray = $args['request_array'];
        if (!empty($requestArray['geolocation-address']) && !empty($requestArray['geolocation-radius'])) {
            if (get_option('geolocation_use_metric_distances')) {
                $unit = __('kilometers');
            } else {
                $unit = __('miles');
            }
            $displayArray['location'] = __('within %1$s %2$s of "%3$s"',
                $requestArray['geolocation-radius'],
                $unit,
                $requestArray['geolocation-address']
            );
        }
        if (array_key_exists('geolocation-mapped', $requestArray)
            && $requestArray['geolocation-mapped'] !== ''
        ) {
            if ($requestArray['geolocation-mapped']) {
                $displayArray['Geolocation Status'] = __('Only Items with Locations');
            } else {
                $displayArray['Geolocation Status'] = __('Only Items without Locations');
            }
        }
        return $displayArray;
    }

    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
        add_shortcode( 'geolocation', array($this, 'geolocationShortcode'));
    }

    public function filterAdminNavigationMain($navArray)
    {
        $navArray['Geolocation'] = array('label'=>__('Map'), 'uri'=>url('geolocation/map/browse'));
        return $navArray;
    }

    public function filterPublicNavigationMain($navArray)
    {
        $navArray['Geolocation'] = array('label'=>__('Map'), 'uri'=>url('geolocation/map/browse'));
        return $navArray;
    }

    public function filterResponseContexts($contexts)
    {
        $contexts['kml'] = array('suffix'  => 'kml',
                'headers' => array('Content-Type' => 'text/xml'));
        return $contexts;
    }

    public function filterActionContexts($contexts, $args)
    {
        $controller = $args['controller'];
        if ($controller instanceof Geolocation_MapController) {
            $contexts['browse'] = array('kml');
        }
        return $contexts;
    }

    public function filterAdminItemsFormTabs($tabs, $args)
    {
        // insert the map tab before the Miscellaneous tab
        $item = $args['item'];
        $tabs['Map'] = $this->_mapForm($item);

        return $tabs;
    }

    public function filterPublicNavigationItems($navArray)
    {
        if (get_option('geolocation_link_to_nav')) {
            $navArray['Browse Map'] = array(
                'label'=>__('Browse Map'),
                'uri' => url('items/map')
            );
        }
        return $navArray;
    }

    /**
     * Register the geolocations API resource.
     *
     * @param array $apiResources
     * @return array
     */
    public function filterApiResources($apiResources)
    {
        $apiResources['geolocations'] = array(
            'record_type' => 'Location',
            'actions' => array('get', 'index', 'post', 'put', 'delete'),
        );
        return $apiResources;
    }

    /**
     * Add geolocations to item API representations.
     *
     * @param array $extend
     * @param array $args
     * @return array
     */
    public function filterApiExtendItems($extend, $args)
    {
        $item = $args['record'];
        $location = $this->_db->getTable('Location')->findBy(array('item_id' => $item->id));
        if (!$location) {
            return $extend;
        }
        $locationId = $location[0]['id'];
        $extend['geolocations'] = array(
            'id' => $locationId,
            'url' => Omeka_Record_Api_AbstractRecordAdapter::getResourceUrl("/geolocations/$locationId"),
            'resource' => 'geolocations',
        );
        return $extend;
    }

    /**
     * Hook to include a form in a contribution type form.
     *
     * @internal Themed partial should go to "my_theme/contribution/map".
     */
    public function hookContributionTypeForm($args)
    {
        if (get_option('geolocation_add_map_to_contribution_form')) {
            $contributionType = $args['type'];
            $view = $args['view'];
            echo $this->_mapForm(null, __('Find A Geographic Location For The %s:', $contributionType->display_name), false, $view, null);
        }
    }

    public function hookContributionSaveForm($args)
    {
        $this->hookAfterSaveItem($args);
    }

    public function filterExhibitLayouts($layouts)
    {
        $layouts['geolocation-map'] = array(
            'name' => __('Geolocation Map'),
            'description' => __('Show attached items on a map')
        );
        return $layouts;
    }

    public function filterApiImportOmekaAdapters($adapters, $args)
    {
        $geolocationAdapter = new ApiImport_ResponseAdapter_Omeka_GenericAdapter(null, $args['endpointUri'], 'Location');
        $geolocationAdapter->setResourceProperties(array('item' => 'Item'));
        $adapters['geolocations'] = $geolocationAdapter;
        return $adapters;
    }

    public function geolocationShortcode($args)
    {
        static $index = 0;
        $index++;

        $booleanFilter = new Omeka_Filter_Boolean;

        if (isset($args['lat'])) {
            $latitude = $args['lat'];
        } else {
            $latitude  = get_option('geolocation_default_latitude');
        }

        if (isset($args['lon'])) {
            $longitude = $args['lon'];
        } else {
            $longitude = get_option('geolocation_default_longitude');
        }

        if (isset($args['zoom'])) {
            $zoomLevel = $args['zoom'];
        } else {
            $zoomLevel = get_option('geolocation_default_zoom_level');
        }

        $center = array('latitude' => (double) $latitude, 'longitude' => (double) $longitude, 'zoomLevel' => (double) $zoomLevel);

        $options = array();

        if (isset($args['fit'])) {
            $options['fitMarkers'] = $booleanFilter->filter($args['fit']);
        } else {
            $options['fitMarkers'] = '1';
        }

        if (isset($args['type'])) {
            $options['mapType'] = $args['type'];
        }

        if (isset($args['collection'])) {
            $options['params']['collection'] = $args['collection'];
        }

        if (isset($args['tags'])) {
            $options['params']['tags'] = $args['tags'];
        }

        $pattern = '#^[0-9]*(px|%)$#';

        if (isset($args['height']) && preg_match($pattern, $args['height'])) {
            $height = $args['height'];
        } else {
            $height = '436px';
        }

        if (isset($args['width']) && preg_match($pattern, $args['width'])) {
            $width = $args['width'];
        } else {
            $width = '100%';
        }

        $attrs = array('style' => "height:$height;width:$width");
        return get_view()->geolocationMapBrowse("geolocation-shortcode-$index", $options, $attrs, $center);
    }

    /**
     * Returns the form code for geographically searching for items.
     *
     * @param Item $item
     * @param string $label if empty string, a default string will be used. Set
     * null if you don't want a label.
     * @param boolean $confirmLocationChange
     * @param Omeka_View $view
     * @param array $post
     * @return string Html string.
     */
    protected function _mapForm($item, $label = '', $confirmLocationChange = true, $view = null, $post = null)
    {
        $html = '';

        if (is_null($view)) {
            $view = get_view();
        }

        // Need to be translated.
        if ($label == '') {
            $label = __('Find a Location by Address:');
        }
        $center = $this->_getCenter();
        $center['show'] = false;

        $location = $this->_db->getTable('Location')->findLocationByItem($item, true);

        if (is_null($post)) {
            $post = $_POST;
        }

        $usePost = !empty($post)
                    && !empty($post['geolocation'])
                    && $post['geolocation']['longitude'] != ''
                    && $post['geolocation']['latitude'] != '';
        if ($usePost) {
            $lng  = empty($post['geolocation']['longitude']) ? '' : (double) $post['geolocation']['longitude'];
            $lat  = empty($post['geolocation']['latitude']) ? '' : (double) $post['geolocation']['latitude'];
            $zoom = empty($post['geolocation']['zoom_level']) ? '' : (int) $post['geolocation']['zoom_level'];
            $address = html_escape($post['geolocation']['address']);
        } else {
            if ($location) {
                $lng  = (double) $location['longitude'];
                $lat  = (double) $location['latitude'];
                $zoom = (int) $location['zoom_level'];
                $address = html_escape($location['address']);
            } else {
                $lng = $lat = $zoom = $address = '';
            }
        }

        // Prepare javascript.
        $options = array();
        $options['form'] = array('id' => 'location_form',
                'posted' => $usePost);
        if ($location or $usePost) {
            $options['point'] = array(
                'latitude' => $lat,
                'longitude' => $lng,
                'zoomLevel' => $zoom);
            $center = $options['point'];
        }
        $options['confirmLocationChange'] = $confirmLocationChange;
        $options['cluster'] = false;

        return $view->partial('map/input-partial.php', array(
            'label' => $label,
            'address' => $address,
            'center' => $center,
            'options' => $options,
            'lng' => $lng,
            'lat' => $lat,
            'zoom' => $zoom,
        ));
    }

    protected function _getCenter()
    {
        return array(
            'latitude'=>  (double) get_option('geolocation_default_latitude'),
            'longitude'=> (double) get_option('geolocation_default_longitude'),
            'zoomLevel'=> (double) get_option('geolocation_default_zoom_level'),
        );
    }
}
