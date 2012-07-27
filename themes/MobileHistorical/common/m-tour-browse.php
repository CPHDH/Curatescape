<?php head( array( 'title' => 'Browse Tours'), 'm-header' );?>
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
