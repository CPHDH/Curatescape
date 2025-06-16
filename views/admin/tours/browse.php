<?php 
if (isset($_GET['featured']) && $_GET['featured'] == 1) {
	$pageTitle = __('Featured Tours (%s total)', $total_results);
}elseif(isset($_GET['tags']) ){
	$tag = htmlspecialchars($_GET['tags']);
	$pageTitle = __('Tours tagged "%1s" (%2s total)', $tag, $total_results );
}else{
	$pageTitle = __('All Tours (%s total)', $total_results);
}
if(has_tours()){
  $tours = activeSort($tours);
}
?>

<?php echo head(array('title' => $pageTitle, 'bodyid'=>'tour','bodyclass' => 'tours browse'));?>

<?php echo flash();?>

<?php if(is_allowed( 'Curatescape_CuratescapeTours', 'add' )): ?>
	<div class="tour-actions">
		<?php if( $total_results === 0 ): ?>
			<p><?php echo __('Get started by adding your first tour.'); ?></p>
		<?php endif;?>
		<a class="add button small green" href="<?php echo url( array( 'action' => 'add' ) ) ?>">
		<?php echo __('Add a Tour'); ?>
		</a>
	</div>
<?php endif;?>

<div id="primary">
	<?php if( $total_results > 0 ): ?>
		<table id="tours" class="simple" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
				<?php echo browse_sort_links(
					array(
						__('Title') => 'title',
						__('ID') => 'id',
						__('Custom Order') => 'ordinal',
					), array('link_tag' => 'th scope="col"', 'list_tag' => '')
				);?>
				</tr>
			</thead>
			<tbody>
			<?php foreach($tours as $index=>$tour): ?>
				<tr class="tour <?php echo (($index % 2) == 1) ? 'odd' : 'even'; ?>">
					<td scope="row" <?php echo ($tour->featured) ? 'class="featured"' : null?> >

						<span class="title">
							<a href="<?php echo url(array( 'action' => 'show','id' => $tour->id), 'toursAction' ); ?>"><?php echo $tour->title; ?></a>
							<?php if(metadata( $tour, 'Featured' ) == 1):?>
							<div class="featured-icon">
								<span class="featured" aria-hidden="true" title="<?php echo __('Featured'); ?>"></span>
								<span class="sr-only icon-label"><?php echo __('Featured'); ?></span>
							</div>
							<?php endif;?>
							<?php if(metadata( $tour, 'Public' ) !== 1): ?>
								<span class="private"><?php echo __('(Private)'); ?></span>
							<?php endif; ?>
						</span>

						<div class="action-links group">
							<a href="javascript:void(0)" class="details-link"><?php echo __('Details');?></a>
							<?php if(is_allowed( 'Curatescape_CuratescapeTours', 'edit')):?>
								<span class="middot">&middot;</span>
								<a href="<?php echo url(array( 'action' => 'edit','id' => $tour->id), 'toursAction' );?>" class="edit"><?php echo __('Edit')?></a>
							<?php endif;?>

							<div class="details admin-tour-browse-meta hidden">
								<div>
									<strong><?php echo __('Credits');?></strong>: <?php echo metadata( $tour, 'Credits' ) ? metadata( $tour, 'Credits' ) : __('None');?>
								</div>
								<span class="middot">&middot;</span>
								<div>
									<strong><?php echo __('Locations');?></strong>: <?php echo count($tour->Items);?>
								</div>
								<span class="middot">&middot;</span>
								<div>
									<strong><?php echo __('Tags');?></strong>: <?php echo ($tags = tag_string($tour, 'tours')) ? $tags : __('None'); ?>
								</div>
							</div>
						</div>
					</td>

					<td scope="row"><?php echo $tour->id; ?></td>

					<td scope="row"><?php echo $tour->ordinal ? $tour->ordinal : __('None'); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif;?>
</div>
<?php echo foot();?>
