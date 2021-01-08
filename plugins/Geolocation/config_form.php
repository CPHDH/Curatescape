<?php $view = get_view(); ?>
<fieldset>
<legend><?php echo __('General Settings'); ?></legend>

<div class="field">
    <div class="two columns alpha">
        <label for="default_latitude"><?php echo __('Default Latitude'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __("Latitude of the map's initial center point, in degrees. Must be between -90 and 90."); ?></p>
        <?php echo $view->formText('default_latitude', get_option('geolocation_default_latitude')); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="default_longitude"><?php echo __('Default Longitude'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __("Longitude of the map's initial center point, in degrees. Must be between -180 and 180.");?></p>
        <?php echo $view->formText('default_longitude', get_option('geolocation_default_longitude')); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="default_zoom_level"><?php echo __('Default Zoom Level'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('An integer greater than or equal to 0, where 0 represents the most zoomed out scale.'); ?></p>
        <?php echo $view->formText('default_zoom_level', get_option('geolocation_default_zoom_level')); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="basemap"><?php echo __('Base Map'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('The type of map to display'); ?></p>
        <?php
        echo $view->formSelect('basemap', get_option('geolocation_basemap'), array(), array(
            __('OpenStreetMap') => array(
                'OpenStreetMap' => __('Standard'),
                'OpenStreetMap.HOT' => __('Humanitarian'),
            ),
            __('OpenTopoMap') => array(
                'OpenTopoMap' => __('OpenTopoMap'),
            ),
            __('Stamen') => array(
                'Stamen.Toner' => __('Toner'),
                'Stamen.TonerBackground' => __('Toner (background)'),
                'Stamen.TonerLite' => __('Toner (lite)'),
                'Stamen.Watercolor' => __('Watercolor'),
                'Stamen.Terrain' => __('Terrain'),
                'Stamen.TerrainBackground' => __('Terrain (background)'),
            ),
            __('Esri') => array(
                'Esri.WorldStreetMap' => __('World Street Map'),
                'Esri.DeLorme' => __('DeLorme'),
                'Esri.WorldTopoMap' => __('World Topographic Map'),
                'Esri.WorldImagery' => __('World Imagery'),
                'Esri.WorldTerrain' => __('World Terrain'),
                'Esri.WorldShadedRelief' => __('World Shaded Relief'),
                'Esri.WorldPhysical' => __('World Physical Map'),
                'Esri.OceanBasemap' => __('Ocean Basemap'),
                'Esri.NatGeoWorldMap' => __('National Geographic World Map'),
                'Esri.WorldGrayCanvas' => __('Light Gray Canvas'),
            ),
            __('CartoDB') => array(
                'CartoDB.Voyager' => __('Voyager'),
                'CartoDB.VoyagerNoLabels' => __('Voyager (no labels)'),
                'CartoDB.Positron' => __('Positron'),
                'CartoDB.PositronNoLabels' => __('Positron (no labels)'),
                'CartoDB.DarkMatter' => __('Dark Matter'),
                'CartoDB.DarkMatterNoLabels' => __('Dark Matter (no labels)'),
            ),
            __('Mapbox') => array(
                'MapBox' => __('Mapbox (see settings below)')
            ),
        ));
        ?>
    </div>
</div>

<div class="field mapbox-settings">
    <div class="two columns alpha">
        <label for="mapbox_access_token"><?php echo __('Mapbox Access Token'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
        <?php
        echo __('Mapbox access token. A token is required when Mapbox is selected as the basemap. Get your token at %s.',
            '<a target="_blank" href="https://www.mapbox.com/account/access-tokens/">https://www.mapbox.com/account/access-tokens/</a>'

        );
        ?>
        </p>
        <?php echo $view->formText('mapbox_access_token', get_option('geolocation_mapbox_access_token')); ?>
    </div>
</div>
<div class="field mapbox-settings">
    <div class="two columns alpha">
        <label for="mapbox_map_id"><?php echo __('Mapbox Map ID'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
        <?php
        echo __('Mapbox Map ID for the map to display as the basemap. The default street map will be used if nothing is entered here.');
        ?>
        </p>
        <?php echo $view->formText('mapbox_map_id', get_option('geolocation_mapbox_map_id')); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="geocoder"><?php echo __('Geocoder'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
            <?php echo __('Service to use for looking up coordinates from addresses.'); ?>
        </p>
        <?php
        echo $view->formSelect('geocoder', get_option('geolocation_geocoder'), array(), array(
            'nominatim' => __('OpenStreetMap Nominatim'),
            'photon' => __('Photon'),
        ));
        ?>
    </div>
</div>
</fieldset>

<fieldset>
<legend><?php echo __('Browse Map Settings'); ?></legend>
<div class="field">
    <div class="two columns alpha">
        <label for="per_page"><?php echo __('Number of Locations Per Page'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('The number of locations displayed per page when browsing the map.'); ?></p>
        <?php echo $view->formText('per_page', get_option('geolocation_per_page')); ?>
    </div>
</div>
<div class="field">
    <div class="two columns alpha">
        <label for="auto_fit_browse"><?php echo __('Auto-fit to Locations'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
            <?php echo __('If checked, the default location and zoom settings '
                . 'will be ignored on the browse map. Instead, the map will '
                . 'automatically pan and zoom to fit the locations displayed '
                . 'on each page.');
            ?>
        </p>
        <?php
        echo $view->formCheckbox('auto_fit_browse', true,
            array('checked' => (boolean) get_option('geolocation_auto_fit_browse')));
        ?>
    </div>
</div>
<div class="field">
    <div class="two columns alpha">
        <label for="per_page"><?php echo __('Default Radius'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('The size of the default radius to use on the items advanced search page. See below for whether to measure in miles or kilometers.'); ?></p>
        <?php echo $view->formText('geolocation_default_radius', get_option('geolocation_default_radius')); ?>
    </div>
</div>
<div class="field">
    <div class="two columns alpha">
        <label for="geolocation_use_metric_distances"><?php echo __('Use metric distances'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('Use metric distances in proximity search.'); ?></p>
        <?php
        echo $view->formCheckbox('geolocation_use_metric_distances', true,
            array('checked' => (boolean) get_option('geolocation_use_metric_distances')));
        ?>
    </div>
</div>
<div class="field">
    <div class="two columns alpha">
        <label for="cluster"><?php echo __('Enable marker clustering'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('Show close or overlapping markers as clusters.'); ?></p>
        <?php
        echo $view->formCheckbox('cluster', true,
            array('checked' => (boolean) get_option('geolocation_cluster')));
        ?>
    </div>
</div>
</fieldset>

<fieldset>
<legend><?php echo __('Item Map Settings'); ?></legend>
<div class="field">
    <div class="two columns alpha">
        <label for="item_map_width"><?php echo __('Width for Item Map'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('The width of the map displayed on your items/show page. If left blank, the default width of 100% will be used.'); ?></p>
        <?php echo $view->formText('item_map_width', get_option('geolocation_item_map_width')); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="item_map_height"><?php echo __('Height for Item Map'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('The height of the map displayed on your items/show page. If left blank, the default height of 300px will be used.'); ?></p>
        <?php echo $view->formText('item_map_height', get_option('geolocation_item_map_height')); ?>
    </div>
</div>
</fieldset>

<fieldset>
<legend><?php echo __('Map Integration'); ?></legend>
<div class="field">
    <div class="two columns alpha">
        <label for="geolocation_link_to_nav"><?php echo __('Add Link to Map on Items/Browse Navigation'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('Add a link to the items map on all the items/browse pages.'); ?></p>
        <?php
        echo get_view()->formCheckbox('geolocation_link_to_nav', true,
            array('checked' => (boolean) get_option('geolocation_link_to_nav')));
        ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="geolocation_add_map_to_contribution_form"><?php echo __('Add Map To Contribution Form'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('If the Contribution plugin is installed and activated, Geolocation  will add a geolocation map field to the contribution form to associate a location to a contributed item.'); ?></p>
        <?php
        echo get_view()->formCheckbox('geolocation_add_map_to_contribution_form', true,
            array('checked' => (boolean) get_option('geolocation_add_map_to_contribution_form')));
        ?>
    </div>
</div>
</fieldset>
<script type="text/javascript">
function toggleMapboxSettings() {
    jQuery('.mapbox-settings').toggle(jQuery('#basemap').val() === 'MapBox');
}
jQuery(document).ready(function () {
    toggleMapboxSettings();
    jQuery('#basemap').on('change', toggleMapboxSettings);
});
</script>
