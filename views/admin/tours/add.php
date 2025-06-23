<?php echo head(array('title' => 'Add Tour','content_class' =>'vertical-nav','bodyid'=>'tour','bodyclass' => 'tours primary add-tour-form add'));?>

<?php echo flash();?>

<form method="post" enctype="multipart/form-data" id="tour-form" action="">
	<?php require( 'form.php' ); ?>
</form>

<?php echo foot(); ?>
