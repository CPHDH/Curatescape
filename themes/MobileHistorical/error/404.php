<?php
if (mobile_device_detect()==true){
//begin mobile 404
?>
<?php head(array('title'=>'404')); ?>
<!-- Start of page content: #error -->


<div data-role="content" id="browse">	
<?php echo mobile_simple_search();?>


	    
		<h2>404 Not Found</h2>
		<p>Sorry, this page cannot be found.</p>

		
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
//begin non-mobile 404
?>
<?php head(array('title'=>'404')); ?>

<div id="primary">
<h1>Oops!</h1>

	<p>Sorry, this page doesn't exist. Check your URL, or send us a note.</p>
	
</div><!-- end primary -->

<?php foot(); ?>
<?php }?>