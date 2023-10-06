<?php
	require_once 'functions.php'; 
	$subjects = sb_get_subjects();
	if(isset($_GET["sort_field"]) && $_GET["sort_field"] == 'name' && isset($_GET["sort_dir"]) && $_GET["sort_dir"] == 'd'){
		arsort($subjects); // z-a
	}elseif(isset($_GET["sort_field"]) && $_GET["sort_field"] == 'count' && isset($_GET["sort_dir"]) && $_GET["sort_dir"] == 'a'){
		usort($subjects, 'sb_ascend'); // least
	}elseif(isset($_GET["sort_field"]) && $_GET["sort_field"] == 'count' && isset($_GET["sort_dir"]) && $_GET["sort_dir"] == 'd'){
		usort($subjects, 'sb_descend'); // most
	}elseif(isset($_GET["sort_field"]) && $_GET["sort_field"] == 'count'){
		usort($subjects, 'sb_ascend'); // least (if no sort_dir is specified)
	}else{
		asort($subjects); // a-z
	}
	$total = count($subjects);
	echo head(array('maptype'=>'none', 'title'=>__('Browse by Subject'),'bodyid'=>'items','bodyclass'=>'browse subjects'));
?>
<!-- The following markup is for the Curatescape Echo theme. -->
<!-- If using another theme, you'll need to create a custom template at yourtheme/subjects-browse/items/browse.php -->
<div id="content" role="main">
	<article class="browse subjects">
		<div class="browse-header">
			<h2 class="query-header"><?php echo __('Subjects: %s', $total);?></h2>
			<nav class="secondary-nav" id="subject-browse">
				<?php echo function_exists('rl_item_browse_subnav') ? rl_item_browse_subnav() : null; ?>
			</nav>
			<div id="helper-links">
				<span class="helper-label"><?php echo function_exists('rl_icon') ? rl_icon('funnel').'&nbsp;'.__("Sort by: ") : null; ?>
				</span>
				<?php echo browse_sort_links(array('Name'=>'name','Count'=>'count')); ?>
			</div>
		</div>
		<div id="primary" class="">
			<section id="subjects" aria-label="<?php echo __('Subjects');?>">
				<?php 
				if($total > 0){
					sb_subjects_list($subjects);
				}else{
					echo '<p>'.__('No subjects are available.').'</p>';
				}
				?>
			</section>
		</div>
	</article>
</div>
<?php echo foot(); ?>