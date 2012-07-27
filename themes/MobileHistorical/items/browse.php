<?php
if (mobile_device_detect()==true){
// begin mobile items/browse
?>
<?php head( array( 'title' => 'Browse Stories') );?>
<!-- Start of page content: #browse -->


<div data-role="content" id="browse">	
<?php echo mobile_simple_search();?>

			
			<?php $term=html_escape($_GET['tags']); ?>
			<?php $term2=html_escape($_GET['term']); ?>

	    
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

<?php echo common('m-footer-nav');?>

</div> <!-- end outer page from header -->	

<?php echo common('m-dialogues');?>

</body>
</html>

<?
// end mobile items/browse
	}
else{
// begin non-mobile items/browse	
?>
<?php head(array('title'=>'Browse Stories')); ?>

<div id="content">
			
		    <div id="header">
			<div id="primary-nav">
    			<ul class="navigation">
    			    <?php echo mh_global_nav('desktop'); ?>
    			</ul>
    		</div>
    		<div id="search-wrap">
				    <?php echo simple_search(); ?>
    			</div>
    			<div style="clear:both;"></div>
   		</div>
	




<!-- -->
<div id="page-col-left">

<div id="lv-logo"><a href="<?php echo WEB_ROOT;?>/"><img src="<?php echo mh_med_logo_url(); ?>" border="0" alt="<?php echo settings('site_title');?>" title="<?php echo settings('site_title');?>" /></a></div>


</div>


	<div id="primary-browse" class="browse">
			
			<?php $term=html_escape($_GET['tags']); ?>
			<?php $term2=html_escape($_GET['term']); ?>

	    
		<h1>Browse <?php echo $term.$term2 ?> (<?php echo total_results(); ?> total)</h1>
		
		<ul class="navigation" id="secondary-nav">
			<?php 
			if (function_exists('subject_browse_public_navigation_items')){
			echo nav(array('Browse All' => uri('items'), 'Browse by Tag' => uri('items/tags'), 'Browse by Subject' => uri('items/subject-browse')));
			}
			else{
			echo nav(array('Browse All' => uri('items'), 'Browse by Tag' => uri('items/tags')));
			} ?>
		</ul>
		
		<div class="pagination top"><?php echo pagination_links(); ?></div>
		
		<?php while (loop_items()): ?>
			<div class="item hentry">
				<div class="item-meta">
				    
				
				<?php if (item_has_thumbnail()): ?>
    				 <div class="itemthumb-float">
    				<?php echo link_to_item(item_square_thumbnail()); ?>						
    				</div>
				<?php endif; ?>

				<h3><?php echo link_to_item(item('Dublin Core', 'Title'), array('class'=>'permalink')); ?></h3>
				
				<?php if ($text = item('Item Type Metadata', 'Text', array('snippet'=>250))): ?>
	    			<div class="item-description">
    				<p><?php echo $text; ?></p>
    				</div>
				<?php elseif ($description = item('Dublin Core', 'Description', array('snippet'=>250))): ?>
    				<div class="item-description">
    				<?php echo $description; ?>
    				</div>
				<?php endif; ?>

				<?php if (item_has_tags()): ?>
    				<div class="tags"><p><strong>Tags:</strong>
    				<?php echo item_tags_as_string(); ?></p>
    				</div>
				<?php endif; ?>
				
                <?php echo plugin_append_to_items_browse_each(); ?>
                
				</div><!-- end class="item-meta" -->
			</div><!-- end class="item hentry" -->			
		<?php endwhile; ?>
		
		<div class="pagination bottom"><?php echo pagination_links(); ?></div>
		
		<?php echo plugin_append_to_items_browse(); ?>
		
	</div><!-- end primary -->
	

<!-- -->



<?php foot(); ?>
<?php 
//end non-mobile items/browse
}?>