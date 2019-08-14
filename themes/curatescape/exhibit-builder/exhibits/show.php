<?php echo head(array('maptype'=>'none','title' => html_escape(metadata('exhibit_page', 'title') . ' : '. metadata('exhibit', 'title')), 'bodyclass' => 'exhibits show', 'bodyid' => 'exhibit')); ?>

<div id="content" role="main">
<article class="page show">

	<h2 class="instapaper_title"><?php echo metadata('exhibit_page', 'title'); ?></h2>
	
	<?php exhibit_builder_render_exhibit_page(); ?>
	
	<div id="exhibit-page-navigation">
	    <?php if ($prevLink = exhibit_builder_link_to_previous_page()): ?>
	    <div id="exhibit-nav-prev">
	    <?php echo $prevLink; ?>
	    </div>
	    <?php endif; ?>
	    <?php if ($nextLink = exhibit_builder_link_to_next_page()): ?>
	    <div id="exhibit-nav-next">
	    <?php echo $nextLink; ?>
	    </div>
	    <?php endif; ?>

	</div>


	<aside id="share-this" class="show">
		<?php echo mh_share_this();?>
	</aside>

</article>
</div> <!-- end content -->


<?php echo foot(); ?>