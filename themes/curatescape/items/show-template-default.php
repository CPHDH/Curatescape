<?php 
$maptype='story';
if ($hasimg=metadata($item, 'has thumbnail') ) {
	$img_markup=item_image('fullsize',array(),0, $item);
	preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $img_markup, $result);
	$hero_img = array_pop($result);
}
	
echo head(array(
	'item'=>$item, 
	'maptype'=>$maptype, 
	'bodyid'=>'items', 
	'bodyclass'=>'show item-story',
	'title' => metadata($item,array('Dublin Core', 'Title')),
	)); ?>

<article class="story item show" role="main">
			
	<header id="story-header">
		<?php
			echo '<div class="item-hero '.(!$hasimg ? 'hero short' : 'hero').'" '.($hasimg ? 'style="background-image: url('.$hero_img.')"' : null).'>';
			echo '<div class="item-hero-text">'.mh_the_title().mh_the_subtitle().mh_the_byline($item,true).'</div>';
			echo '</div>';	
			echo function_exists('tour_nav') ? '<nav class="tour-nav-container top">'.tour_nav(null,mh_tour_label('singular')).'</nav>' : null;
			echo mh_the_lede();
		?>
		<?php //echo item_is_private($item);?>
	</header>
	<section class="text">
		<h2 hidden class="hidden">Text</h2>
		<?php echo mh_the_text(); ?>
	</section>
	
	<section class="media">
		<h2 hidden class="hidden">Media</h2>
		<?php mh_video_files($item);?>
		<?php mh_item_images($item);?>	
		<?php mh_audio_files($item);?>	
		<?php mh_document_files($item);?>		
	</section>
	<?php if(mh_get_item_json($item)): ?>
	<section class="map">
		<h2>Map</h2>
		<figure>
			<?php echo mh_map_type($maptype,$item); ?>
			<?php echo mh_map_actions($item);?>
		</figure>
		<figcaption><?php echo mh_map_caption();?></figcaption>
	</section>
	<?php endif;?>
	
	<?php echo mh_factoid(); ?>
	
	<section class="metadata">
		<h2 hidden class="hidden">Metadata</h2>
		<?php echo mh_official_website();?>	
		<?php echo mh_item_citation(); ?>
		<?php echo function_exists('tours_for_item') ? tours_for_item($item->id, __('Related %s', mh_tour_label('plural'))) : null?>
		<?php echo mh_subjects(); ?>
		<?php echo mh_tags();?>			
		<?php echo mh_related_links();?>
		<?php echo mh_post_date(); ?>				
		<?php echo mh_display_comments();?>
	</section>	

	<?php echo mh_share_this(mh_item_label());?>
	
	<?php echo function_exists('tour_nav') ? '<nav class="tour-nav-container bottom">'.tour_nav(null,mh_tour_label()).'</nav>' : null; ?>
</article>
<?php echo foot(); ?>