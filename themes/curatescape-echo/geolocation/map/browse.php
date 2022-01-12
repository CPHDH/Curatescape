<?php
echo head(array('title'=>__('Map'),'bodyid'=>'map','bodyclass'=>'item-map')); ?>

<div id="content" role="main" class="wide">
	<div id="primary" class="show">
		<?php echo rl_homepage_map(false,$totalItems);?>
	</div>
</div> <!-- end content -->
<?php echo foot(); ?>

