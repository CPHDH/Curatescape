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

	<div id="lv-logo"><a href="<?php echo WEB_ROOT;?>/"><img src="<?php echo img('lv-logo.png'); ?>" border="0" alt="<?php echo settings('site_title');?>" title="<?php echo settings('site_title');?>" /></a>
	</div>

</div>


<div id="primary-browse" class="browse">
<h1>Browse Tours (<?php echo $total_records; ?> total)</h1>


   <?php
   if( has_tours() ):
      ?>
      <div class="pagination"><?php echo pagination_links(); ?></div>
      <?php if( has_tours_for_loop() ): ?>
         <table id="tours" class="simple" cellspacing="0" cellpadding="0">
            
            <tbody>
               <?php $key = 0; ?>
               <?php while( loop_tours() ): ?>
               <tr class="tours <?php if( ++$key%2==1) echo 'odd'; else echo 'even'; ?>" >
                  <td scope="row"><h3><?php echo tour( 'id' ); ?></h3></td>
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

<?php foot();
