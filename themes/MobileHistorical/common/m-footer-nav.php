<div data-role="footer">
		<div data-role="navbar">
			<?php echo mh_global_nav('mobile_foot');?>
		</div> <!-- end nav -->	
		<?php if (get_theme_option('twitter_username')!=null){
		$username=get_theme_option('twitter_username');
		echo '<h4>follow <a href="http://twitter.com/'.$username.'">@'.$username.'</a> on twitter</h4>';
		}
		else{
		echo '<h4>'.settings('site_title').'</h4>';
		}
		?>
	
</div><!-- /footer -->
