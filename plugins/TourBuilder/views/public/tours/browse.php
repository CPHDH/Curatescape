<?php
if(isset($_GET['tags'])){
	$title=__('Tours tagged "%s"',htmlspecialchars($_GET['tags'])).__(' (%s total)', total_tours());
}else{
	$title=__('Browse Tours (%s total)', total_tours());
}

echo head( array('maptype'=>'none', 'title' => __('Browse Tours'), 'bodyid'=>'tours','bodyclass' => 'browse' ) );
?>

<h1><?php echo $title;?></h1>

<nav class="secondary-nav" id="tag-browse">
	<?php echo public_nav_tours(); ?>
</nav>

<?php echo pagination_links(); ?>

<section class="results">
	<?php 
	if( has_tours() ){
		if( has_tours_for_loop() ){
			$i=1;
			$tourimg=0;
			$active_sort = isset($_GET['sort_field']) ? array() : array('ordinal','a');
			$tours = active_sort_tours($tours,$active_sort);
			foreach( $tours as $tour ){
				set_current_record( 'tour', $tour );
				$tourdesc = strip_tags( htmlspecialchars_decode(tour( 'description' )) );
	
				echo '<div class="item hentry"><article id="item-result-'.$i.'" class="item-result has-image">';
				echo '<h3>'.link_to_tour(null,array('class'=>'permalink')).'</h3>';
	
				echo '<span class="tour-meta-browse">';
				if(tour( 'Credits' )){
					echo __('Tour curated by: %s',tour( 'Credits' )).' | ';
				}
				echo count($tour->Items).' '.__('Locations').'</span>';
				echo '<div class="item-description"><p>'.snippet($tourdesc,0,250).'</p></div>';
	
				echo '</article></div>';
				$i++;
			}
		}
	}else{
		echo '<p>'.__('No tours found.').'</p>';
	}
	?>
</section>

<?php echo pagination_links(); ?>

<?php echo foot();?>