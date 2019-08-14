	
	</div><!--end wrap-->
	
	<footer class="main container">
		<nav id="footer-nav" aria-label="<?php echo __('Footer Navigation');?>">
		    <?php echo mh_global_nav(); ?> 
		    <?php echo mh_simple_search('footer-search',array('id'=>'footer-search-form'),__('Search - Footer'));?>
		    <?php echo random_item_link("<i class='fa fa-random fa-lg' aria-hidden='true'></i> View A Random ".mh_item_label('singular'),'random-button button');?>
		    <?php echo mh_appstore_downloads();?>
	    </nav>	
	 
		<div class="default">
			<?php echo mh_footer_find_us();?>
			<div id="copyright"><?php echo mh_license();?></div> 
			<div id="powered-by"><?php echo __('Powered by <a href="http://omeka.org/">Omeka</a> + <a href="http://curatescape.org">Curatescape</a>');?></div>
		</div>
		
		<?php echo mh_footer_cta();?>
		
		<div class="custom"><?php echo get_theme_option('custom_footer_html');?></div>
	
		<?php echo fire_plugin_hook('public_footer', array('view'=>$this)); ?>	
		<?php echo mh_google_analytics();?>	
			
	</footer>
</div> <!-- end page-content -->

<div hidden class="hidden">
	<!-- Mmenu Markup -->
	<?php echo mh_simple_search('sidebar-search',array('id'=>'sidebar-search-form'),__('Search - Drawer'));?>
	<nav aria-label="<?php echo __('Drawer Navigation');?>" id="offscreen-menu">
		<?php echo mh_global_nav(true);?>
	</nav>
</div>
	
</body>
</html>