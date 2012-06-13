<?php
define('GOOGLE_MAPS_API_VERSION', '3.x');
define('GEOLOCATION_MAX_LOCATIONS_PER_PAGE', 50);
define('GEOLOCATION_DEFAULT_LOCATIONS_PER_PAGE', 10);

require_once 'Location.php';

// Plugin Hooks
add_plugin_hook('install', 'geolocation_install');
add_plugin_hook('uninstall', 'geolocation_uninstall');
add_plugin_hook('config_form', 'geolocation_config_form');
add_plugin_hook('config', 'geolocation_config');
add_plugin_hook('define_acl', 'geolocation_define_acl');
add_plugin_hook('define_routes', 'geolocation_add_routes');
add_plugin_hook('after_save_form_item', 'geolocation_save_location');
add_plugin_hook('admin_append_to_items_show_secondary', 'geolocation_admin_show_item_map');
add_plugin_hook('public_append_to_items_show', 'geolocation_public_show_item_map');
add_plugin_hook('admin_append_to_advanced_search', 'geolocation_admin_append_to_advanced_search');
add_plugin_hook('public_append_to_advanced_search', 'geolocation_public_append_to_advanced_search');
add_plugin_hook('item_browse_sql', 'geolocation_item_browse_sql');
add_plugin_hook('contribution_append_to_type_form', 'geolocation_append_contribution_form');
add_plugin_hook('contribution_save_form', 'geolocation_save_contribution_form');
add_plugin_hook('public_theme_header', 'geolocation_header');

// Plugin Filters
add_filter('admin_navigation_main', 'geolocation_admin_nav');
add_filter('define_response_contexts', 'geolocation_kml_response_context');
add_filter('define_action_contexts', 'geolocation_kml_action_context');
add_filter('admin_items_form_tabs', 'geolocation_item_form_tabs');
add_filter('public_navigation_items', 'geolocation_public_nav');

// Hook Functions
function geolocation_install()
{    
    $db = get_db();
    $sql = "
    CREATE TABLE IF NOT EXISTS $db->Location (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `item_id` BIGINT UNSIGNED NOT NULL ,
    `latitude` DOUBLE NOT NULL ,
    `longitude` DOUBLE NOT NULL ,
    `zoom_level` INT NOT NULL ,
    `map_type` VARCHAR( 255 ) NOT NULL ,
    `address` TEXT NOT NULL ,
    INDEX (`item_id`)) ENGINE = MYISAM";
    $db->query($sql);
    
    // If necessary, upgrade the plugin options
    geolocation_upgrade_options();
    
    set_option('geolocation_default_latitude', '38');
    set_option('geolocation_default_longitude', '-77');
    set_option('geolocation_default_zoom_level', '5');
    set_option('geolocation_per_page', GEOLOCATION_DEFAULT_LOCATIONS_PER_PAGE);
    set_option('geolocation_add_map_to_contribution_form', '1');
}

function geolocation_uninstall()
{
    // Delete the plugin options
    delete_option('geolocation_default_latitude');
	delete_option('geolocation_default_longitude');
	delete_option('geolocation_default_zoom_level');
	delete_option('geolocation_per_page');
    delete_option('geolocation_add_map_to_contribution_form');
    
    // This is for older versions of Geolocation, which used to store a Google Map API key.
	delete_option('geolocation_gmaps_key');

    // Drop the Location table
	$db = get_db();
	$db->query("DROP TABLE $db->Location");
}

function geolocation_config_form()
{
    // If necessary, upgrade the plugin options
    geolocation_upgrade_options();

	include 'config_form.php';
}

function geolocation_config()
{   
    // Use the form to set a bunch of default options in the db
    set_option('geolocation_default_latitude', $_POST['default_latitude']);
    set_option('geolocation_default_longitude', $_POST['default_longitude']);
    set_option('geolocation_default_zoom_level', $_POST['default_zoomlevel']); 
    set_option('geolocation_item_map_width', $_POST['item_map_width']); 
    set_option('geolocation_item_map_height', $_POST['item_map_height']); 
    $perPage = (int)$_POST['per_page'];
    if ($perPage <= 0) {
        $perPage = GEOLOCATION_DEFAULT_LOCATIONS_PER_PAGE;
    } else if ($perPage > GEOLOCATION_MAX_LOCATIONS_PER_PAGE) {
        $perPage = GEOLOCATION_MAX_LOCATIONS_PER_PAGE;
    }
    set_option('geolocation_per_page', $perPage);
    set_option('geolocation_add_map_to_contribution_form', $_POST['geolocation_add_map_to_contribution_form']);
    set_option('geolocation_link_to_nav', $_POST['geolocation_link_to_nav']);
}

function geolocation_upgrade_options() 
{
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

function geolocation_define_acl($acl)
{
    $acl->allow(null, 'Items', 'modifyPerPage');
}

function geolocation_public_nav($nav)
{
    if (get_option('geolocation_link_to_nav')) {
        $nav['Browse Map'] = uri('items/map');
    }
    return $nav;
}

/**
 * Plugin hook that can manipulate Omeka's routes to allow for new URIs to 
 * access data
 * Currently does the following things:
 *     matches up the URI items/map/:page with MapController::browseAction()
 * Adds a couple of data feeds to render XML for the map (these pages are in the 
 * xml/ directory)
 * @see Zend_Controller_Router_Rewrite
 * @param $router
 * @return void
 **/
function geolocation_add_routes($router)
{
    $mapRoute = new Zend_Controller_Router_Route('items/map/:page', 
                                                 array('controller' => 'map', 
                                                       'action'     => 'browse', 
                                                       'module'     => 'geolocation',
                                                       'page'       => '1'), 
                                                 array('page' => '\d+'));
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

/**
 * Each time we save an item, check the POST to see if we are also saving a 
 * location
 * @return void
 **/
function geolocation_save_location($item)
{
    $post = $_POST;    

    // If we don't have the geolocation form on the page, don't do anything!
    if (!$post['geolocation']) {
        return;
    }
        
    // Find the location object for the item
    $location = geolocation_get_location_for_item($item, true);
    
    // If we have filled out info for the geolocation, then submit to the db
    $geolocationPost = $post['geolocation'][0];
    if (!empty($geolocationPost) && 
        (((string)$geolocationPost['latitude']) != '') && 
        (((string)$geolocationPost['longitude']) != '')) {
        if (!$location) {
            $location = new Location;
            $location->item_id = $item->id;
        }
        $location->saveForm($geolocationPost);
    // If the form is empty, then we want to delete whatever location is 
    // currently stored
    } else {
        if ($location) {
            $location->delete();
        }
    }
}

// Filter Functions
function geolocation_admin_nav($navArray)
{
    $geoNav = array('Map' => uri('geolocation/map/browse'));
    $navArray += $geoNav;
    return $navArray;
}

function geolocation_kml_response_context($context)
{
    $context['kml'] = array('suffix'  => 'kml', 
                            'headers' => array('Content-Type' => 'text/xml'));
    return $context;
}

function geolocation_kml_action_context($context, $controller)
{
    if ($controller instanceof Geolocation_MapController) {
        $context['browse'] = array('kml');
    }
    return $context;
}

function geolocation_get_map_items_per_page()
{
    $itemsPerMap = (int)get_option('geolocation_per_page') or $itemsPerMap = 10;
    return $itemsPerMap;
}

/**
 * Add a Map tab to the edit item page
 * @return array
 **/
function geolocation_item_form_tabs($tabs)
{
    // insert the map tab before the Miscellaneous tab
    $item = get_current_item();
    $ttabs = array();
    foreach($tabs as $key => $html) {
        if ($key == 'Miscellaneous') {
            $ht = '';
            $ht .= geolocation_scripts();
            $ht .= geolocation_map_form($item);
            $ttabs['Map'] = $ht;
        }
        $ttabs[$key] = $html;
    }
    $tabs = $ttabs;
    return $tabs;
}

// Helpers

/**
 * Returns the html for loading the javascripts used by the plugin.
 *
 * @param bool $pageLoaded Whether or not the page is already loaded.  
 * If this function is used with AJAX, this parameter may need to be set to true.
 * @return string
 */
function geolocation_scripts()
{
    $ht = '';
    $ht .= geolocation_load_google_maps();
    $ht .= js('map');
    return $ht;
}

/**
 * Returns the html for loading the Google Maps javascript
 *
 * @param bool $pageLoaded Whether or not the page is already loaded.  
 * If this function is used with AJAX, this parameter may need to be set to true.
 * @return string
 */
function geolocation_load_google_maps()
{
    return '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
}

/**
 * Returns a location (or array of locations) for an item (or array of items)
 * @param array|Item|int $item An item or item id, or an array of items or item ids
 * @param boolean $findOnlyOne Whether or not to return only one location if it exists for the item
 * @return array|Location A location or an array of locations
 **/
function geolocation_get_location_for_item($item, $findOnlyOne = false)
{
    return get_db()->getTable('Location')->findLocationByItem($item, $findOnlyOne);
}

/**
 * Returns the default center point for the Google Map
 * @return array
 **/
function geolocation_get_center()
{
    return array(
        'latitude'=>  (double) get_option('geolocation_default_latitude'), 
        'longitude'=> (double) get_option('geolocation_default_longitude'), 
        'zoomLevel'=> (double) get_option('geolocation_default_zoom_level'));
}

function geolocation_header($request)
{
    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
    if ( ($module == 'geolocation' && $controller == 'map')
      || ($module == 'contribution' && $controller == 'contribution' && $action == 'contribute' && get_option('geolocation_add_map_to_contribution_form') == '1')):
?>
    <!-- Scripts for the Geolocation items/map page -->
    <?php echo geolocation_scripts(); ?>
    
    <!-- Styles for the Geolocation items/map page -->
    <link rel="stylesheet" href="<?php echo css('geolocation-items-map'); ?>" />
    <link rel="stylesheet" href="<?php echo css('geolocation-marker'); ?>" />
    
<?php
    endif;
}

/**
 * Returns html for a google map
 * @param string $divId The id of the div that holds the google map
 * @param array $options Possible options include:
 *     form = 'geolocation'  (provides the prefix for form elements that should 
 *     catch the map coordinates)
 * @return array
 **/
function geolocation_google_map($divId = 'map', $options = array()) {

    $ht = '';
    $ht .= '<div id="' . $divId . '" class="map"></div>';
    
    // Load this junk in from the plugin config
    $center = geolocation_get_center();
    
    // The request parameters get put into the map options
    $params = array();
    if (!isset($options['params'])) {
        $params = array();
    }
    $params = array_merge($params, $_GET);
    
    if ($options['loadKml']) {
        unset($options['loadKml']);
        // This should not be a link to the public side b/c then all the URLs that
        // are generated inside the KML will also link to the public side.
        $options['uri'] = uri('geolocation/map.kml');
    }
    
    // Merge in extra parameters from the controller
    if (Zend_Registry::isRegistered('map_params')) {
        $params = array_merge($params, Zend_Registry::get('map_params'));
    }
        
    // We are using KML as the output format
    $options['params'] = $params;    
        
    $options = js_escape($options);
    $center = js_escape($center);
    
    ob_start();
?>  
    <script type="text/javascript">
    //<![CDATA[
        var <?php echo Inflector::variablize($divId); ?>OmekaMapBrowse = new OmekaMapBrowse(<?php echo js_escape($divId); ?>, <?php echo $center; ?>, <?php echo $options; ?>);
    //]]>
    </script>
<?php
    $ht .= ob_get_contents();
    ob_end_clean();
    return $ht;
}

/**
 * Returns the google map code for an item
 * @param Item $item
 * @param int $width
 * @param int $height
 * @param boolean $hasBalloonForMarker
 * @return string
 **/
function geolocation_google_map_for_item($item = null, $width = '200px', $height = '200px', $hasBalloonForMarker = false, $markerHtmlClassName = 'geolocation_balloon') {  
    if (!$item) {
        $item = get_current_item();
    }      
    $ht = '';
    $divId = "item-map-{$item->id}";
    ob_start();
    if ($hasBalloonForMarker) {
        echo geolocation_marker_style();        
    }
?>
<style type="text/css" media="screen">
    /* The map for the items page needs a bit of styling on it */
    #address_balloon dt {
        font-weight: bold;
    }
    #address_balloon {
        width: 100px;
    }
    #<?php echo $divId;?> {
        width: <?php echo $width; ?>;
        height: <?php echo $height; ?>;
    }
    div.map-notification {
        width: <?php echo $width; ?>;
        height: <?php echo $height; ?>;
        display:block;
        border: 1px dotted #ccc;
        text-align:center;
        font-size: 2em;
    }
</style>
<?php        
    $location = geolocation_get_location_for_item($item, true);
    // Only set the center of the map if this item actually has a location 
    // associated with it
    if ($location) {
        $center['latitude']     = $location->latitude;
        $center['longitude']    = $location->longitude;
        $center['zoomLevel']    = $location->zoom_level;
        $center['show']         = true;
        if ($hasBalloonForMarker) {
            $center['markerHtml']   = geolocation_get_marker_html_for_item($item, $markerHtmlClassName);            
        }
        $center = js_escape($center);
        $options = js_escape($options);
        echo '<div id="' . $divId . '" class="map"></div>';
?>        
        <script type="text/javascript">
        //<![CDATA[
            var <?php echo Inflector::variablize($divId); ?>OmekaMapSingle = new OmekaMapSingle(<?php echo js_escape($divId); ?>, <?php echo $center; ?>, <?php echo $options; ?>);
        //]]>
        </script>
<?php         
    } else {
?>
        <p class="map-notification">This item has no location info associated with it.</p>
<?php
    }
    $ht .= ob_get_contents();
    ob_end_clean();
    return $ht;
}


function geolocation_get_marker_html_for_item($item, $markerHtmlClassName = 'geolocation_balloon')
{
    $titleLink = link_to_item(item('Dublin Core', 'Title', array(), $item), array(), 'show', $item);
    $thumbnailLink = !(item_has_thumbnail($item)) ? '' : link_to_item(item_thumbnail(array(), 0, $item), array(), 'show', $item);
    $description = item('Dublin Core', 'Description', array('snippet'=>150), $item);
    return '<div class="' . $markerHtmlClassName . '"><p class="geolocation_marker_title">' . $titleLink . '</p>' . $thumbnailLink . '<p>' . $description . '</p></div>';
}

/**
 * Returns the form code for geographically searching for items
 * @param Item $item
 * @param int $width
 * @param int $height
 * @return string
 **/
function geolocation_map_form($item, $width = '500px', $height = '410px', $label = 'Find A Location For The Item:', $confirmLocationChange = true,  $post = null) { 	
    $ht = '';
	
    $center = geolocation_get_center();
    $center['show'] = false;

    $location = geolocation_get_location_for_item($item, true);
    
    if ($post === null) {
        $post = $_POST;
    }
        
    $usePost = !empty($post) && !empty($post['geolocation']);
    if ($usePost) {
        $lng  = (double) @$post['geolocation'][0]['longitude'];
        $lat  = (double) @$post['geolocation'][0]['latitude'];
        $zoom = (int) @$post['geolocation'][0]['zoom_level'];
        $addr = @$post['geolocation'][0]['address'];
    } else {
        if ($location) {
            $lng  = (double) $location['longitude'];
            $lat  = (double) $location['latitude'];
            $zoom = (int) $location['zoom_level'];
            $addr = $location['address'];
        } else {
            $lng = $lat = $zoom = $addr = '';
        }
    }
    ob_start();
?>
<style type="text/css" media="screen">
    /* Need a bit of styling for the geocoder balloon */
    #geolocation_find_location_by_address {margin-bottom:18px; float:none;}
    #confirm_address, #wrong_address {background:#eae9db; padding:8px 12px; color: #333; cursor:pointer;}
    #confirm_address:hover, #wrong_address:hover {background:#c60; color:#fff;}
</style>
<div id="location_form">
    <input type="hidden" name="geolocation[0][latitude]" value="<?php echo $lat; ?>" />
    <input type="hidden" name="geolocation[0][longitude]" value="<?php echo $lng; ?>" />
    <input type="hidden" name="geolocation[0][zoom_level]" value="<?php echo $zoom; ?>" />
    <input type="hidden" name="geolocation[0][map_type]" value="Google Maps v<?php echo GOOGLE_MAPS_API_VERSION;  ?>" />
    <label><?php echo html_escape($label); ?></label>
    <input type="text" name="geolocation[0][address]" id="geolocation_address" size="60" value="<?php echo $addr; ?>" />
    <button type="button" name="geolocation_find_location_by_address" id="geolocation_find_location_by_address">Find By Address</button>
    
    <!-- <div id="geolocation-geocoder-confirmation"></div> -->
</div>
<?php
    $options = array();
    $options['form'] = array('id' => 'location_form', 
                             'posted' => $usePost);
    if ($location or $usePost) {
        $options['point'] = array('latitude' => $lat, 
                                  'longitude' => $lng, 
                                  'zoomLevel' => $zoom);
    }
    
    $options['confirmLocationChange'] = $confirmLocationChange;
    
    $center = js_escape($center);
    $options = js_escape($options);
    $divId = 'omeka-map-form';    
?>
    <div id="<?php echo html_escape($divId); ?>" style="width: <?php echo $width; ?>; height: <?php echo $height; ?>;"></div>
    <script type="text/javascript">
        //<![CDATA[
        var anOmekaMapForm = new OmekaMapForm(<?php echo js_escape($divId); ?>, <?php echo $center; ?>, <?php echo $options; ?>);
        jQuery(document).bind('omeka:tabselected', function () {
            anOmekaMapForm.resize();
        });
        //]]>
    </script>
<?php
    $ht .= ob_get_contents();
    ob_end_clean();

    return $ht;
}

/**
 * Returns the html for the marker CSS
 * @return string
 **/
function geolocation_marker_style()
{
    $html = '<link rel="stylesheet" href="'.css("geolocation-marker").'" />';
    return $html;
}

/**
 * Shows a small map on the admin show page in the secondary column
 * @param Item $item
 * @return void
 **/
function geolocation_admin_show_item_map($item)
{
    $html = geolocation_scripts()
          . '<div class="info-panel">'
          . '<h2>Geolocation</h2>'
          . geolocation_google_map_for_item($item,'224px','270px')
          . '</div>';
    echo $html;
}

function geolocation_public_show_item_map($width = null, $height = null, $item = null)
{
    if (!$width) {
        $width = get_option('geolocation_item_map_width') ? get_option('geolocation_item_map_width') : '100%';
    }
    
    if (!$height) {
        $height = get_option('geolocation_item_map_height') ? get_option('geolocation_item_map_height') : '300px';
    }
    
    if (!$item) {
        $item = get_current_item();
    }
    
    $html = geolocation_scripts()
          . '<h3>Geolocation</h3>'
          . geolocation_google_map_for_item($item, $width, $height);

    echo $html;
}

function geolocation_append_contribution_form($contributionType)
{
    if (get_option('geolocation_add_map_to_contribution_form') == '1') {
        $html = '<div id="geolocation_contribution">'
              . geolocation_map_form(null, '500px', '410px', 'Find A Geographic Location For The ' . $contributionType->display_name . ':', false)
              . '</div>'
              . '<script type="text/javascript">'
              . 'jQuery("#contribution-type-form").bind("contribution-form-shown", function () {anOmekaMapForm.resize();});'
              . '</script>';
        echo $html;
    }
}

function geolocation_save_contribution_form($contributionType, $item, $post)
{
    if (get_option('geolocation_add_map_to_contribution_form') == '1') {
        geolocation_save_location($item);
    }
}

function geolocation_item_browse_sql($select, $params)
{
    // It would be nice if the item_browse_sql hook also passed in the request 
    // object.
    if (($request = Omeka_Context::getInstance()->getRequest())) {

        $db = get_db();

        // Get the address, latitude, longitude, and the radius from parameters
        $address = trim($request->getParam('geolocation-address'));
        $currentLat = trim($request->getParam('geolocation-latitude'));
        $currentLng = trim($request->getParam('geolocation-longitude'));
        $radius = trim($request->getParam('geolocation-radius'));

        if ($request->get('only_map_items') || $address != '') {
            //INNER JOIN the locations table
            $select->joinInner(array('l' => $db->Location), 'l.item_id = i.id', 
                array('latitude', 'longitude', 'address'));
        }
        
        // Limit items to those that exist within a geographic radius if an address and radius are provided 
        if ($address != '' && is_numeric($currentLat) && is_numeric($currentLng) && is_numeric($radius)) {
            // SELECT distance based upon haversine forumula
            $select->columns('3956 * 2 * ASIN(SQRT(  POWER(SIN(('.$currentLat.' - l.latitude) * pi()/180 / 2), 2) + COS('.$currentLat.' * pi()/180) *  COS(l.latitude * pi()/180) *  POWER(SIN(('.$currentLng.' -l.longitude) * pi()/180 / 2), 2)  )) as distance');
            // WHERE the distance is within radius miles of the specified lat & long
             $select->where('(latitude BETWEEN '.$currentLat.' - ' . $radius . '/69 AND ' . $currentLat . ' + ' . $radius .  '/69)
             AND (longitude BETWEEN ' . $currentLng . ' - ' . $radius . '/69 AND ' . $currentLng  . ' + ' . $radius .  '/69)');
            //ORDER by the closest distances
            $select->order('distance');
        }
    
        // This would be better as a filter that actually manipulated the 
        // 'per_page' value via this plugin. Until then, we need to hack the 
        // LIMIT clause for the SQL query that determines how many items to 
        // return.
        if ($request->get('use_map_per_page')) {            
            // If the limit of the SQL query is 1, we're probably doing a 
            // COUNT(*)
            $limitCount = $select->getPart(Zend_Db_Select::LIMIT_COUNT);
            if ($limitCount != 1) {                
                $select->reset(Zend_Db_Select::LIMIT_COUNT);
                $select->reset(Zend_Db_Select::LIMIT_OFFSET);
                $pageNum = $request->get('page') or $pageNum = 1;                
                $select->limitPage($pageNum, geolocation_get_map_items_per_page());
            }
        }
    }
}

function geolocation_admin_append_to_advanced_search() 
{   
    // Get the request object
    $request = Omeka_Context::getInstance()->getRequest();
    
    if ($request->getControllerName() == 'map' && $request->getActionName() == 'browse') {
        $html = geolocation_append_to_advanced_search('search');
    } else if ($request->getControllerName() == 'items' && $request->getActionName() == 'advanced-search') {
        $html = geolocation_scripts()
              . geolocation_append_to_advanced_search();
    }
    
    echo $html;
}

function geolocation_public_append_to_advanced_search() 
{
    $html = geolocation_scripts()
          . geolocation_append_to_advanced_search();
    echo $html;
}

function geolocation_append_to_advanced_search($searchFormId = 'advanced-search-form', $searchButtonId = 'submit_search_advanced')
{
    // Get the request object
    $request = Omeka_Context::getInstance()->getRequest();

    // Get the address, latitude, longitude, and the radius from parameters
    $address = trim($request->getParam('geolocation-address'));
    $currentLat = trim($request->getParam('geolocation-latitude'));
    $currentLng = trim($request->getParam('geolocation-longitude'));
    $radius = trim($request->getParam('geolocation-radius'));
    
    if (empty($radius)) {
        $radius = 10; // 10 miles
    }
    
    $ht = '';
    ob_start();
?> 
    <div class="field">
	    <?php echo label('geolocation-address', 'Geographic Address'); ?>
	    <div class="inputs">
	        <?php echo text(array('name'=>'geolocation-address','size' => '40','id'=>'geolocation-address','class'=>'textinput'),$address); ?>
            <?php echo hidden(array('name'=>'geolocation-latitude','id'=>'geolocation-latitude'),$currentLat); ?>
            <?php echo hidden(array('name'=>'geolocation-longitude','id'=>'geolocation-longitude'),$currentLng); ?>
            <?php echo hidden(array('name'=>'geolocation-radius','id'=>'geolocation-radius'),$radius); ?>
	    </div>
	</div>
	
	<div class="field">
		<?php echo label('geolocation-radius','Geographic Radius (miles)'); ?>
		<div class="inputs">
	        <?php echo text(array('name'=>'geolocation-radius','size' => '40','id'=>'geolocation-radius','class'=>'textinput'),$radius); ?>
	    </div>
	</div>
	
	<script type="text/javascript">
	    jQuery(document).ready(function() {
    	    jQuery('#<?php echo $searchButtonId; ?>').click(function(event) {
    	            	        
    	        // Find the geolocation for the address
    	        var address = jQuery('#geolocation-address').val();
                
                if (jQuery.trim(address).length > 0) {
                    var geocoder = new google.maps.Geocoder();	        
                    geocoder.geocode({'address': address}, function(results, status) {
                        // If the point was found, then put the marker on that spot
                		if (status == google.maps.GeocoderStatus.OK) {
                			var gLatLng = results[0].geometry.location;
                	        // Set the latitude and longitude hidden inputs
                	        jQuery('#geolocation-latitude').val(gLatLng.lat());
                	        jQuery('#geolocation-longitude').val(gLatLng.lng());
                            jQuery('#<?php echo $searchFormId; ?>').submit();
                		} else {
                		  	// If no point was found, give us an alert
                		    alert('Error: "' + address + '" was not found!');
                		}
                    });
                    
                    event.stopImmediatePropagation();
        	        return false;
                }                
    	    });
	    });
	</script>
	
<?php
    $ht .= ob_get_contents();
    ob_end_clean();
    return $ht;
}