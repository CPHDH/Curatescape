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
		    	<em><?php echo htmlspecialchars_decode(metadata('tour','Postscript Text')); ?></em>
		   </div>
		</div>
		
		<div id="tour-items">
			<h2 class="locations"><?php echo $tour->getItems() ? __('Locations for Tour') : null;?></h2>
			
			<!-- Tour Map -->
			<?php 
			include_once 'includes/tour-map.php'; 
			?>
			
	        <?php 
	        $i=1;
	        foreach( $tour->getItems() as $tourItem ): 
	        	if($tourItem->public || current_user()){
		        	set_current_record( 'item', $tourItem );
					$itemID=$tourItem->id;
					$hasImage=metadata($tourItem,'has thumbnail');
					$item_image=null;
					if ($hasImage){
						preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', item_image('fullsize'), $result);
						$item_image = array_pop($result);				
					}					
					?>
			        <div class="item-result <?php echo $hasImage ? 'has-image' : null;?>" 
				        style="clear:both;width: 100%;margin-bottom: 1em;max-width: 400px;">
				        <h3><a class="permalink" href="
					        <?php echo url('/') ?>items/show/<?php echo $itemID.
						        '?tour='.tour( 'id' ).'&index='.($i-1).''; ?>">
								<?php echo '<span class="number">'.$i.'</span>';?> 
								<?php echo metadata( $tourItem, array('Dublin Core', 'Title') ); ?>
				        </a></h3>
		
						<?php
						echo isset($item_image) ? '<a href="'. url('/') .
						'items/show/'.$itemID.'?tour='.tour( 'id' ).'&index='.($i-1).'"><div class="item-image" 
						style="background-image:url('.$item_image.');background-color:#ccc;
						height:200px;width:100%;background-size:cover;clear:left;"></div></a>' : null; 
						?>
							         
				        <div class="item-description">
					         <?php echo '<p>'.
						         snippet(metadata($tourItem,array('Dublin Core','Description')),0,250).
						         '</p>'; ?>
					    </div>
			        </div>
			        <?php 
			        $i++;
			        $item_image=null;
				}
	        endforeach; 
	        ?>
		</div>		   
	
</article>

<?php echo foot(); ?>