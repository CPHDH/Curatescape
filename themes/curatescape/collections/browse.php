<?php 
if(!$collections){
	// We don't use the Collections on the front-end
	include_once($_SERVER["DOCUMENT_ROOT"] . "/themes/curatescape/error/404.php");
}else{
$total=count($collections);
$title=__('Browse Collections');
$bodyclass = 'collections browse';
$bodyid='collections';
echo head(array('maptype'=>'none','title' => $title, 'bodyclass' => $bodyclass, 'bodyid' => $bodyid)); 

?>

<div id="content" role="main">
<article class="browse collection">			
	<h2 class="query-header"><?php echo $title.': '.$total;?></h2>

	<div id="primary" class="browse">
	
	<section id="results">
	<nav class="collections-nav navigation secondary-nav">
	  <?php echo mh_collection_browse_subnav(); ?>
	</nav>	
	<div class="pagination bottom"><?php echo pagination_links(); ?></div>

	<?php foreach($collections as $collection): ?>

		    <article class="collection item-result">
		        <h3><?php echo link_to($collection,'show', metadata($collection, array('Dublin Core','Title')), array('class'=>'permalink') ) ?></h3>
		        <div class="collection-description"><?php echo snippet(metadata($collection, array('Dublin Core','Description')),0,500); ?></div>
		    </article><br><br>
		
	<?php endforeach; ?>

	
    
	</section>
    </div>

	
	<div class="pagination bottom"><?php echo pagination_links(); ?></div>
	<?php echo mh_share_this(); ?>
</article>
</div> <!-- end content -->



<?php echo foot();?>

<?php } ?>