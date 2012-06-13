<?php
if ((get_theme_option('stealth_mode')==1)&&(has_permission('Items', 'edit')!=true)){
include_once('s-index.php');
}
else{
//if not stealth mode, do everything else
?>

<?php
if (mobile_device_detect()==true){
//begin mobile index
?>
<?php head();?>

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
<p>
	<?php echo mh_about();?>
</p>

<a href="#download-app" data-role="button" data-rel="dialog" data-transition="pop" id="download-app">Download the App</a>		
</div> <!-- end content-->

<?php echo common('m-footer-nav');?>

</div> <!-- end outer page from header -->	

<?php echo common('m-dialogues');?>

</body>
</html>
<?	}
//end mobile index
else{	
//begin non-mobile index
?>
<?php head(array('bodyid'=>'home')); ?>


		<div id="content-home">
			
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
		<div id="hm-map">
		<div id="map_canvas">
		<?php echo geolocation_scripts(); ?>
				<style type="text/css" media="screen">
#map-display { width: 710px; height: 335px;}
#map-links li {overflow:hidden; border-bottom:1px dotted #ccc;}
#map-links li a {float:right; width:50%; text-decoration:none; }
</style>

<div class="pagination">
    <?php echo pagination_links(); ?>
</div><!-- end pagination -->

<div id="map-block">
    <?php echo geolocation_google_map('map-display', array('loadKml'=>true, 'list'=>'map-links'));?>
</div><!-- end map_block -->



<div id="link_block" style="display:none;">
    <div id="map-links" style="display:none;"></div><!-- Used by JavaScript -->
</div><!-- end link_block -->
		</div> 
		</div>


<div id="hm-logo"><img src="<?php 
echo mh_lg_logo_url(); 
?>" /></div>

<div id="app-badge">
<?php echo mh_app_download_links_home();?>
</div>

<div id="col-lft">
<img src="<?php echo mh_tour_logo_url(); ?>" alt="Take a Tour" title="Take a Tour" border="0" />

<?php display_tour_items($items); ?>


<p class="view-items-link"><a href="<?php echo WEB_ROOT;?>/tour-builder/tours/browse/">More Tours</a></p>
<div style="clear:both;"> </div>
</div>

<div id="primary-home">

	<div class="hm-col">
	<!-- Recent Items -->		
	<div id="recent-items">
    	<h2 class="col-heading">Recently Added Stories</h2>
	
    		<?php set_items_for_loop(recent_items(1)); ?>
    		<?php if (has_items_for_loop()): ?>
		    
    		<ul class="items-list">
    			<?php while (loop_items()): ?>
			    <div class="itemthumb"><?php echo link_to_item(item_square_thumbnail()); ?></div>
    			<li class="item">
    				<h3><?php echo link_to_item(); ?></h3>
				
        			<?php if($desc = item('Dublin Core', 'Description', array('snippet'=>150))): ?>
        				<div class="item-description"><?php echo $desc; ?></div>
        			<?php endif; ?>						
    			</li>
			
    			<?php endwhile; ?>
    		</ul>
		
    		<?php else: ?>
    			<p>No recent items available.</p>
			
    		<?php endif; ?>
		
    		<p class="view-items-link"><?php echo link_to_browse_items('View All Stories'); ?></p>
	
	</div><!-- end recent-items -->
	</div><!-- end col -->
	
	<div class="hm-col-sep"><img src="<?php echo img('hm-col-sep.gif'); ?>" alt="" title="" border="0" /></div>
	
	<!-- center column-->	
	<div class="hm-col">
	
<?php 
//if twitter values are set, display twitter stuff, otherwise display About text
if ( (get_theme_option('twitter_username')!=false) && (get_theme_option('home_column')!=='about')) {?>


	 <h2 class="col-heading">What Users Are Saying</h2>
	 <div id="twit">
	 <ul><?php echo twi_get(); ?></ul>
	 </div><br>
	 <?php echo mh_follow_the_conversation(); ?>


<?php }
else{
echo '<h2 class="col-heading">About</h2><p id="about">'.get_theme_option('about').'</p>';
}?>	


	</div><!-- end col -->
	
	
	<div class="hm-col-sep"><img src="<?php echo img('hm-col-sep.gif'); ?>" alt="" title="" border="0" /></div>
    
    <div class="hm-col">
    <!-- Featured Item -->
	<div id="featured-item">
		<?php echo display_random_featured_item_CH(true); ?>
	</div><!--end featured-item-->
	
	</div><!-- end center column -->
	

	
	
</div><!-- end primary -->


<?php foot(); ?>
<?php 
//end non-mobile index
}?>

<?php
//end stealth mode else statement
 };?>