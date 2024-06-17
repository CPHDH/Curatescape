<?php
$locations=array();
$tourTitle = (tour( 'title' )) ? strip_formatting( tour( 'title' ) ) : '[Untitled]';
echo head( array( 'maptype'=>'tour','title' => ''.__('Tour').' | '.$tourTitle, 'bodyid'=>'tours',
   'bodyclass' => 'show', 'tour'=>$tour ) );
?>
<article id="primary" class="tour" role="main">

		<div id="tour-header">
			<?php
			echo '<h1>'.$tourTitle.'</h1>';
			echo '<span class="tour-meta-browse">';
			if(tour( 'Credits' )){
				echo __('Tour curated by: %s',tour( 'Credits' )).' | ';
			}
			echo count($tour->Items).' '.__('Locations').'</span>';
			?>
		</div>

		<div id="tour-text">
			<div id="tour-description">
				<?php echo htmlspecialchars_decode(nls2p( tour( 'Description' ) )); ?>
			</div>
			<div id="tour-postscript">
				<em><?php echo htmlspecialchars_decode(nls2p(metadata('tour','Postscript Text'))); ?></em>
			</div>
		</div>

		<div id="tour-items">
			<h2 class="locations"><?php echo $tour->getItems() ? __('Locations for Tour') : null;?></h2>

			<?php 
			$i=1;
			foreach( $tour->getItems() as $tourItem ): 
				if($tourItem->public || current_user()){
					set_current_record( 'item', $tourItem );
					$item_image=null;
					$more='<a class="ti-more" href="/items/show/'.$tourItem->id.'">'.__('Learn more').'</a>';
					$hasImage=metadata($tourItem,'has thumbnail');
					$custom = $tour->getTourItem($tourItem->id);
					// description
					if(!empty($custom->text)){
						$description = $custom->text.' '.$more;
					}else{
						$description=(element_exists('Item Type Metadata','Story')) ? snippet(metadata($tourItem,array('Item Type Metadata','Story')),0,300,'&hellip; '.$more) : null;
					}
					// subtitle
					if(!empty($custom->subtitle)){
						$subtitle = $custom->subtitle;
					}else{
						$subtitle=(element_exists('Item Type Metadata','Subtitle')) ? metadata($tourItem,array('Item Type Metadata','Subtitle')) : null;
					}
					
					if ($hasImage){
						preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', item_image('fullsize'), $result);
						$item_image = array_pop($result);
					}
					?>
					<div class="item-result <?php echo $hasImage ? 'has-image' : null;?>" >
						<h3 class="ti-title"><a class="permalink" href="
							<?php echo url('/') ?>items/show/<?php echo $tourItem->id.'?tour='.tour( 'id' ).'&index='.($i-1).''; ?>">
								<?php echo '<span class="number">'.$i.'.</span>';?> 
								<?php echo metadata( $tourItem, array('Dublin Core', 'Title') ); ?>
								<?php echo $subtitle ? '<span class="ti-sep">:</span> <span class="ti-subtitle">'.$subtitle.'</span>' : null;?></a></h3>
						
						<?php
						echo isset($item_image) ? '<a href="'. url('/') .
						'items/show/'.$tourItem->id.'?tour='.tour( 'id' ).'&index='.($i-1).'"><img src="'.$item_image.'" loading="lazy" style="max-width:100%;"/></a>' : null; 
						?>

						<div class="ti-text">
							<?php echo '<p>'.$description.'</p>'; ?>
						</div>
					</div>
					<?php 
					$i++;
					$item_image=null;
				}
			endforeach; ?>
		</div>

</article>

<?php echo foot(); ?>