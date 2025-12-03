<?php
// Structured to maximize out-of-the-box theme compatibility at some expense to semantics, aesthetics, etc.
// To customize, copy the contents of this file to your-theme/curatescape/tours/show.php 
echo head(array('title' => metadata('tour', 'title'), 'bodyid'=>'tours', 'bodyclass' => 'show'));
$tourItems = $tour->Items;
?>
<h1 id="tourtitle"><?php echo metadata('tour', 'title'); ?></h1>
<div id="tour-content-container">
	<article id="tour-content" aria-labelledby="tourtitle">
		<div class="tour-description">
			<?php echo normalizeTextBlocks(metadata('tour', 'Description'));?>
		</div>
		<div class="tour-map">
			<?php if(count($tourItems)):?>
				<?php 
				if(option('curatescape_map_mirror_geolocation')){
					echo get_view()->CuratescapeMap()->GeolocationShortcode(null, $tour, null, "tour-items-map");
				}
				else{
					echo get_view()->CuratescapeMap()->Multi(null, false, "multi", $tour->id, WEB_ROOT.'/tours/show/'.$tour->id.'?output=mobile-json');
				}
				?>
			<?php endif;?>
		</div>
		<div class="tour-items">
			<?php if(count($tourItems)):?>
				<?php if($tourItemsDiplay = $tour->tourItemsOutput()):?>
					<h2><?php echo storyLabelString(true);?> 
						<span class="tour-item-count" aria-label="<?php echo __('(%s total)', count($tourItems));?>">
							<?php echo count($tourItems);?>
						</span>
					</h2>
					<div class="tour-items-browse">
						<?php echo $tourItemsDiplay;?>
					</div>
				<?php endif;?>
			<?php else:?>
				<p class="tour-no-items">
					<?php echo __('This %1s does not have any %2s.', strtolower(tourLabelString()), strtolower(storyLabelString(true)));?>
				</p>
			<?php endif;?>
		</div>

		<?php if($colophon = $tour->tourColophon()):?>
			<div class="tour-colophon" role="doc-colophon" aria-label="<?php echo __('%s Information', tourLabelString());?>">
				<?php echo $colophon;?>
			</div>
		<?php endif;?>
	</article>
</div>
<?php echo foot(); ?>