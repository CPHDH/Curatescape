<?php echo head(array('title' =>toursBrowsePageTitle($total_results), 'bodyid'=>'tour','bodyclass' => 'tours browse'));?>

<?php echo flash(); ?>

<?php echo item_search_filters();?>

<?php if( $total_results > 0 ): ?>

	<?php if(is_allowed( 'Curatescape_CuratescapeTours', 'add' )): ?>
		<a href="<?php echo html_escape(url( array( 'action' => 'add' ) )); ?>" class="add full-width-mobile button green"><?php echo __('Add a Tour'); ?></a>
	<?php endif;?>

	<div class="table-responsive">
		<table id="tours">
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
			<?php $key = 0; ?>
			<?php foreach($tours as $tour): ?>
				<tr class="tour <?php echo (++$key % 2 == 1) ? 'odd' : 'even'; ?>">
					<td class="record-info">

						<span class="title">
							<a href="<?php echo url(array( 'action' => 'show','id' => $tour->id), 'tourAction' ); ?>"><?php echo html_escape($tour->title); ?></a>
						</span>

						<?php if($tour->featured || !$tour->public): ?>
						<div class="labels">
							<?php if($tour->featured): ?>
							<span class="featured label"><?php echo __('Featured'); ?></span>
							<?php endif; ?>
							<?php if(!$tour->public): ?>
							<span class="private label"><?php echo __('Private'); ?></span>
							<?php endif; ?>
						</div>
						<?php endif; ?>

						<ul class="action-links group">
							<li>
								<a href="#" class="details-link"><?php echo __('Details');?></a>
							</li>
							<?php if(is_allowed( 'Curatescape_CuratescapeTours', 'edit')):?>
							<li>
								<a href="<?php echo url(array( 'action' => 'edit','id' => $tour->id), 'tourAction' );?>" class="edit"><?php echo __('Edit')?></a>
							</li>
							<?php endif;?>
							<?php if(is_allowed( 'Curatescape_CuratescapeTours', 'delete')):?>
							<li>
								<a href="<?php echo url(array( 'action' => 'delete-confirm','id' => $tour->id), 'tourAction' );?>" class="delete-confirm"><?php echo __('Delete')?></a>
							</li>
							<?php endif;?>
						</ul>

						<div class="details hidden">
							<?php $tourDescription = snippet_by_word_count($tour->description, 40); ?>
							<?php if($tourDescription !== ''): ?>
								<p class="description"><?php echo html_escape($tourDescription); ?></p>
							<?php endif; ?>
							<p>
								<strong><?php echo __('Credits');?>:</strong>
								<?php echo metadata( $tour, 'Credits' ) ? metadata( $tour, 'Credits' ) : __('None');?>
							</p>
							<p>
								<strong><?php echo __('Locations');?>:</strong>
								<?php echo count($tour->Items);?>
							</p>
							<p>
								<strong><?php echo __('Tags');?>:</strong>
								<?php echo ($tags = tag_string($tour, 'tours/browse')) ? $tags : __('None'); ?>
							</p>
						</div>
					</td>

					<td><?php echo $tour->id; ?></td>

					<td><?php echo $tour->ordinal ? $tour->ordinal : __('None'); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>

<?php else: ?>

	<?php if(total_records('CuratescapeTour') === 0): ?>
		<h2><?php echo __('You have no tours.'); ?></h2>
		<?php if(is_allowed( 'Curatescape_CuratescapeTours', 'add' )): ?>
			<p><?php echo __('Get started by adding your first tour.'); ?></p>
			<a href="<?php echo html_escape(url( array( 'action' => 'add' ) )); ?>" class="add big green button"><?php echo __('Add a Tour'); ?></a>
		<?php endif;?>
	<?php else: ?>
		<p><?php echo __('The query returned no results.'); ?></p>
	<?php endif;?>

<?php endif;?>

<?php echo foot();?>
