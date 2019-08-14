<?php echo head(array('maptype'=>'none','title' => metadata('exhibit', 'title'), 'bodyclass'=>'exhibits summary show','bodyid' => 'exhibit')); ?>

<div id="content" role="main">
<article class="exhibit page show">
	<h1><?php echo metadata('exhibit', 'title'); ?></h1>
			
	<?php if ($exhibitDescription = metadata('exhibit', 'description', array('no_escape' => true))): ?>
	<div class="exhibit-description">
	    <?php echo $exhibitDescription; ?>
	</div>
	<?php endif; ?>
	
	<?php if (($exhibitCredits = metadata('exhibit', 'credits'))): ?>
	<div class="exhibit-credits">
	    <h3><?php echo __('Credits'); ?></h3>
	    <p><?php echo $exhibitCredits; ?></p>
	</div>
	<?php endif; ?>


	<nav id="exhibit-pages">
		<h3>Contents</h3>
	    <ul>
	        <?php set_exhibit_pages_for_loop_by_exhibit(); ?>
	        <?php foreach (loop('exhibit_page') as $exhibitPage): ?>
	        	<?php echo exhibit_builder_page_summary($exhibitPage); ?>
	        <?php endforeach; ?>
	    </ul>
	</nav><br><br>
		
	<aside id="share-this" class="show">
		<?php echo mh_share_this();?>
	</aside>

</article>
</div> <!-- end content -->



<?php echo foot(); ?>