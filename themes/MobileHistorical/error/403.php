<?php
if (mobile_device_detect()==true){
//begin mobile 403
?>
<?php head(array('title'=>'403')); ?>
<!-- Start of page content: #error -->


<div data-role="content" id="browse">	
<?php echo mobile_simple_search();?>
   
	<h2>Oops!</h2>	
	<p>Sorry, you don't have permission to view this page.</p>
		
<a href="#download-app" data-role="button" data-rel="dialog" data-transition="pop">Download the App</a>		
</div> <!-- end content-->

<?php echo common('m-footer-nav');?>

</div> <!-- end outer page from header -->	

<?php echo common('m-dialogues');?>

</body>
</html>


<?
	}
else{	
//begin non-mobile 403
?>
<?php head(array('title'=>'403','bodyid'=>'403')); ?>

<div id="primary">	
<h2>Oops!</h2>	

	<p>Sorry, you don't have permission to view this page.</p>
	
</div><!-- end primary -->

<?php foot(); ?>
<?php }?>