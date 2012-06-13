<div class="field">
	<label for="per_page">Number of Locations Per Page:</label>
	<div class="inputs">
	<input type="text" class="textinput"  name="per_page" size="4" value="<?php echo get_option('geolocation_per_page'); ?>" id="per_page" />
	<p class="explanation">The number of locations displayed per page (max <?php echo GEOLOCATION_MAX_LOCATIONS_PER_PAGE; ?>).</p>
	</div>
</div>

<div class="field">
	<label for="default_latitude">Default Latitude</label>
	<div class="inputs">
		<input type="text" class="textinput" name="default_latitude" size="8" value="<?php echo get_option('geolocation_default_latitude'); ?>" id="default_latitude" />
	    <p class="explanation">A number between -90 and 90.</p>
	</div>
</div>

<div class="field">
	<label for="default_longitude">Default Longitude</label>
	<div class="inputs">
		<input type="text" class="textinput"  name="default_longitude" size="8" value="<?php echo get_option('geolocation_default_longitude'); ?>" id="default_longitude" />
        <p class="explanation">A number between -180 and 180.</p>
	</div>
</div>

<div class="field">
	<label for="default_zoomlevel">Default Zoom Level</label>
	<div class="inputs">
		<input type="text" class="textinput"  name="default_zoomlevel" size="3" value="<?php echo get_option('geolocation_default_zoom_level'); ?>" id="default_zoomlevel" />
	    <p class="explanation">An integer greater than or equal to 0, where 0 represents the most zoomed out scale.</p>
	</div>
</div>

<div class="field">
	<label for="item_map_width">Width for Item Map</label>
	<div class="inputs">
		<input type="text" class="textinput"  name="item_map_width" size="8" value="<?php echo get_option('geolocation_item_map_width'); ?>" id="item_map_width" />
        <p class="explanation">The width of the map displayed on your items/show page. If left blank, the default width of 100% will be used.</p>
	</div>
</div>

<div class="field">
	<label for="item_map_height">Height for Item Map</label>
	<div class="inputs">
		<input type="text" class="textinput"  name="item_map_height" size="8" value="<?php echo get_option('geolocation_item_map_height'); ?>" id="item_map_height" />
        <p class="explanation">The height of the map displayed on your items/show page. If left blank, the default height of 300px will be used.</p>
	</div>
</div>

<div class="field">
    <label for="geolocation_link_to_nav">Add Link to Map on Items/Browse Navigation</label>
    <div class="inputs">
        <?php echo __v()->formCheckbox('geolocation_link_to_nav', true, 
         array('checked'=>(boolean)get_option('geolocation_link_to_nav'))); ?>
         <p class="explanation">If checked, this will add a link to the items map on all the items/browse pages.</p>
    </div>
</div>

<div class="field">
    <label for="geolocation_add_map_to_contribution_form">Add Map To Contribution Form</label>
    <div class="inputs">
        <?php echo __v()->formCheckbox('geolocation_add_map_to_contribution_form', true, 
         array('checked'=>(boolean)get_option('geolocation_add_map_to_contribution_form'))); ?>
         <p class="explanation">If the Contribution plugin is installed and activated, Geolocation  will add a geolocation map field to the contribution form to associate a location to a contributed item.</p>
    </div>
</div>