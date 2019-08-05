<?php 
queue_css_file('geolocation-items-map');
    
$title = __("Browse Items on the Map").' (' . html_escape($totalItems).' '.__('total').')';

echo head(array('title' => $title));
echo item_search_filters();
echo pagination_links();
?>

<div id="geolocation-browse">
    <?php echo $this->geolocationMapBrowse('map_browse', array('list' => 'map-links', 'params' => $params)); ?>
    <div id="map-links"><h2><?php echo __('Find An Item on the Map'); ?></h2></div>
</div>

<div id="search_block">
    <?php echo items_search_form(array('id'=>'search'), $_SERVER['REQUEST_URI']); ?>
</div><!-- end search_block -->

<?php echo foot(); ?>
