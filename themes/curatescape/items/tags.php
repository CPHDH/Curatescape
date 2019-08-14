<?php 
	$tags=get_records('Tag',array('sort_field' => 'count', 'sort_dir' => 'd'),0);
	echo head(array('maptype'=>'none', 'title'=>'Browse by Tag','bodyid'=>'items','bodyclass'=>'browse tags')); 
	?>


<div id="content">
<article class="browse tags">		
	<h2 class="query-header"><?php echo __('Tags: %s', total_records('Tags'));?></h2>

	<div id="primary" class="browse">
	<section id="tags">
		<h2 hidden class="hidden"><?php echo __('Tags');?></h2>

	    <nav class="secondary-nav" id="tag-browse"> 
			<?php mh_item_browse_subnav(); ?>
	    </nav>
	
	    <?php echo tag_cloud($tags,url('items/browse')); ?>

	</section> 
	</div><!-- end primary -->

	<?php echo mh_share_this();?>
</article>	
</div> <!-- end content -->


<?php echo foot(); ?>