<?php
// Following template structure used by Exhibit Builder plugin to maximize theme compatibility
// To customize, copy the contents of this file to your-theme/curatescape/tours/browse.php 
$pageTitle = toursBrowsePageTitle($total_results);
echo head(array('title' => $pageTitle, 'bodyid'=>'tours','bodyclass' => 'browse'));
?>
<h1><?php echo $pageTitle;?></h1>
<nav class="secondary-nav navigation items-nav" id="tours-browse">
	<?php echo publicNavTours(); ?>
</nav>
<?php if (count($tours) > 0): ?>
	<?php echo pagination_links(); ?>
	<?php foreach (loop('tour') as $tour): ?>
		<div class="tour">
			<h2>
				<?php echo linkToTour($tour); ?>
			</h2>
			<?php if ($tourImage = $tour->getFileCustom()): ?>
				<?php echo linkToTour($tour, $tourImage, array('class'=>'image'), 'show'); ?>
			<?php endif; ?>
			<?php if ($tourDescription = metadata($tour, 'description', array('no_escape'=>true))): ?>
				<div class="description">
					<?php echo $tourDescription; ?>
				</div>
			<?php endif; ?>
			<?php if ($tourTags = tag_string($tour, 'tours')): ?>
				<p class="tags">
					<?php echo $tourTags; ?>
				</p>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	<?php echo pagination_links(); ?>
<?php else: ?>
	<p><?php echo __('There are no tours available yet.'); ?></p>
<?php endif; ?>
<?php echo foot(); ?>