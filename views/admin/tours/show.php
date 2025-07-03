<?php
$tourTitle = strip_formatting(metadata('tour','Title'));
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
	$tourTitle = ': &quot;' . $tourTitle . '&quot; ';
} else {
	$tourTitle = '';
}
$tourTitle = 'Tour #'.metadata('tour','id').$tourTitle;
echo head(array('title' => $tourTitle,'bodyclass' =>'show','bodyid'=>'tour'));
echo flash();
?>

<section class="seven columns alpha">
	<?php if(metadata('tour','Title')): ?>
		<div id="tour-title" class="element">
			<h2><?php echo __('Title');?></h2>
			<div class="element-text">
				<?php echo metadata('tour','Title'); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if(metadata('tour','Credits')): ?>
		<div id="tour-credits" class="element">
			<h2><?php echo __('Credits');?></h2>
			<div class="element-text">
				<?php echo metadata('tour','Credits'); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if(metadata('tour','Description')): ?>
		<div id="tour-description" class="element">
			<h2><?php echo __('Description');?></h2>
			<div class="element-text">
				<?php echo htmlspecialchars_decode(metadata('tour','Description')); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if(metadata('tour','postscript_text')): ?>
		<div id="postscript_text" class="element">
			<h2><?php echo __('Postscript Text');?></h2>
			<div class="element-text">
				<?php echo htmlspecialchars_decode(metadata('tour','postscript_text')); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if($tour->Tags):?>
		<div id="tour_tags" class="element">
			<h2><?php echo __('Tags');?></h2>
			<div class="element-text">
				<?php echo tag_string($tour,'tours');?>
			</div>
		</div>
	<?php endif; ?>

	<?php
	$items = $tour->getItems();
	if($tour->getItems()): ?>
		<div id="tour-items" class="element">
			<h2><?php echo __('Items');?></h2>
			<div class="element-text">
				<ul>
					<?php foreach( $items as $item ):?>
						<?php set_current_record('item', $item, true);?>
						<li>
							<?php echo link_to_item($tour->tourItemTitleString($item)).
							(!$item->public ? '&nbsp;('.__('Private').')' : null); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>
</section>

<section class="three columns omega">
	<div id="edit" class="panel">
		<?php if(is_allowed('Curatescape_CuratescapeTours', 'edit')): ?>
			<a href="<?php echo url(array( 'action' => 'edit','id' => $tour->id), 'tourAction' );?>"
				class="edit big green button"><?php echo __('Edit'); ?></a>
		<?php endif; ?>

		<a href="<?php echo html_escape(public_url( 'tours/show/'.$tour->id)); ?>"
			class="big blue button" target="_blank"><?php echo __('View Public Page'); ?></a>

		<?php if(is_allowed('Curatescape_CuratescapeTours', 'delete')): ?>
			<a href="<?php echo url(array( 'action' => 'delete-confirm','id' => $tour->id), 'tourAction' );?>"
			class="delete-confirm big red button"><?php echo __('Delete'); ?></a>
		<?php endif; ?>
	</div>

	<div class="ordinal panel">
		<p>
			<span class="label"><?php echo __('Custom Order'); ?>:</span>
			<?php echo $tour->ordinal ? $tour->ordinal : __('None'); ?>
		</p>
	</div>

	<div class="public-featured panel">
		<p>
			<span class="label"><?php echo __('Public'); ?>:</span>
			<?php echo ($tour->public) ? __('Yes') : __('No'); ?>
		</p>
		<p>
			<span class="label"><?php echo __('Featured'); ?>:</span>
			<?php echo ($tour->featured) ? __('Yes') : __('No'); ?>
		</p>
	</div>

	<div class="panel">
		<h4><?php echo __('Output Formats'); ?></h4>
		<div><?php echo output_format_list(); ?></div>
	</div>
</section>

<?php echo foot(); ?>
