<?php
if (mobile_device_detect()==true){
// begin mobile tour/browse
?>
<?php head( array( 'title' => 'Browse Tours'));?>
<!-- Start of page content: #tour-browse -->


<div data-role="content" id="tour-browse">	
<?php echo mobile_simple_search();?>



	    
		<h2>Browse Tours (<?php echo $total_records; ?> total)</h2>

		
<ul data-role="listview"data-role="listview" class="ui-listview ui-listview-inset ui-corner-all ui-shadow">



   <?php
   echo flash();
   if( has_tours() ):
      ?>
      <?php if( has_tours_for_loop() ): ?>
      <?php $key = 0; ?>
      <?php while( loop_tours() ): ?>
      	<li data-theme="c" class="ui-btn ui-btn-up-c ui-btn-icon-right ui-li">
      	<div class="ui-btn-inner ui-li">
      		<div class="ui-btn-text">
      			<a href="<?php echo $this->url( array('action' => 'show', 'id' => tour( 'id' ) ) );?>" class="ui-link-inherit <?php if( ++$key%2==1) echo 'odd'; else echo 'even'; ?>" >
      
      				<h4 class="ui-li-heading"><?php echo tour( 'title' ); ?></h4>

  					<!-- 
					<p class="ui-li-desc">
                     <?php// echo nls2p( tour( 'Description' ) ); ?>
           			</p> 
					-->
					
               </a>
            </div>
            <span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>
         </div>
         </li>
               <?php endwhile; ?>

      <?php endif; ?>
   <?php endif; ?>

</ul>

		
<a href="#download-app" data-role="button" data-rel="dialog" data-transition="pop" id="download-app">Download the App</a>		
</div> <!-- end content-->

<?php echo common('m-footer-nav');?>

</div> <!-- end outer page from header -->	

<?php echo common('m-dialogues');?>

</body>
</html>

<?
//end mobile tour/browse
	}
else{	
//begin non-nobile tour/browse
?>
<?php
head( array( 'title' => 'Browse Tours', 'content_class' => 'horizontal-nav',
   'bodyclass' => 'tours primary browse-tours' ) );
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

<div id="lv-logo"><a href="<?php echo WEB_ROOT;?>/"><img src="<?php echo mh_med_logo_url(); ?>" border="0" alt="Cleveland Historical" title="Cleveland Historical" /></a></div>
<!--
<h3>Tags:</h3>
<?php
$tags = get_tags(array('sort' => 'alpha'), 20); 
echo tag_cloud($tags, uri('items/browse'));
?>
-->


</div>


	<div id="primary-browse" class="browse">
<h1>Browse Tours (<?php echo $total_records; ?> total)</h1>

<?php if( has_permission( 'TourBuilder_Tours', 'add' ) ): ?>
<p id="add-tour" class="add-button">
   <a class="add"
      href="<?php echo $this->url( array( 'action' => 'add' ) ); ?>">Add a Tour</a>
</p>
<?php endif; ?>

   <?php
   echo flash();
   if( has_tours() ):
      ?>
      <div class="pagination"><?php echo pagination_links(); ?></div>
      <?php if( has_tours_for_loop() ): ?>
         <table id="tours" class="simple" cellspacing="0" cellpadding="0">
            
            <tbody>
               <?php $key = 0; ?>
               <?php while( loop_tours() ): ?>
               <tr class="tours <?php if( ++$key%2==1) echo 'odd'; else echo 'even'; ?>" >
                  <td scope="row"><h3><?php// echo tour( 'id' ); ?></h3></td>
                  <td scope="row"><h3><a href="<?php
                     echo $this->url( array(
                        'action' => 'show', 'id' => tour( 'id' ) ) );
                     ?>"><?php echo tour( 'title' ); ?></a></h3>
                     <p><?php echo nls2p( tour( 'Description' ) ); ?></p>
                     </td></tr>

               
               <?php endwhile; ?>
            </tbody>
         </table>
      <?php endif; ?>
   <?php endif; ?>
</div>

<?php foot();?>
<?php 
//end non-mobile tour/browse
}?>