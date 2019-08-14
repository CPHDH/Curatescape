<?php 
if(!$collection){
	// We don't use the Collections on the front-end
	include_once($_SERVER["DOCUMENT_ROOT"] . "/themes/curatescape/error/404.php");
}else{
$title=metadata($collection,array('Dublin Core','Title'));
$bodyclass = 'collections show';
$bodyid='collections';
echo head(array('maptype'=>'none','title' => __('Collection').' | '.$title, 'bodyclass' => $bodyclass, 'bodyid' => $bodyid)); 
?>

<div id="content" role="main">
<article class="browse collection">			
	<h2><?php echo $title;?></h2>
<!-- 	<span class="collection-meta-browse"><?php echo metadata($collection, 'total_items').' Items';?></span> -->

	<div id="primary" class="browse">
	
	    <section id="text">
		   <div id="collection-description">
		    <?php echo metadata($collection,array('Dublin Core','Description')); ?>

		    <?php if (metadata('collection', 'total_items') > 0): ?>

				<br><br><p><?php echo link_to('items','browse',__("Browse %1s Items in this collection.",metadata('collection', 'total_items')),array('class'=>'button collection-items-browse'),array('collection'=>$collection->id) ); ?></p>

		    <?php else: ?>
		    
		        <br><br><p><?php echo __("This collection currently has no %s.",mh_item_label('plural')); ?></p>
		        
		    <?php endif; ?>
		    
		   </div>
		    		   
		</section>
    </div>
	<?php echo mh_share_this('Collection'); ?>
</article>
</div> <!-- end content -->



<?php echo foot();?>

<?php } ?>