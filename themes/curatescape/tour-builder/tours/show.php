<?php
$maptype='tour';	
$tourTitle = strip_formatting( tour( 'title' ) );
$label = mh_tour_label('singular');
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
} else {
   $tourTitle = '';
}
echo head( array( 'maptype'=>$maptype,'title' => ''.$label.' | '.$tourTitle, 'content_class' => 'horizontal-nav', 'bodyid'=>'tours',
   'bodyclass' => 'show tour', 'tour'=>$tour ) );
?>
<div id="content">
	<section class="map">
		<h2 hidden class="hidden"><?php echo __('Map for %s',mh_tour_label('singular'));?></h2>
		<nav aria-label="<?php echo __('Skip Interactive Map');?>"><a id="skip-map" href="#tour-content"><?php echo __('Skip Interactive Map');?></a></nav>
		<figure>
			<?php echo mh_map_type($maptype,null,$tour); ?>
			<?php echo mh_map_actions(null,$tour);?>
		</figure>
	</section>
	
	<article id="tour-content" class="tour show" role="main">
	
		<header id="tour-header">
		<h2 class="tour-title"><?php echo $tourTitle; ?></h2>
		<?php $byline=null;
		if(tour( 'Credits' )){
			$byline.= '<span class="tour-meta">'.__('Curated by %s',tour( 'Credits' )).'</span>';
		}elseif(get_theme_option('show_author') == true){
			$byline.= '<span class="tour-meta">'.__('Curated by The %s Team',option('site_title')).'</span>';
		}
		echo '<div class="byline">'.$byline.'</div>';
		?>
		</header>
	
		<div id="primary" class="show">
		    <section id="text">
			   <h2 hidden class="hidden"><?php echo __('%s Description',mh_tour_label('singular'));?></h2>
			   <div id="tour-description">
			    <?php echo htmlspecialchars_decode(nls2p( tour( 'Description' ) )); ?>
			   </div>
			</section>
			   
			<section id="tour-items">
				
				<h3 hidden class="hidden"><?php echo __('Locations for %s', $label);?></h3>
				<nav class="secondary-nav" id="tours-show" aria-hidden="true"> 
					<?php echo mh_tour_browse_subnav($label,$tour->id);?>
				</nav>				
					<?php 
					$i=1;
					foreach( $tour->getItems() as $tourItem ): 
						if($tourItem->public || current_user()):
							set_current_record( 'item', $tourItem );
								$itemID=$tourItem->id;
								$hasImage=metadata($tourItem,'has thumbnail');
							?>
							<div class="tour-item-container flex">
								<div class="tour-route flex flex-column" aria-hidden="true">
									<div class="tour-route-number"><?php echo $i;?> </div>
									<div class="tour-route-path"></div>
								</div>
						        <article class="item-result <?php echo $hasImage ? 'has-image' : null;?>">
									<h3><a href="<?php echo url('/') ?>items/show/<?php echo $itemID.'?tour='.tour( 'id' ).'&index='.($i-1).''; ?>">
										<?php echo strip_tags(metadata( $tourItem, array('Dublin Core', 'Title') )); ?>
									</a></h3>
										         
							        <div class="item-description"><?php echo snippet(mh_the_text($tourItem),0,250); ?></div>
						        </article>
							</div>
							<?php 
							$i++; 
							$item_image=null; 
						endif;
					endforeach; ?>
			</section>
			<section id="tour-postscript">
				<h2 hidden class="hidden"><?php echo __('%s Postscript',mh_tour_label('singular'));?></h2>
				<?php echo htmlspecialchars_decode(metadata('tour','Postscript Text')); ?>
			</section>
			
			<div class="comments">
				<?php echo (get_theme_option('tour_comments') ==1) ? mh_display_comments() : null;?>
			</div>	
			
			<?php echo mh_share_this(mh_tour_label());?>
		
		</div>
		
	</article>
</div> <!-- end content -->

<?php echo foot(); ?>