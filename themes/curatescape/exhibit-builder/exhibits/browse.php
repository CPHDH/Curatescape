<?php 
$tag = (isset($_GET['tag']) ? $_GET['tag'] : null);
$tags = (isset($_GET['tags']) ? $_GET['tags'] : null);
$term = (isset($_GET['term']) ? $_GET['term'] : null);
$query = (isset($_GET['search']) ? $_GET['search'] : null);
$advanced = (isset($_GET['advanced']) ? true : false);
$bodyclass='browse';
$maptype='focusarea';

if ( (isset($tag) || isset($tags)) && !isset($query) ) {
	$title = __('Exhibits tagged "%s"',($tag ? $tag : $tags));
	$bodyclass .=' queryresults';
	$maptype='queryresults';
}
elseif ( isset($term) && !isset($query) ) {
	$title = __('Results for subject term "%s"',$term);
	$bodyclass .=' queryresults';
	$maptype='queryresults';
}
elseif (isset($query)) {
	$title = (!($advanced) ? __('Search Results for "%s"',$query) :__('Advanced Search Results'));
	$bodyclass .=' queryresults';
	$maptype='queryresults';
}	
else{
	$title = __('All Exhibits');
	$bodyclass .=' exhibits stories';
}	
echo head(array('maptype'=>'none','title'=>$title,'bodyid'=>'exhibits','bodyclass'=>$bodyclass)); 
?>


<div id="content" role="main">

<article class="browse stories items">	
	<h2 class="query-header"><?php 
	$title .= ( ($total_results) ? ': <span class="item-number">'.$total_results.'</span>' : '');
	echo $title; 
	?></h2>

	<section id="results">
			

	<?php if (count($exhibits) > 0): ?>
	
		<nav class="secondary-nav" id="item-browse"> 
		    <?php echo nav(array(
		        array(
		            'label' => __('All'),
		            'uri' => url('exhibits')
		        ),
		        array(
		            'label' => __('Tags'),
		            'uri' => url('exhibits/tags')
		        )
		    )); ?>
		</nav>    
    
	
    <div class="pagination"><?php echo pagination_links(); ?></div>
	
    <div id="exhibits">	
		<?php $exhibitCount = 0; ?>
		<?php foreach (loop('exhibit') as $exhibit): ?>
		    <?php $exhibitCount++; ?>
		    <div class="exhibit <?php if ($exhibitCount%2==1) echo ' even'; else echo ' odd'; ?>">
		        <h3><?php echo link_to_exhibit(); ?></h3>
		        <?php if ($exhibitDescription = metadata('exhibit', 'description', array('no_escape' => true))): ?>
		        <div class="description"><?php echo strip_tags($exhibitDescription); ?></div><br><br>
		        <?php endif; ?>
		    </div>
		<?php endforeach; ?>
    </div>
    
    <div class="pagination"><?php echo pagination_links(); ?></div>

    <?php else: ?>
	<p><?php echo __('There are no exhibits available yet.'); ?></p>
	<?php endif; ?>
				
	</section>	
	
	<?php echo mh_share_this();?>

</article>
</div> <!-- end content -->



<?php echo foot(); ?>