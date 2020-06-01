<?php 
$maptype='story';
if ($hasimg=metadata($item, 'has thumbnail') ) {
	$img_markup=item_image('fullsize',array(),0, $item);
	preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $img_markup, $result);
	$hero_img = array_pop($result);
	$hero_class="has-image";
}else{
	$hero_img='';
	$hero_class="no-image";
}
	
echo head(array(
	'item'=>$item, 
	'maptype'=>$maptype, 
	'bodyid'=>'items', 
	'bodyclass'=>'show item-story',
	'title' => metadata($item,array('Dublin Core', 'Title')),
	)); ?>

<article class="story item show" role="main" id="content">
			
	<header id="story-header">
		<?php
			echo '<div class="item-hero hero '.$hero_class.'" style="background-image: url('.$hero_img.')">';
			echo '<div class="item-hero-text">'.mh_the_title().mh_the_subtitle().mh_the_byline($item,true).'</div>';
			echo '</div>';	
			if(function_exists('tour_nav')){
				$tournavhtml=tour_nav(null,mh_tour_label('singular'),get_theme_option('tour_nav_always'),$item->id);
				echo $tournavhtml ? '<nav aria-label="'.__('Tour Navigation - Top').'" class="tour-nav-container top">'.$tournavhtml.'</nav>' : null;
			}
			echo mh_the_lede();
		?>
		<?php //echo item_is_private($item);?>
	</header>
	<section class="text">
		<h2 hidden class="hidden"><?php echo __('Text');?></h2>
		<?php echo mh_the_text(); ?>
	</section>
	
	<?php if($item->Files):?>
	<section class="media">
		<h2 hidden class="hidden"><?php echo __('Media');?></h2>
		<?php mh_video_files($item);?>
		<?php mh_item_images($item);?>	
		<?php mh_audio_files($item);?>	
		<?php mh_document_files($item);?>		
	</section>
	<?php endif;?>
	<?php if(mh_get_item_json($item)): ?>
	<section class="map">
		<h2>Map</h2>
		<nav aria-label="<?php echo __('Skip Interactive Map');?>"><a id="skip-map" href="#map-actions-anchor"><?php echo __('Skip Interactive Map');?></a></nav>
		<figure>
			<?php echo mh_map_type($maptype,$item); ?>
			<?php echo mh_map_actions($item);?>
			<figcaption><?php echo mh_map_caption();?></figcaption>
		</figure>
	</section>
	<?php endif;?>
	
	<?php echo mh_factoid(); ?>
	
	<section class="metadata">
		<h2 hidden class="hidden"><?php echo __('Metadata');?></h2>
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
	
	<?php echo function_exists('tour_nav') ? '<nav aria-label="'.__('Tour Navigation - Bottom').'" class="tour-nav-container bottom">'.tour_nav(null,mh_tour_label()).'</nav>' : null; ?>
</article>
<?php echo foot(); ?>