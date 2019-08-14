<?php
$label=mh_tour_label('plural');
echo head( array(
	'maptype'=>'none', 
	'title' => $label, 
	'bodyid'=>'tours',
    'bodyclass' => 'browse' )
    );
?>
<div id="content">
	<article class="browse tour">			
	<h2 class="query-header"><?php echo __('All %1$s: %2$s', $label, total_tours());?></h2>
		<div id="primary" class="browse">
			<section id="results">
			<h2 hidden class="hidden"><?php echo mh_tour_label('plural');?></h2>
			<nav class="tours-nav navigation secondary-nav">
			  <?php echo public_nav_tours(); ?>
			</nav>	
			<div class="pagination top">
				<?php echo pagination_links(); ?>
			</div>
		    <?php 
		    if( has_tours() ){
		    if( has_tours_for_loop() ){
		    	$i=1;
		    	$tourimg=0;
				foreach( $tours as $tour ){ 
					set_current_record( 'tour', $tour );
					$tourdesc = strip_tags( htmlspecialchars_decode(tour( 'description' )) );
					echo '<article id="item-result-'.$i.'" class="item-result has-image">';
					echo '<h3>'.link_to_tour(null,array('class'=>'permalink')).'</h3>';
					echo '<div class="browse-meta-top byline">';
					echo '<span class="total">'.mh_tour_total_items($tour).' '.__('Locations').'</span> ~ ';
					if(tour( 'Credits' )){
						echo __('Curated by %s',tour( 'Credits' ));
					}elseif(get_theme_option('show_author') == true){
						echo __('Curated by The %s Team',option('site_title'));
					}		
					echo '</div>';
		
					echo '<div class="item-description">'.snippet($tourdesc,0,250).'</div>'; 			
					echo '</article>';
					$i++;
				
					}
				}
			}
			?>
			</section>
	    </div>
	    
		<div class="pagination bottom">
			<?php echo pagination_links(); ?>
		</div>
		
		<?php echo mh_share_this(); ?>
		
	</article>
</div> <!-- end content -->
<?php echo foot();?>