<!--STEALTH MODE-->

		<?php head(array(),'s-header'); ?>	
		
		<div id="app-badge">
			<?php 
			if (get_theme_option('enable_app_links')){ 
	
				$ios_link = get_theme_option('ios_link');
				echo ($ios_link ? '<a href="'.$ios_link.'">
				<img src="'.img('app-store-badge.gif').'" alt="Available at the iPhone App Store" border="0"/>
				</a>':'');
				
				$android_link = get_theme_option('android_link');
				echo ($android_link ? '<a href="'.$android_link.'">
				<img src="'.img('btn-android.png').'" alt="Available at the Android Market" border="0"/>
				</a>':'');
			
			} ?>
		</div>
		
		<div style="clear:both;"></div>
	
		<div id="header">
			<div id="logo">
				<img src="<?php echo mh_stealth_logo_url();?>" alt="<?php echo settings('site_title');?>" border=0 />
			</div>

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
				<?php 
					
					$contact_email = get_theme_option('contact_email');
					echo ($contact_email ? '<h3 class="contact"><small>Email:</small> <a href="mailto:'.$contact_email.'">'.$contact_email.'</a></h3>' : '');
					
					$twitter_username = get_theme_option('twitter_username');
					echo ($twitter_username ? '<h3 class="contact"><small>Twitter:</small> <a href="http://twitter.com/'.$twitter_username.'">@'.$twitter_username.'</a></h3>' : '');

					$contact_phone = get_theme_option('contact_phone');
					echo ($contact_phone ? '<h3 class="contact"><small>Phone:</small> '.$contact_phone.'</h3>' : '');
					
					$contact_address = get_theme_option('contact_address');
					echo ($contact_address ? '<p>'.$contact_address.'</p>' : '');									
						
				?>			
				
				
				</div>
				
				
				<div id="col3">
				<h2>Donate</h2>
				<p>Your donation helps us continue to provide the <?php echo settings('site_title');?> App.  Future updates will include additional content, new features and more, but we will need your support.  Help us keep this app free and continue to keep our history history alive.</p>
				<br />
				<!--start button-->

				<?php
				$donate_button = get_theme_option('donate_button');
				echo ($donate_button ? '<p>'.$donate_button.'</p>' : '<p>Please contact us directly if you would like to support the project.</p>');
				?>	


				<!-- end button-->
				
				</div>				


			</div><!--#cols-->
		
		
		</div><!--#content-->
	</div><!--#container-->
	
<div id="footer">
<p>Powered by <a href="http://omeka.org/">Omeka</a> + <a href="http://curatescape.org">Curatescape</a>
<br/>
&copy; <?php echo date('Y').' '.settings('author');?> 
</p>
</div>

</div><!-- end content -->

</div><!--end wrap-->

</body>

</html>