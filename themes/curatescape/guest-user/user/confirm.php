<?php
$head = array('title' => __('Confirmation Error'));
echo head($head);
?>

<article class="page show"id="content" role="main">
	<h1><?php echo $head['title']?></h1>
	<div id='primary'>
	<?php echo flash(); ?>
	</div>
</article>
<?php echo foot(); ?>