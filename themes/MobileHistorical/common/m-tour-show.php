<?php
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
   $tourTitle = ': ' . $tourTitle . '';
} else {
   $tourTitle = '';
}
$tourTitle = 'Tour #' . tour( 'id' ) . $tourTitle;
$tourID = tour( 'id' );
head(array( 'title' => $tourTitle),'m-header' );
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