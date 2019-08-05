<?php 
$tag = (isset($_GET['tag']) ? htmlspecialchars($_GET['tag']) : null); // items --> browse
$tags = (isset($_GET['tags']) ? htmlspecialchars($_GET['tags']) : null); // tags/items --> show
$subj = ( (isset($_GET['advanced'][0]['element_id']) && $_GET['advanced'][0]['element_id'] == 49 )  ? htmlspecialchars($_GET['advanced'][0]['terms']) : null );
$auth= ( (isset($_GET['advanced'][0]['element_id']) && $_GET['advanced'][0]['element_id'] == 39 )  ? htmlspecialchars($_GET['advanced'][0]['terms']) : null );
$collection = (isset($_GET['collection']) ? htmlspecialchars($_GET['collection']) : null);
$query = (isset($_GET['search']) ? htmlspecialchars($_GET['search']) : null);
$bodyclass='browse';
$maptype='focusarea';

if ( ($tag || $tags) && !($query) ) {
	$the_tag=($tag ? $tag : $tags);
	$title = __('%1$s tagged "%2$s"', mh_item_label('plural'), $the_tag);
	$bodyclass .=' queryresults';
	$maptype='queryresults';
}
elseif ( !empty($auth) ) {
	$title = __('%1$s by author "%2$s"', mh_item_label('plural'), $auth);
	$bodyclass .=' queryresults';
	$maptype='queryresults';
}elseif ( !empty($subj) ) {
	$title = __('Results for subject term "%s"', $subj);
	$bodyclass .=' queryresults';
	$maptype='queryresults';
}elseif ( !empty($collection) ) {
	$c=get_record_by_id('collection',$collection);
	$collection_title=metadata($c,array('Dublin Core','Title'));
	$title = __('%1s in "%2s"', mh_item_label('plural'), $collection_title);
	$bodyclass .=' queryresults';
	$maptype='queryresults';
}elseif ( isset($_GET['featured']) && $_GET['featured'] == 1){
	$title = __('Featured %s', mh_item_label('plural'));
	$bodyclass .=' queryresults';
	$maptype='queryresults';
}
elseif ($query) {
	$title = __('Search Results for "%s"', $query);
	$bodyclass .=' queryresults';
	$maptype='queryresults';
}	
else{
	$title = __('All %s', mh_item_label('plural'));
	$bodyclass .=' items stories';
}	
echo head(array('maptype'=>$maptype,'title'=>$title,'bodyid'=>'items','bodyclass'=>$bodyclass)); 
?>

<div id="content">
	
<section class="map">
	<figure>
		<?php echo mh_map_type($maptype,null,null); ?>
	</figure>
</section>	

<article class="browse stories items">	
	<h2 class="query-header"><?php 
	$title .= ( $total_results  ? ': <span class="item-number">'.$total_results.'</span>' : '');
	echo $title; 
	?></h2>

	<div id="primary" class="browse">
		<section id="results">
				
			<nav class="secondary-nav" id="item-browse"> 
				<?php echo mh_item_browse_subnav();?>
			</nav>
			
			<div class="pagination top"><?php echo pagination_links(); ?></div>
			<div class="browse-items flex" role="main">
			<?php 
			foreach(loop('Items') as $item): 
				$item_image=null;
				$description = mh_the_text($item,array('snippet'=>250));
				$tags=tag_string(get_current_record('item') , url('items/browse'));
				$titlelink=link_to_item(metadata($item, array('Dublin Core', 'Title')), array('class'=>'permalink'));
				$hasImage=metadata($item, 'has thumbnail');
				if ($hasImage){
						preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', item_image('fullsize'), $result);
						$item_image = array_pop($result);				
				}
				
				?>
				<article class="item-result <?php echo $hasImage ? 'has-image' : null;?>">
					<?php echo isset($item_image) ? link_to_item('<span class="item-image" style="background-image:url('.$item_image.');"></span>',array('title'=>metadata($item,array('Dublin Core','Title')))) : null; ?>
					<h3><?php echo $titlelink; ?></h3>
					<div class="browse-meta-top"><?php echo mh_the_byline($item,false);?></div>
					
					
					<?php if ($description): ?>
	    				<div class="item-description">
	    					<?php echo strip_tags($description); ?>
	    				</div>
					<?php endif; ?>
					
					<?php if(false): /* TODO: make a theme option */ ?>
						<div class="item-meta-browse">
							<?php 
							if(get_theme_option('subjects_on_browse')==1){
								echo mh_subjects(); 
								}
							?>					
							<?php echo mh_tags();?>
						</div>
					<?php endif;?>
					
				</article> 
			<?php endforeach; ?>
			</div>
			<div class="pagination bottom"><?php echo pagination_links(); ?></div>
		</section>	
	</div><!-- end primary -->
	<?php echo mh_share_this();?>
</article>
</div> <!-- end content -->


<?php echo foot(); ?>