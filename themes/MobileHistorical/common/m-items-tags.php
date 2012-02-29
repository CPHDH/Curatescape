<?php head( array( 'title' => 'Browse Tags') );?>
<!-- Start of page content: #tags -->


<div data-role="content" id="browse">	
<?php echo mobile_simple_search();?>
			
			<?php $term=$_GET['tags']; ?>
			<?php $term2=$_GET['term']; ?>

	    
		<h2>Browse Tags </h2>

		<div data-role="footer">
			<div data-role="navbar">
				<ul>
				<?php //echo nav(array('All' => uri('items'), 'Tags' => uri('items/tags'))); ?>
				<li><a href="<?php echo uri('items')?>" data-transition="fade">All</a></li>
				<li><a href="<?php echo uri('items/tags')?>" data-transition="fade">Tags</a></li>
				</ul>
			</div><!-- /navbar -->
		</div><!-- /footer -->
		
		 <?php echo tag_cloud($tags,uri('items/browse')); ?>

<a href="#download-app" data-role="button" data-rel="dialog" data-transition="pop" id="download-app">Download the App</a>		
</div> <!-- end content-->

<?php echo common('m-footer-nav');?>

</div> <!-- end outer page from header -->	

<?php echo common('m-dialogues');?>

</body>
</html>
