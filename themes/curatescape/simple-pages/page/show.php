<?php 
$bodyclass = 'page simple-page show';
echo head(array('maptype'=>'none','title' => html_escape(metadata('simple_pages_page', 'title')), 'bodyclass' => $bodyclass, 'bodyid' => html_escape(metadata('simple_pages_page', 'slug')))); ?>

<div id="content">
	<article class="page show">
		<h2 class="instapaper_title"><?php echo metadata('simple_pages_page', 'title'); ?></h2>
	
		<section id="text">
			    <?php
			    $text = metadata('simple_pages_page', 'text', array('no_escape' => true));
			    echo $this->shortcodes($text);
			    ?>					    
		</section>    
		
		<?php echo mh_share_this();?>
	
	</article>

</div> <!-- end content -->

<?php echo foot(); ?>