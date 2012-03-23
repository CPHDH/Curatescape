<?php
if (mobile_device_detect()==true){
//begin mobile tour/show
?>
<?php
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
   $tourTitle = ': ' . $tourTitle . '';
} else {
   $tourTitle = '';
}
$tourTitle = 'Tour #' . tour( 'id' ) . $tourTitle;
$tourID = tour( 'id' );
head(array( 'title' => $tourTitle));
?>
<!-- Start of page content: #tour-show -->


<div data-role="content" id="tour-show">	
<?php echo mobile_simple_search();?>



		 
		<h2><?php echo tour( 'title' ); ?></h2>
		
		<div class="element-text">
		<?php echo nls2p( tour( 'Description' ) ); ?>
		</div>
   		
   		<!-- 
   		<div id="tour-credits" class="element">
	 	<h3>Credits</h3>
		  <div class="element-text">
	  		   <?php// echo tour( 'Credits' ); ?>
		  </div>
  		 </div>  		  
  		 -->

		
<ul data-role="listview"data-role="listview" class="ui-listview ui-listview-inset ui-corner-all ui-shadow">
<?php foreach( $tour->Items as $tourItem ): ?>

	  	<li data-theme="c" class="ui-btn ui-btn-up-c ui-btn-icon-right ui-li ui-li-has-thumb">
	  	<div class="ui-btn-inner ui-li">
	  		<div class="ui-btn-text">
	  			<a href="<?php echo uri('/') ?>items/show/<?php echo $tourItem->id; ?>" class="ui-link-inherit" target="_self" >	 
				<?php if (item_square_thumbnail($props = array(),$index = 0, $tourItem)): ?>
				<?php echo (item_square_thumbnail(array('class'=>'ui-li-thumb'),$index = 0, $tourItem)); ?>
				<?php endif; ?>		
	  
	  				<h4 class="ui-li-heading">
	  				<?php echo $this->itemMetadata( $tourItem, 'Dublin Core', 'Title' ); ?>
	  				</h4>
	  				<!-- 
	  				<p><?php// echo $this->itemMetadata( $tourItem, 'Dublin Core', 'Description' ); ?></p>
					-->
					
			   </a>
			</div>
			<span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>
		 </div>
		 </li>
<?php endforeach; ?>
</ul>

		
<a href="#download-app" data-role="button" data-rel="dialog" data-transition="pop" id="download-app">Download the App</a>		
</div> <!-- end content-->

<?php echo common('m-footer-nav');?>

</div> <!-- end outer page from header -->	

<?php echo common('m-dialogues');?>

</body>
</html>
<?
//end mobile tour/show
	}
else{	
//begin non-mobile tour/show
?>
<?php
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
   $tourTitle = ': &quot;' . $tourTitle . '&quot; ';
} else {
   $tourTitle = '';
}
$tourTitle = 'Tour' . $tourTitle;

head( array( 'title' => $tourTitle, 'content_class' => 'horizontal-nav',
   'bodyclass' => 'show' ) );
?>

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

<div id="tour-items">
   <h3>Locations</h3>
   <div>

         <?php foreach( $tour->Items as $tourItem ): ?>
         <li><a href="<?php echo uri('/') ?>items/show/<?php echo $tourItem->id; ?>"><?php
         echo $this->itemMetadata( $tourItem, 'Dublin Core', 'Title' ); ?></a>
         </li>
         <?php endforeach; ?>

   </div>
</div>


</div>


	<div id="primary" class="show">

<h1><?php echo $tourTitle; ?></h1>

<?php if( has_permission( 'TourBuilder_Tours', 'edit' ) ): ?>


<?php endif; ?>


   <div id="tour-description" class="element">
      <h2>Description</h2>
      <div class="element-text">
         <?php echo nls2p( tour( 'Description' ) ); ?>
      </div>
   </div>

   <div id="tour-credits" class="element">
   <?php if(tour( 'Credits' )): ?>
      <h2>Credits</h2>
      <div class="element-text">
         <?php echo tour( 'Credits' ); ?>
      </div>
    <?php endif;?>  
   </div>






</div>
<!-- -->

<div id="page-col-right">

	<div id="itemfiles" class="element">
	<div class="element-text">

		<div id="share-this">
			<h3 style="margin-top:10px;clear:both">Share this Page</h3>
			<!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
			<a class="addthis_button_preferred_1"></a>
			<a class="addthis_button_preferred_2"></a>
			<a class="addthis_button_preferred_3"></a>
			<a class="addthis_button_preferred_4"></a>
		</div>
		<?php $addthis = (get_theme_option('Add This')) ? (get_theme_option('Add This')) : 'ra-4e89c646711b8856';?>
		<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=<?php echo $addthis ;?>"></script>
			<!-- replace #pubid= value with your ADDTHIS user profile to enable analytics (see settings >> profiles) -->
		<!-- AddThis Button END -->
	</div>
	</div>
	
</div>	
	
</div>



<?php foot(); ?>
<?php 
//end non-mobile tour/show
}?>