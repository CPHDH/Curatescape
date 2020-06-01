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
			<div id="browse-tours-container">
		    <?php 
			$html = null;
		    if( has_tours() ){
		    if( has_tours_for_loop() ){
		    	$i=1;
		    	$tourimg=0;
		    	
				foreach( $tours as $tour ){ 
					set_current_record( 'tour', $tour );
					

					if(tour('credits')){
						$byline= __('Curated by %s',tour('credits'));
					}else{
						$byline= __('Curated by The %s Team',option('site_title'));
					}
					$tourdesc = strip_tags( htmlspecialchars_decode(tour( 'description' )) );			
						
					$html .= '<article class="item-result '.(get_theme_option('fetch_tour_images') ? 'fetch-tour-image' : null).'" data-tour-id="'.tour('id').'">';
					$html .= get_theme_option('fetch_tour_images') ? '<div class="tour-image-container"></div>' : null;
					$html .= '<div>';
					$html .= '<h3 class="home-tour-title"><a href="' . WEB_ROOT . '/tours/show/'. tour('id').'">' . tour('title').'</a></h3><span class="total">'.__('%s Locations',mh_tour_total_items($tour)).'</span> ~ <span>'.$byline.'</span>';
					$html .= '<div class="item-description">'.snippet($tourdesc,0,250).'</div>'; 
					$html .= '</div>';
					$html .= '</article>';		
						
					}
					
				}else{
					$html .= '<p>'.__('No tours are available. Publish some now.').'</p>';
				}
			}
			echo $html;	
			?>
			</div>
			</section>
	    </div>
	    
		<div class="pagination bottom">
			<?php echo pagination_links(); ?>
		</div>
		
		<?php echo mh_share_this(); ?>
		
	</article>
</div> <!-- end content -->
<?php echo foot();?>