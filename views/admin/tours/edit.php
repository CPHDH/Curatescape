<?php echo head(array('title' => 'Edit Tour','content_class' =>'vertical-nav','bodyid'=>'tour','bodyclass' => 'tours primary edit-tour-form edit'));?>

<?php echo flash();?>

<form method="post" enctype="multipart/form-data" id="tour-form" action="">
	<?php require( 'form.php' ); ?>
</form>

<?php echo foot(); ?>