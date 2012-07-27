<?php head( array( 'title' => 'Browse Locations') );?>
<!-- Start of page content: #browse -->


<div data-role="content" id="browse">	
<?php echo mobile_simple_search();?>

			
			<?php $term=$_GET['tags']; ?>
			<?php $term2=$_GET['term']; ?>

	    
		<h2>Browse Sites (<?php echo total_results(); ?>)</h2>

		<div data-role="footer">
			<div data-role="navbar">
				<ul>
				<li><a href="<?php echo uri('items')?>" data-transition="fade">All</a></li>
				<li><a href="<?php echo uri('items/tags')?>" data-transition="fade">Tags</a></li>
				</ul>
			</div><!-- /navbar -->
		</div><!-- /footer -->

		<div class="pagination top"><?php echo pagination_links(); ?></div>
		
		<ul data-role="listview"data-role="listview" class="ui-listview ui-listview-inset ui-corner-all ui-shadow">
		
		<?php while (loop_items()): ?>
			<li data-theme="c" class="ui-btn ui-btn-up-c ui-btn-icon-right ui-li ui-li-has-thumb"><div class="ui-btn-inner ui-li"><div class="ui-btn-text">
			<a href="<?php echo item_uri(); ?>" class="ui-link-inherit" target="_self" >
				<?php if (item_square_thumbnail()): ?>
				<?php echo (item_square_thumbnail(array('class'=>'ui-li-thumb'))); ?>
				<?php endif; ?>				
				<h4 class="ui-li-heading"><?php echo item('Dublin Core', 'Title'); ?></h4>
				<!--
				<p class="ui-li-desc">
					<?php// echo item('Dublin Core', 'Description', array('snippet'=>250)); ?>
				</p>
				-->
			</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span></div></li>
			
		
		<?php endwhile; ?>
		</ul>
		
<a href="#download-app" data-role="button" data-rel="dialog" data-transition="pop" id="download-app">Download the App</a>		
</div> <!-- end content-->

<?php echo common('footer-nav');?>

</div> <!-- end outer page from header -->	

<?php echo common('dialogues');?>

</body>
</html>
