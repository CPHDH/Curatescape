<?php
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
   $tourTitle = ': &quot;' . $tourTitle . '&quot; ';
} else {
   $tourTitle = '';
}
$tourTitle = 'Tour #' . tour( 'id' ) . $tourTitle;

head( array( 'title' => $tourTitle, 'content_class' => 'horizontal-nav',
   'bodyclass' => 'show' ) );
?>

<div id="content">
			
		    <div id="header">
			<div id="primary-nav">
    			<ul class="navigation">
    			    <?php echo public_nav_main(array('Home' => uri('/'), 'Tours' => uri('/tour-builder/tours/browse/'), 'Browse Locations' => uri('items'))); ?>
    			</ul>
    		</div>
    		<div id="search-wrap">
				    <?php echo simple_search(); ?>
    			</div>
    			<div style="clear:both;"></div>
   		</div>
	




<!-- -->
<div id="page-col-left">

<div id="lv-logo"><a href="<?php echo WEB_ROOT;?>/"><img src="<?php echo img('lv-logo.png'); ?>" border="0" alt="<?php echo settings('site_title');?>" title="<?php echo settings('site_title');?>" /></a></div>

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


   <div id="tour-description" class="element">
      <h2>Description</h2>
      <div class="element-text">
         <?php echo nls2p( tour( 'Description' ) ); ?>
      </div>
   </div>

   <div id="tour-credits" class="element">
      <h2>Credits</h2>
      <div class="element-text">
         <?php echo tour( 'Credits' ); ?>
      </div>
   </div>






<!-- -->
<div id="page-col-right">
		
	</div>	
</div>
<!-- -->



<?php foot(); ?>
