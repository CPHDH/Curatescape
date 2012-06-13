<?php head(array('title' => 'Browse Map','bodyid'=>'map','bodyclass' => 'browse')); ?>

<div id="primary">

<h1>Browse Items on the Map (<?php echo $totalItems; ?> total)</h1>

<?php if(function_exists('public_nav_items')): ?>
<ul class="items-nav navigation" id="secondary-nav">
	<?php echo public_nav_items(array('Browse All' => uri('items/browse'), 'Browse by Tag' => uri('items/tags'))); ?>
</ul>
<?php endif; ?>
<div class="pagination">
    <?php echo pagination_links(); ?>
</div><!-- end pagination -->

<div id="map-block">
    <?php echo geolocation_google_map('map-display', array('loadKml'=>true, 'list'=>'map-links'));?>
</div><!-- end map_block -->

<div id="link_block">
    <h2>Find An Item on the Map</h2>
    <div id="map-links"></div><!-- Used by JavaScript -->
</div><!-- end link_block -->

</div><!-- end primary -->

<?php foot(); ?>