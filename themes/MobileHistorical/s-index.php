<!--STEALTH MODE-->

		<?php head(array(),'s-header'); ?>	
		<div id="app-badge"><a href="<?php get_theme_option('ios_link');?>"><img src="<?php echo img('app-store-badge.gif');?>" alt="Available at the iPhone App Store" border="0" /></a>
		<a href="<?php get_theme_option('android_link');?>"><img src="<?php echo img('btn-android.png');?>" width="96" height="32" border="0" /></a>
		</div>
		
		
		<div style="clear:both;"></div>
	
		<div id="header">
			<div id="logo">
				<img src="<?php echo mh_stealth_logo_url();?>" alt="<?php echo settings('site_title');?>" border=0 />
			</div>
			<!--
			<div id="get-app">
				<img src="<?php// echo img('getapp.gif');?>" alt="Get the App:" border=0 />
				<a href="http://ax.itunes.apple.com/us/app/cleveland-historical/id401222855?mt=8#ls=1"><img src="<?php// echo img('dwnload-btn.gif');?>" alt="Download Button" border=0 /></a>
			</div>
			-->
		</div> <!--#header-->

		<div id="content">
			<div id="cols">
				<div id="col1">
				<h2>About</h2>
				<h3><?php echo settings('site_title');?></h3>
				<p><?php echo mh_about();?></p>					
				</div>
				<div id="col2">
				<h2>Contact</h2>
				<h3 class="contact"><small>Email:</small> <a href="mailto:<?php echo get_theme_option('contact_email') ;?>"><?php echo get_theme_option('contact_email') ;?></a></h3>
				<?php if( get_theme_option('twitter_username') != null):?>
				<h3 class="contact"><small>Twitter:</small> <a href="http://twitter.com/<?php echo get_theme_option('twitter_username') ;?>">@<?php echo get_theme_option('twitter_username') ;?></a></h3>
				<?php endif; ?>
				<h3 class="contact"><small>Phone:</small> <?php echo get_theme_option('contact_phone') ;?></h3>
				<p>				
				<?php echo get_theme_option('contact_address') ;?>
				</p>				
				
				
				</div>
				
				
				<div id="col3">
				<h2>Donate</h2>
				<p>Your donation helps us continue to provide the <?php echo settings('site_title');?> App.  Future updates will include additional content, new features and more, but we will need your support.  Help us keep this app free and continue to keep our history history alive.</p>
				<br />
				<!--start button-->

				<p><?php echo ((get_theme_option('donate_button')!=null) ? get_theme_option('donate_button'): 'Please contact us directly if you would like to support the project.') ;?></p>

				<!-- end button-->
				
				</div>				


			</div><!--#cols-->
		
		
		</div><!--#content-->
	</div><!--#container-->
	
<div id="footer">
<p>Powered by <a href="http://omeka.org/">Omeka</a> + <a href="http://mobilehistorical.org">MobileHistorical</a>
<br/>
&copy; <?php echo date('Y').' '.settings('author');?> 
</p>
</div>

</div><!-- end content -->

</div><!--end wrap-->

</body>

</html>