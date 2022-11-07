	</div>
	<!--end page content-->

	<footer class="primary">
	    <div class="max-content-width inner-padding">
	        <div id="footer-search-container">
	            <?php echo rl_simple_search('footer-search', array('id'=>'footer-search-form','class'=>'capsule'), __('Search - Footer'));?>
	            <div class="search-options">
	                <?php echo '<a href="'.url('items/search').'">'.__('Advanced %s Search', rl_item_label()).' &#9656;</a>';?><br>
	                <?php echo '<a href="'.url('search').'">'.__('Sitewide Search').' &#9656;</a>';?>
	            </div>
	        </div>

	        <div class="funlinks">
	            <?php echo rl_find_us('transparent-on-dark');?>
	            <?php if (rl_appstore_downloads()) {
    				echo '<div class="footer-appstore-links">'.rl_appstore_downloads().'</div>';
				};?>
	        </div>

	        <nav id="footer-nav" aria-label="<?php echo __('Footer Navigation');?>">
	            <?php echo rl_global_nav(); ?>
	        </nav>

	        <div class="footer-random">
	            <?php echo  random_item_link(rl_icon("dice").__("View a Random %s",rl_item_label('singular')), 'random-button button');?>
	        </div>

	        <div id="project-meta">
	            <div class="separator wide"></div>
	            <div class="meta copyright"><?php echo rl_license();?></div>
	            <div class="meta powered-by"><?php echo __('Powered by <a href="http://omeka.org/">Omeka</a> + <a href="http://curatescape.org">Curatescape</a>');?></div>
	        </div>

	        <div id="custom-meta">
	            <div class="custom"><?php echo get_theme_option('custom_footer_html');?></div>
	        </div>

	        <?php echo fire_plugin_hook('public_footer', array('view'=>$this)); ?>
	        <?php echo rl_google_analytics();?>

	    </div>
	</footer>
	</div> <!-- end site-content -->
	</body>

	</html>