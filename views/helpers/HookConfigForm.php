<?php
class Curatescape_View_Helper_HookConfigForm extends Zend_View_Helper_Abstract{
	public function HookConfigForm(){
		?>
		<style>
			fieldset > p{
				padding:1lh;
				background-color:#F5f5f5;
				border-left:1px solid #ccc;
			}
			.intro{
				margin-top:0;
				padding:1lh;
				background-image:linear-gradient(10deg, #445a66,#15455b,#2987b3);
				color:#F5f5f5;
			}
			.intro a{
				color:#fff}legend + p{
				margin-top:-8px;
				margin-bottom:20px;
			}
			code{
				font-size: .9em;
			}
		</style>

		<p class="intro"><?php echo __('%1s adds a number of features to better support location-based narrative content. For documentation and other important information, visit %2s. For support, create an account at %3s.', _PLUGIN_NAME_, '<a href="https://curatescape.org" target="_blank">curatescape.org</a>', '<a href="https://forum.curatescape.org" target="_blank">forum.curatescape.org</a>');?></p>

		<fieldset>
			<legend><?php echo __('Item Type Settings'); ?></legend>
			<p><?php echo __('The following options change the way the <a href="%1s" target="_blank">%2s Item Type</a> is displayed to end users.', itemTypeURL(), _CURATESCAPE_ITEM_TYPE_NAME_);?></p>

			<!-- Alt Item Type Name -->
			<?php echo $this->configFormText('curatescape_alt_item_type_name', 'Alternate Name', __('Enter an alternate display name for the %1s Item Type. You may also need to adjust the corresponding link label in <a href="%2s" target="_blank">Navigation settings</a>.', _CURATESCAPE_ITEM_TYPE_NAME_, admin_url('appearance/edit-navigation')), 'Example: Story');?>
			<!-- Alt Item Type Name (Plural) -->
			<?php echo $this->configFormText('curatescape_alt_item_type_name_p', 'Alternate Name Plural', __('Enter the plural form of your alternate display name for the %s Item Type. You may also need to adjust the corresponding link label in <a href="%2s" target="_blank">Navigation settings</a>.', _CURATESCAPE_ITEM_TYPE_NAME_, admin_url('appearance/edit-navigation')), 'Example: Stories');?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Item Record Settings'); ?></legend>
			<p><?php echo __('The following options apply to Item records.');?></p>

			<!-- Recommended Template -->
			<?php echo $this->configFormCheckBox('curatescape_template', 'Metadata Template', __("If checked, the Item metadata display will be modified to use an enhanced layout for Items using the %s Item Type. Other Item Types will fall back to the Omeka default template.", _CURATESCAPE_ITEM_TYPE_NAME_) );?>
			<!-- Link Select Elements -->
			<?php echo $this->configFormCheckBox('curatescape_metadata_browse', 'Link Select Elements', "If checked, the Creator and Subject metadata elements will automatically be converted into hyperlinks that point to related Items. Note that this feature may conflict with Search By Metadata plugin." );?>
			<!-- Auto Subtitle -->
			<?php echo $this->configFormCheckBox('curatescape_auto_subtitle', 'Append Subtitle', 'If checked, the Subtitle will be automatically appended to the Title anywhere it is displayed, including the record title, &lt;meta&gt; title, and more.');?>
			<!-- Subtitle Styles -->
			<?php echo $this->configFormCheckBox('curatescape_subtitle_styles', 'Subtitle Styles', 'If checked, the Subtitle will be formatted using plugin styles that change the color, typography, and layout. If unchecked, the Subtitle will be separated from the title by a colon. Requires use of auto-appending subtitle option, selected above. May not be supported in some themes.');?>
			<!-- Repeat Story Header -->
			<?php echo $this->configFormCheckBox('curatescape_inner_heading', 'Repeat Story Header', 'If checked, the Title and Subtitle will be displayed above the Lede, even if they have already appeared on the page. This might be useful for themes that display the Item files at the top of the page.');?>
			<!-- Byline -->
			<?php echo $this->configFormSelect('curatescape_byline', 'Byline Location', 'Select the location where the formatted byline should be displayed.', array( 'after_lede' => __('After Lede (default)'), 'before_lede' => __('Before Lede'), 'none' => __('None (do not display)') ) );?>
			<!-- Inline Factoids -->
			<?php echo $this->configFormCheckBox('curatescape_inline_factoids', 'Inline Factoids', 'If checked, Factoids will be displayed within the main text. If the main text is more than five paragraphs in length, Factoids will be displayed after the third paragraph. Factoids are always wrapped in an &lt;aside&gt; tag for optimal accessibility.');?>
			<!-- Factoid Label -->
			<?php echo $this->configFormText('curatescape_factoids_label', 'Factoids Label', 'Enter a custom label to replace the default heading for Factoids.', 'Example: Did you know?');?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('File Display Settings'); ?></legend>
			<p><?php echo __('The following options control how files are displayed on Item and File records.');?></p>

			<!-- Media Style-->
			<?php echo $this->configFormSelect('curatescape_gallery_style', 'Media Gallery', 'Select the style to be used for displaying images and other media files. Each style will adapt to the available space as determined by the theme layout and browser dimensions. May not be supported by all themes.', array( 'gallery-grid' => __('Thumbnail Grid (default)'), 'gallery-inline-captions' => __('Inline Captions'), 'gallery-slides' => __('Slides'), 'gallery-table' => __('Files Table'), 'none' => __('None (use theme)') ) );?>
			<!-- Image Lightbox-->
			<?php echo $this->configFormCheckBox('curatescape_lightbox', 'Image Lightbox', 'If checked, image links will open in lightbox overlay (PhotoSwipe). If unchecked, image links will open to either the file or the file record, based on site settings. Requires use of plugin media styles, selected above.');?>
			<!-- Docs in Lightbox-->
			<?php echo $this->configFormCheckBox('curatescape_lightbox_docs', 'PDF Lightbox', 'If checked, PDF document files will be presented alongside images and use the lightbox overlay (PhotoSwipe) with select gallery types. Note that the presentation of PDF document files will vary across different browsers and devices. If unchecked, PDF document files will be listed uniformly in a separate table when the Thumbnail Grid gallery type is active.');?>
			<!-- Files Show File Markup-->
			<?php echo $this->configFormCheckBox('curatescape_file_markup', 'File Record Markup', 'If checked, the HTML for files on each single file record (i.e. files/show) will use Curatescape style markup. Potentially useful for projects with audio, video, and PDF files.');?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Tour Settings'); ?></legend>
			<p><?php echo __('Items using the %s Item Type (along with Geolocation data) can be collected into walking, cycling, and driving tours. The following options apply to Tours.', _CURATESCAPE_ITEM_TYPE_NAME_);?></p>
			
			<!-- Alt Tour Name -->
			<?php echo $this->configFormText('curatescape_alt_tour_name', 'Alternate Name', __('Enter an alternate display name for Tours. You may also need to adjust the corresponding link label in <a href="%s" target="_blank">Navigation settings</a>.', admin_url('appearance/edit-navigation')), 'Example: Tour');?>
			<!-- Alt Tour Name (Plural) -->
			<?php echo $this->configFormText('curatescape_alt_tour_name_p', 'Alternate Name Plural', __('Enter the plural form of your alternate display name for Tours. You may also need to adjust the corresponding link label in <a href="%s" target="_blank">Navigation settings</a>.', admin_url('appearance/edit-navigation')), 'Example: Tours');?>
			<!-- Thumb Style-->
			<?php echo $this->configFormSelect('curatescape_tour_thumb_style', 'Tour Thumbnail', 'Select the style of thumbnail image to use when browsing tours. The composite style combines the first image from the first four Items on the Tour to create a mini-collage.', array( 'composite' => __('Composite (default)'), 'first-image' => __('First image from first tour stop'), 'none' => __('None') ) );?>
			<!-- Tour Item Gallery Style-->
			<?php echo $this->configFormSelect('curatescape_gallery_style_tour', 'Tour Item Gallery', 'Select the style to be used for displaying tour items in list form. Each style will adapt to the available space as determined by the theme layout and browser dimensions.', array( 'gallery-inline-captions' => __('Inline Captions (default)'), 'gallery-grid' => __('Thumbnail Grid'), 'none' => __('None') ) );?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Homepage Settings'); ?></legend>
			<p><?php echo __('Use these settings to add content to the homepage.');?></p>
			<!-- Home Map -->
			<?php echo $this->configFormSelect('curatescape_home_map', 'Map Location', __('Select a location on the homepage for displaying a map of %s.', _CURATESCAPE_ITEM_TYPE_NAME_PLURAL_), array( 'top' => __('Top (default)'), 'bottom' => __('Bottom'), 'none' => __('None')));?>
			<!-- Home Map Heading -->
			<?php echo $this->configFormText('curatescape_home_map_heading', 'Map Heading', __('Enter a text heading for the homepage map or leave blank to omit the heading.'), 'Example: Story Map');?>
			<!-- Home Map Caption-->
			<?php echo $this->configFormText('curatescape_home_map_caption', 'Map Caption', __('Enter a text caption to display below the homepage map or leave blank to omit the caption. If blank, a basic description of the map will be made available to screen readers. HTML links are allowed.'), 'Example: Use the Story Map to explore the area.');?>
		</fieldset>
		
		<fieldset>
			<legend><?php echo __('Navigation Settings'); ?></legend>
			<p><?php echo __('The following settings apply to the primary and secondary/Item navigation menus, as well as the Omeka admin bar.');?></p>

			<!-- Admin Bar Edit-->
			<?php echo $this->configFormCheckBox('curatescape_admin_bar_edit', 'Admin Bar', 'If checked, an "Edit" link will be appended to the default Omeka admin bar. Theme support varies. May conflict with other plugins that add this feature.');?>
			<!-- Primary Nav -->
			<?php echo $this->configFormCheckBox('curatescape_append_primary_nav', 'Append Primary', __('If checked, new navigation items for Home and %1s will be added to the primary navigation menu. If you configure an alternate Item Type name, the modified version will be used instead of the default label. You can modify or disable these items in sitewide <a href="%2s" target="_blank">Navigation settings</a>.', _CURATESCAPE_ITEM_TYPE_NAME_PLURAL_, admin_url('appearance/edit-navigation')));?>
			<!-- Secondary Nav -->
			<?php echo $this->configFormCheckBox('curatescape_append_secondary_nav', 'Append Secondary', 'If checked, new navigation items for Featured and Curatescape Stories will be added to the secondary/Item navigation for the items/browse view.');?>
			<!-- Secondary Nav Shorten -->
			<?php echo $this->configFormCheckBox('curatescape_shorten_secondary_nav', 'Shorten Secondary', 'If checked, the secondary/Item navigation labels will be shortened to preserve space in some themes. For example, "Browse By Tags" will be shortened to "Tags."');?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Output Settings'); ?></legend>
			<p><?php echo __('These settings apply to the various output formats available via the %s plugin.', _PLUGIN_NAME_);?></p>

			<?php echo $this->configFormSelect('curatescape_json_cache', 'JSON Browser Cache', __('When enabled, Curatescape JSON feeds (e.g. for <a href="%1s" target="_blank">items/browse</a>) will be cached in-browser for the selected duration. Browser cache rules are bypassed for logged in users and apply only to repeat loads within the selected duration.', items_output_url('mobile-json') ), array( '0' => __('Disable cache'), '60' => __('1 minute'), '300' => __('5 minutes'), '600' => __('10 minutes (default)'), '1800' => __('30 minutes'), '3600' => __('1 hour'), '43200' => __('12 hours'), '86400' => __('24 hours') ) );?>

			<!-- JSON Server Cache  -->
			<?php 
			$itemjson = WEB_ROOT.'/plugins/'._PLUGIN_NAME_.str_replace(_PLUGIN_DIR_, '', _JSON_ITEMS_FILE_);
			$tourjson = WEB_ROOT.'/plugins/'._PLUGIN_NAME_.str_replace(_PLUGIN_DIR_, '', _JSON_TOURS_FILE_);
			$cacheDebugLinks = '<div class="explanation"><a class="button" target="_blank" href="/items/browse?output=mobile-json&curatescape_cache_break=debug">'.__('Clear & Debug Items Cache').'</a><a class="button" target="_blank" href="/tours/browse?output=mobile-json&curatescape_cache_break=debug">'.__('Clear & Debug Tours Cache').'</a></div>';
			echo $this->configFormSelect('curatescape_json_storage', 'JSON Server Cache', __('When enabled, certain Curatescape JSON feeds will be cached and stored on the server for the selected duration. Increase the duration to improve performance and reduce memory usage for large datasets. Disable this option if you experience errors relating to your server security configurations. All server cache files are automatically cleared when saving an item. The server cache is bypassed for logged in users. %s', $cacheDebugLinks), array( '0' => __('Disable cache'), '60' => __('1 minute'), '300' => __('5 minutes (default)'), '600' => __('10 minutes'), '1800' => __('30 minutes'), '3600' => __('1 hour'), '43200' => __('12 hours'), '86400' => __('24 hours') ) );?>

			<!-- Enhanced RSS-->
			<?php echo $this->configFormCheckBox('curatescape_rss', 'Enhanced RSS', __('If checked, the <a href="%1s" target="_blank">default RSS feed</a> will be replaced with an <a href="%2s" target="_blank">enhanced format</a> that has been customized for narrative content.', items_output_url('rss2'), items_output_url('rss-plus')));?>
		</fieldset>
		
		<fieldset>
			<legend><?php echo __('Preview Settings'); ?></legend>
			<p><?php echo __('The following options control the way your content is represented on search engines and social media websites.');?></p>

			<!-- Meta Tags -->
			<?php echo $this->configFormCheckBox('curatescape_meta_tags', 'Meta Tags', __('If checked, use enhanced &lt;meta&gt; tags for improved search engine optimization and social media sharing.', _PLUGIN_NAME_) );?>
			<!-- Meta Image -->
			<?php echo $this->configFormText('curatescape_meta_image', 'Meta Image', __('Enter the URL for a PNG or JPG file to serve as the fallback image to represent your site on social media and search engine results. Used only when there is not a content-related image available (for example, on the homepage and browse pages). Recommended dimensions: 1200px × 630px (1.91:1). Alternately, theme developers may create the following theme option to automatically replace this plugin setting with an uploaded file from the theme: %s','<code>curatescape_meta_image.type="file"</code>'), 'Example: '.WEB_ROOT.'/meta.png');?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Analytics'); ?></legend>
			<p><?php echo __('The following options control the settings for third-party analytics integrations.');?></p>

			<!-- Google Analytics -->
			<?php echo $this->configFormText('curatescape_google_analytics', 'Google Analytics', 'Enter the Web Stream Measurement ID for this website as shown in your Google Analytics account dashboard. May conflict with themes and plugins that already include Google Analytics by other means.', 'Example: G-0123456789');?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('App Store Settings'); ?></legend>
			<p><?php echo __('Did you know that Curatescape is a not-for-profit, university-based project with a mission to build (surprisingly affordable) mobile apps for humanities organizations? Learn more at %1s. The following options apply to projects with corresponding mobile apps. To display app store buttons use the shortcode: %2s. Refer to plugin documentation for full list of shortcode options.', '<a href="https://curatescape.org" target="_blank">curatescape.org</a>', '<code>[curatescape_app_buttons icons="true"]</code>');?></p>

			<!-- iOS-->
			<?php echo $this->configFormText('curatescape_app_ios', 'iOS App', 'Enter the numeric identifier for the iOS app, found in Apple App Store Connect account.', 'Example: 0123456789');?>
			<!-- Android-->
			<?php echo $this->configFormText('curatescape_app_android', 'Android App', 'Enter the alphanumeric package name/identifier for the Android app, found in Google Play Console account.', 'Example: com.developer.appname');?>
		</fieldset>
		
		<fieldset>
			<legend><?php echo __('Dashboard Settings'); ?></legend>
			<p><?php echo __('The following options enable the addition of useful information, tools, and resources to the <a href="/admin/" target="_blank">admin dashboard</a>.');?></p>
			
			<!-- Content Audit -->
			<?php echo $this->configFormCheckBox('curatescape_dashboard_audit', 'Content Audit', __('If checked, the admin dashboard will display the results of a content audit to ensure that authors are employing the recommended best practices for %s. Audit results are cached on the server side.', _CURATESCAPE_ITEM_TYPE_NAME_PLURAL_) );?>
			<!-- File Stats -->
			<?php echo $this->configFormCheckBox('curatescape_dashboard_stats', 'File Statistics', __('If checked, the admin dashboard will display file statistics and format recommendations. File statistics are cached on the server side.', _PLUGIN_NAME_) );?>
			<!-- Project Admin -->
			<?php echo $this->configFormCheckBox('curatescape_dashboard_project_mgmt', 'Project Management', __('If checked, the admin dashboard will display convenient links to manage configured app store and analytics accounts. See relevant options above.') );?>
			<!-- Resources -->
			<?php echo $this->configFormCheckBox('curatescape_dashboard_resources', 'Resources', __('If checked, the admin dashboard will display a list of useful %s resources.', _PLUGIN_NAME_) );?>
			
		</fieldset>

		<fieldset>
			<legend><?php echo __('Advanced Settings'); ?></legend>
			<p><?php echo __('The following options may be especially useful for site administrators, designers, developers, and other advanced users. Come back here if you are troubleshooting a specific issue.');?></p>

			<!-- Plugin Styles -->
			<?php echo $this->configFormCheckBox('curatescape_plugin_styles', 'Plugin Styles', __('If checked, use CSS styles provided by the %1s plugin. Turning this off may be useful for theme developers, but should otherwise remain checked.', _PLUGIN_NAME_) );?>
			<!-- Theme Fixes -->
			<?php echo $this->configFormCheckBox('curatescape_theme_fixes', 'Theme Fixes', __('If checked, use theme-specific CSS styles provided by the %1s plugin. These styles apply to select themes and only address issues related to the %2s plugin.', _PLUGIN_NAME_, _PLUGIN_NAME_));?>
			<!-- Filter Text-->
			<?php echo $this->configFormCheckBox('curatescape_filter_text', 'Filter Text', __('If checked, use additional text filters when saving Items. %s elements will be filtered to remove unsupported HTML. Invalid markup from desktop word processing software will be removed from all Item elements. Required for mobile app projects and generally recommended for all.', _CURATESCAPE_ITEM_TYPE_NAME_));?>
			<!-- Format Warnings-->
			<?php echo $this->configFormCheckBox('curatescape_format_warnings', 'Format Warnings', 'If checked, display a warning after the user has saved an Item that appears to violate the recommended formatting rules (for example, by including a new line within a Subject term). This option does not result in the modification of any content. Required for mobile app projects and generally recommended for all.');?>
			<!-- Form Enforcement -->
			<?php echo $this->configFormCheckBox('curatescape_form_enforcement', 'Form Enforcement', __('If checked, the "Use HTML" and "Add Input" buttons in the Item form will be disabled for select elements in order to enforce the recommended formatting rules.') );?>
			<!-- Omit Redundant -->
			<?php echo $this->configFormCheckBox('curatescape_omit_redundant_elements', 'Omit Redundant', __('If checked, the Title and Subtitle elements for the Item will be omitted from the metadata table since they already appear as part of the page heading. Likewise, the Coverage element will be omitted since it already appears as part of the map caption (which consists of the Street Address and Access Information texts).', _CURATESCAPE_ITEM_TYPE_NAME_) );?>
		</fieldset>

		<?php
	}

	private function configFormCheckBox($optionName, $labelName, $helperText){
		if(!$optionName || !$labelName || !$helperText) return null;
		?>
		<div class="field">
			<div class="two columns alpha">
				<label for="<?php echo $optionName;?>"><?php echo __($labelName); ?></label>
			</div>
			<div class="inputs five columns omega">
				<p class="explanation"><?php echo __($helperText); ?></p>
				<?php echo get_view()->formCheckbox($optionName, true,
				array('checked'=>(boolean)get_option($optionName))); ?>
			</div>
		</div>
		<?php
	}

	private function configFormSelect($optionName, $labelName, $helperText, $options=array()){
		if(!$optionName || !$labelName || !$helperText || !count($options)) return null;
		?>
		<div class="field">
			<div class="two columns alpha">
				<label for="<?php echo $optionName;?>"><?php echo __($labelName); ?></label>
			</div>
			<div class="inputs five columns omega">
				<p class="explanation"><?php echo __($helperText); ?></p>
				<?php echo get_view()->formSelect($optionName, get_option($optionName), null, $options); ?>
			</div>
		</div>
		<?php
	}

	private function configFormText($optionName, $labelName, $helperText, $placeholder=null){
		if(!$optionName || !$labelName || !$helperText) return null;
		?>
		<div class="field">
			<div class="two columns alpha">
				<label for="<?php echo $optionName;?>"><?php echo __($labelName); ?></label>
			</div>
			<div class="inputs five columns omega">
				<p class="explanation"><?php echo __($helperText); ?></p>
				<?php echo get_view()->formText($optionName, get_option($optionName), array('placeholder' => __($placeholder))); ?>
			</div>
		</div>
		<?php
	}
}