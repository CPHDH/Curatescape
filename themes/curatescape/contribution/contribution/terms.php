<?php 
$head = array('title' => __('Contribution Terms of Service'));
echo head($head);
?>
<article class="page show" id="content" role="main">
<div id="primary">
<h1><?php echo $head['title']; ?></h1>
<?php echo get_option('contribution_consent_text'); ?>
</div>
</article>
<?php echo foot(); ?>