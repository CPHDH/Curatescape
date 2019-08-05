<?php 
$query = (isset($_GET['query']) ? htmlspecialchars($_GET['query']) : null);
$searchRecordTypes = get_search_record_types();
$title = __('Search %s', mh_item_label('plural'));
$bodyclass ='browse advanced-search'.(current_user() ? ' logged-in' : null);
$maptype='none';


echo head(array('maptype'=>$maptype,'title'=>$title,'bodyid'=>'search','bodyclass'=>$bodyclass)); 
?>


<div id="content">

<article class="search browse">	
	<h2 class="query-header"><?php echo $title; ?></h2>
		


	<div id="primary" class="browse">
	<section id="results">
			
		<nav class="secondary-nav" id="item-browse"> 
			<?php echo mh_item_browse_subnav();?>
		</nav>
		


		<?php echo $this->partial('items/search-form.php',
			array('formAttributes' =>array('id'=>'advanced-search-form'))); ?>
        
				
	</section>	
	</div><!-- end primary -->

	<?php echo mh_share_this();?>
</article>
</div> <!-- end content -->



<?php echo foot(); ?>