<?php
// Following template structure used by Exhibit Builder plugin to maximize theme compatibility
// To customize, copy the contents of this file to your-theme/curatescape/tours/tags.php 
$pageTitle = toursBrowsePageTitle('0', true);
echo head(array('title' => $pageTitle, 'bodyid'=>'tours', 'bodyclass' => 'tags'));
?>
<h1><?php echo $pageTitle; ?></h1>
<nav class="secondary-nav navigation items-nav" id="tags-browse">
	<?php echo publicNavTours(); ?>
</nav>
<?php echo tag_cloud($tags, 'tours/browse'); ?>
<?php echo foot(); ?>
