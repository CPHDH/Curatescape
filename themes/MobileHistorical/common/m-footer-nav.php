<div data-role="footer">
		<div data-role="navbar">
    			<ul class="navigation">
    			    <li><a href="<?php echo uri('/')?>" <?php if (get_theme_option('stealth_mode')==1){echo 'target="_self"';}else{echo 'data-direction="reverse"';} ;?>>Home</a></li>
    			    <li><a href="<?php echo uri('/items/')?>"  >Browse</a></li>
    			    <li><a href="<?php echo uri('/tour-builder/tours/browse/')?>"  >Tours</a></li>
    			</ul>
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
