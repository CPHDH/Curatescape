<?php head('m-header');?>

<!-- Start of page content: #home -->

	<div data-role="content" id="home">	
	<?php echo mobile_simple_search();?>


		<div id="featured">
		<h2>Featured Site</h2>
		
		<?php
		$item = random_featured_item(true);
		set_current_item($item);
 		?>
 		<ul data-role="listview"data-role="listview" class="ui-listview ui-listview-inset ui-corner-all ui-shadow" >

			<li data-theme="c" class="ui-btn ui-btn-up-c ui-btn-icon-right ui-li ui-li-has-thumb"><div class="ui-btn-inner ui-li"><div class="ui-btn-text">
			<a href="<?php echo item_uri(); ?>" class="ui-link-inherit" target="_self" data-transition="slide">
				<?php if (item_square_thumbnail()): ?>
				<?php echo (item_square_thumbnail(array('class'=>'ui-li-thumb'))); ?>
				<?php endif; ?>				
				<h3 class="ui-li-heading"><?php echo item('Dublin Core', 'Title'); ?></h3>
				<!-- 
				<p class="ui-li-desc">
					<?php// echo item('Dublin Core', 'Description', array('snippet'=>250)); ?>
				</p>
				-->
			</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span></div></li>
		</ul>
		</div>
		
		<div id="recent">
		<h2>Recently Added</h2>

		<ul data-role="listview"data-role="listview" class="ui-listview ui-listview-inset ui-corner-all ui-shadow">
		<?php set_items_for_loop(recent_items(3)); ?>
		<?php while (loop_items()): ?>
			<li data-theme="c" class="ui-btn ui-btn-up-c ui-btn-icon-right ui-li ui-li-has-thumb"><div class="ui-btn-inner ui-li"><div class="ui-btn-text">
			<a href="<?php echo item_uri(); ?>" class="ui-link-inherit" target="_self" data-transition="slide">
				<?php if (item_square_thumbnail()): ?>
				<?php echo (item_square_thumbnail(array('class'=>'ui-li-thumb'))); ?>
				<?php endif; ?>				
				<h3 class="ui-li-heading"><?php echo item('Dublin Core', 'Title'); ?></h3>

			</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span></div></li>
			
		
		<?php endwhile; ?>
		</ul>
		</div>
	<h2>About</h2>
<p>Cleveland Historical is a free mobile app that puts Cleveland history at your fingertips. Developed by the Center for Public History + Digital Humanities at Cleveland State University, Cleveland Historical lets you explore the people, places, and moments that have shaped the city’s history. Learn about the region through layered, map-based, multimedia presentations, use social media to share your stories, and experience curated historical tours of Northeast Ohio.</p>

<a href="#download-app" data-role="button" data-rel="dialog" data-transition="pop" id="download-app">Download the App</a>		
</div> <!-- end content-->

<?php echo common('m-footer-nav');?>

</div> <!-- end outer page from header -->	

<?php echo common('m-dialogues');?>

</body>
</html>