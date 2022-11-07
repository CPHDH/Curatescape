<?php 
	$tags=get_records('Tag',array('sort_field' => 'count', 'sort_dir' => 'd','type'=>'tour'),0);
	echo head(array('maptype'=>'none', 'title'=>'Browse by Tag','bodyid'=>'items','bodyclass'=>'browse tags')); 
	?>


<div id="content">
<article class="browse tags">		
	<h2 class="query-header"><?php echo __('Tags: %s', count($tags));?></h2>

	<div id="primary" class="browse">
	<section id="tags">
		<h2 hidden class="hidden"><?php echo __('Tags');?></h2>

	    <nav class="secondary-nav" id="tag-browse"> 
			<?php echo public_nav_tours(); ?>
	    </nav>
	
	    <?php echo tag_cloud($tags,url('tours/browse')); ?>

	</section> 
	</div><!-- end primary -->

	<?php echo mh_share_this();?>
</article>	
</div> <!-- end content -->


<?php echo foot(); ?>