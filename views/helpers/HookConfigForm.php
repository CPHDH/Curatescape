<?php
class Curatescape_View_Helper_HookConfigForm extends Zend_View_Helper_Abstract{
	public function HookConfigForm(){
		?>
		<p class="intro"><?php echo __('%1s adds a number of features to better support location-based narrative content. For documentation and other important information, or to learn about %2s mobile apps for iOS and Android, visit %3s. For support, create an account at %4s.', _PLUGIN_NAME_, _PLUGIN_NAME_, '<a href="https://curatescape.org" target="_blank">curatescape.org</a>', '<a href="https://forum.curatescape.org" target="_blank">forum.curatescape.org</a>');?></p>

		<fieldset>
			<legend><?php echo __('Item Type Settings'); ?></legend>
			<p><?php echo __('Use the following options to change the way the <a href="%1s" target="_blank">%2s Item Type</a> is displayed to end users.', itemTypeURL(), _CURATESCAPE_ITEM_TYPE_NAME_);?></p>

			<!-- Alt Item Type Name -->
			<?php echo $this->configFormText('curatescape_alt_item_type_name', 'Alternate Name', __('Enter an alternate display name for the %1s Item Type. You may also need to adjust the corresponding link label in <a href="%2s" target="_blank">Navigation settings</a>.', _CURATESCAPE_ITEM_TYPE_NAME_, admin_url('appearance/edit-navigation')), 'Example: Story');?>
			<!-- Alt Item Type Name (Plural) -->
			<?php echo $this->configFormText('curatescape_alt_item_type_name_p', 'Alternate Name Plural', __('Enter the plural form of your alternate display name for the %s Item Type. You may also need to adjust the corresponding link label in <a href="%2s" target="_blank">Navigation settings</a>.', _CURATESCAPE_ITEM_TYPE_NAME_, admin_url('appearance/edit-navigation')), 'Example: Stories');?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Item Record Settings'); ?></legend>
			<p><?php echo __('Use the following options to customize the functionality, layout, and content of Item records.');?></p>

			<!-- Recommended Template -->
			<?php echo $this->configFormCheckBox('curatescape_template', 'Metadata Template', __("If checked, the Item metadata display will be modified to use an enhanced layout for Items using the %s Item Type. Other Item Types will fall back to the Omeka default template.", _CURATESCAPE_ITEM_TYPE_NAME_) );?>
			<!-- Link Select Elements -->
			<?php echo $this->configFormCheckBox('curatescape_metadata_browse', 'Link Select Elements', "If checked, the Creator and Subject metadata elements will automatically be converted into hyperlinks that point to related Items. Note that this option will be ignored if the Search By Metadata plugin is installed and activated." );?>
			<!-- Auto Subtitle -->
			<?php echo $this->configFormCheckBox('curatescape_auto_subtitle', 'Append Subtitle', 'If checked, the Subtitle will be automatically appended to the Title anywhere it is displayed, including the record title, &lt;meta&gt; title, and more.');?>
			<!-- Subtitle Styles -->
			<?php echo $this->configFormCheckBox('curatescape_subtitle_styles', 'Subtitle Styles', 'If checked, the Subtitle will be formatted using plugin styles that change the color, typography, and layout. If unchecked, the Subtitle will be separated from the title by a colon. Requires use of auto-appending subtitle option, selected above. May not be supported in some themes.');?>
			<!-- Byline -->
			<?php echo $this->configFormSelect('curatescape_byline', 'Byline Location', 'Select the location where the formatted byline should be displayed.',
				array(
				'before_lede' => __('Before Lede (default)'),
				'after_lede' => __('After Lede'),
				'none' => __('None (do not display)')
				)
			);?>
			<!-- Inline Factoids -->
			<?php echo $this->configFormCheckBox('curatescape_inline_factoids', 'Inline Factoids', 'If checked, Factoids will be displayed within the main text. If the main text is more than five paragraphs in length, Factoids will be displayed after the third paragraph. Factoids are always wrapped in an &lt;aside&gt; tag for optimal accessibility.');?>
			<!-- Factoid Label -->
			<?php echo $this->configFormText('curatescape_factoids_label', 'Factoids Label', 'Enter a custom label to replace the default heading for Factoids.', 'Example: Did you know?');?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Tour Settings'); ?></legend>
			<p><?php echo __('Items using the %s Item Type (along with Geolocation data) can be collected into walking, cycling, and driving tours. Use the following options to customize the display of Tours.', _CURATESCAPE_ITEM_TYPE_NAME_);?></p>
			
			<!-- Alt Tour Name -->
			<?php echo $this->configFormText('curatescape_alt_tour_name', 'Alternate Name', __('Enter an alternate display name for Tours. You may also need to adjust the corresponding link label in <a href="%s" target="_blank">Navigation settings</a>.', admin_url('appearance/edit-navigation')), 'Example: Tour');?>
			<!-- Alt Tour Name (Plural) -->
			<?php echo $this->configFormText('curatescape_alt_tour_name_p', 'Alternate Name Plural', __('Enter the plural form of your alternate display name for Tours. You may also need to adjust the corresponding link label in <a href="%s" target="_blank">Navigation settings</a>.', admin_url('appearance/edit-navigation')), 'Example: Tours');?>
			<!-- Thumb Style-->
			<?php echo $this->configFormSelect('curatescape_tour_thumb_style', 'Tour Thumbnail', 'Select the style of thumbnail image to use when browsing tours. The composite style combines the first image from the first four Items on the Tour to create a mini-collage.', 
				array(
				'composite' => __('Composite (default)'),
				'first-image' => __('First image from first tour stop'),
				'none' => __('None')
				) 
			);?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Map Settings'); ?></legend>
			<p><?php echo __('Use the following options to customize %s maps. Additional Map configurations can be found in <a target="_blank" href="%2s">Geolocation plugin settings</a>. To display %3s maps use the shortcode: %4s. Refer to the %5s for full list of shortcode options.', _PLUGIN_NAME_, '/admin/plugins/config?name=Geolocation', _PLUGIN_NAME_, '<code>[curatescape_map]</code>', '<a href="https://omeka.org/classic/plugins/'._PLUGIN_NAME_.'" target="_blank">plugin documentation</a>');?></p>
			
			<!-- Mirror Geolocation -->
			<?php echo $this->configFormCheckBox('curatescape_map_mirror_geolocation', 'Mirror Geolocation', __('If checked, use the maps provided by the Geolocation plugin. Uncheck and configure custom options if you are using a Curatescape theme or if you have developed a Curatescape-optimized custom theme. Additional information available in <a target="_blank" href="%s"> plugin documentation</a>.', 'https://omeka.org/classic/plugins/'._PLUGIN_NAME_));?>
			<span class="map-settings">
				<?php $mapLayers = array(
					'CARTO_VOYAGER'=>__('CartoDB | Voyager (default)'),
					'CARTO_DARKMATTER'=>__('CartoDB | Dark Matter'),
					'CARTO_POSITRON'=>__('CartoDB | Positron'),
					'OFM_LIBERTY'=>__('Open Free Map | Liberty'),
					'STADIA_OSMBRIGHT'=>__('Stadia | OSM Bright (account required)'),
					'STADIA_OUTDOORS'=>__('Stadia | Outdoors (account required)'),
					'STADIA_STAMENTONER'=>__('Stadia | Stamen Toner (account required)'),
					'STADIA_STAMENTERRAIN'=>__('Stadia | Stamen Terrain (account required)'),
					'STADIA_ALIDADESMOOTH'=>__('Stadia | Alidade Smooth (account required)'),
					'STADIA_ALIDADESATELLITE'=>__('Stadia | Alidade Smooth Lite (account required)'),
					'STADIA_ALIDADESMOOTHDARK'=>__('Stadia | Alidade Smooth Dark (account required)'),
					'CUSTOM_URL'=>__('Custom URL'),
				);?>
				<!-- Map Primary -->
				<?php echo $this->configFormSelect('curatescape_map_primary_layer', 'Primary Map Style', 'Select the primary map style. ', 
					$mapLayers
				);?>
				<!-- Map Secondary -->
				<?php echo $this->configFormSelect('curatescape_map_secondary_layer', 'Secondary Map Style', 'Select the secondary map style. ',
					array_merge(array(''=>__('None (default)')), $mapLayers)
				);?>
				<span class="custom-settings">
					<!-- Custom URL -->
					<?php echo $this->configFormText('curatescape_map_custom_url', 'Custom URL', __('Enter the URL for a vector style source that conforms to the <a href="">MapLibre style specification</a>. If using custom settings for both primary and secondary layers, please enter two URLs, separated by a comma.', 'https://maplibre.org/maplibre-style-spec/'), 'Example: https://api.maptiler.com/maps/my-map/?key=xxxxxxx...');?>
				</span>
				<span class="stadia-settings">
					<!-- Stadia Key -->
					<?php echo $this->configFormText('curatescape_map_stadia_key', 'Stadia Authentication', __('Stadia Maps can be authenticated by registering this domain (%1s) in your <a target="_blank" href="%2s">Stadia account</a>. Alternately, you may enter your Stadia API key below. At least one method is required. Domain registration is the preferred method.', preg_replace('~^https?://~', '', WEB_ROOT), 'https://stadiamaps.com/'), 'Example: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx');?>
					<!-- Prefer EU -->
					<?php echo $this->configFormCheckBox('curatescape_map_prefer_eu', 'EU Preference', 'If checked, load supported map tile sets from servers in the European Union.');?>
				</span>
				<!-- Custom Label -->
				<?php echo $this->configFormText('curatescape_map_custom_label', 'Custom Map Label', __('Enter custom labels for your map layers, separated by a comma. These labels will be displayed in the interface for switching between primary and secondary map layers (if enabled). The number of labels should match the number of map layers.'), 'Example: Street, Satellite');?>
				<!-- Marker Color -->
				<?php echo $this->configFormText('curatescape_map_marker_color', 'Marker Color', __('Enter an HTML color code to use for map markers.'), 'Example: #222222');?>
				<!-- Featured Marker Color -->
				<?php echo $this->configFormText('curatescape_map_marker_featured_color', 'Featured Marker Color', __('Enter an HTML color code to use for featured item map markers.'), 'Example: #222222');?>
				<!-- Featured Marker Star Icon -->
				<?php echo $this->configFormCheckBox('curatescape_map_marker_featured_star', 'Featured Marker Icon', 'If checked, use a star icon inside featured item map markers.');?>
				<!-- Clusters -->
				<?php echo $this->configFormCheckBox('curatescape_map_clusters', 'Clusters', 'If checked, markers on the map will be grouped into clusters at higher zoom levels.');?>
				<span class="cluster-settings">
					<!-- Cluster Colors -->
					<?php echo $this->configFormText('curatescape_map_cluster_colors', 'Cluster Colors', __('Enter three valid CSS colors to customize the map clusters, each separated by a <code>|</code> pipe. The first color will be used for small clusters, the second for medium clusters, and the third for large clusters. Leave blank to use default colors.'), 'Example: teal | #008080 | rgb(0, 128, 128)');?>
				</span>
				<!-- Subjects Select -->
				<?php echo $this->configFormCheckBox('curatescape_map_subjects_select', 'Subjects Select', 'If checked, allow users to select from a dropdown of Subject terms to filter items on the global map. Note that this option is only recommended when all items have been given at least one Subject term. Use of the <a target="_blank" href="%s">Simple Vocab plugin</a> is strongly recommended.', 'https://omeka.org/classic/plugins/SimpleVocab/');?>
				<!-- Fixed Center-->
				<?php echo $this->configFormCheckBox('curatescape_map_fixed_center', 'Fixed Center', 'If checked, the initial center of your multi-marker maps will always adhere to the <a target="_blank" href="%s">Geolocation plugin settings</a> for default latitude, longitude, and zoom level. This option does not apply to tours, to single items, or to maps where the user has explicitly chosen a specific set of parameters (such as a subject term). This option may be useful if your map contains outliers beyond the expected bounds of your coverage area.', '/admin/plugins/config?name=Geolocation');?>
			</span>
		</fieldset>
		
		<fieldset>
			<legend><?php echo __('Navigation Settings'); ?></legend>
			<p><?php echo __('Use the following options to customize the primary and secondary (item) navigation menus, as well as the Omeka admin bar.');?></p>

			<!-- Admin Bar Edit-->
			<?php echo $this->configFormCheckBox('curatescape_admin_bar_edit', 'Admin Bar', 'If checked, an "Edit" link will be appended to the default Omeka admin bar for supported record types. Theme support varies. Note that this option will be selectively ignored if the Admin Tools plugin is active and configured to add its own edit link for a given record type.');?>
			<!-- Secondary Nav -->
			<?php echo $this->configFormCheckBox('curatescape_append_secondary_nav', 'Append Secondary', 'If checked, new navigation items for Featured and Curatescape Stories will be added to the secondary/Item navigation for the items/browse view.');?>
			<!-- Secondary Nav Shorten -->
			<?php echo $this->configFormCheckBox('curatescape_shorten_secondary_nav', 'Shorten Secondary', 'If checked, the secondary (item) navigation labels will be shortened to preserve space in some themes. For example, "Browse By Tags" will be shortened to "Tags."');?>
		</fieldset>

		<fieldset>
		<legend><?php echo __('Form Settings'); ?></legend>
		<p><?php echo __('Use the following options to configure the item form for maximal compatibility with %s.', _PLUGIN_NAME_);?></p>
			<!-- Filter Text-->
			<?php echo $this->configFormCheckBox('curatescape_filter_text', 'Filter Text', __('If checked, use additional text filters when saving Items. %s elements will be filtered to remove unsupported HTML. Invalid markup from desktop word processing software will be removed from all Item elements. Strongly recommended for mobile app projects and generally recommended for all.', _CURATESCAPE_ITEM_TYPE_NAME_));?>
			<!-- Format Warnings-->
			<?php echo $this->configFormCheckBox('curatescape_format_warnings', 'Format Warnings', 'If checked, display a warning after the user has saved an Item that appears to violate the recommended formatting rules (for example, by including a new line within a Subject term). This option does not result in the modification of any content. Strongly recommended for mobile app projects and generally recommended for all.');?>
			<!-- Form Enforcement -->
			<?php echo $this->configFormCheckBox('curatescape_form_enforcement', 'Form Enforcement', __('If checked, the "Use HTML" and "Add Input" buttons in the Item form will be disabled for select elements in order to enforce the recommended formatting rules. Labels will be appended to indicate %s-recommended elements.', _PLUGIN_NAME_) );?>
			<!-- Form Restrictions -->
			<?php echo $this->configFormCheckBox('curatescape_form_recommended_only', 'Form Restrictions', __('If checked, only %s-recommended elements and item types will be allowed. All other elements and item types will be hidden from view. Strongly recommended for all mobile app projects, as well as classroom-based projects and others that may involve novice users.', _PLUGIN_NAME_) );?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Output Settings'); ?></legend>
			<p><?php echo __('Use the following options to customize the %s JSON and RSS output formats.', _PLUGIN_NAME_);?></p>

			<?php echo $this->configFormSelect('curatescape_json_cache', 'JSON Browser Cache', __('When enabled, Curatescape JSON feeds (e.g. for <a href="%1s" target="_blank">items/browse</a>) will be cached in-browser for the selected duration. Browser cache rules are bypassed for logged in users and apply only to repeat loads within the selected duration.', items_output_url('mobile-json') ), 
				array(
				'0' => __('Disable cache'),
				'60' => __('1 minute'),
				'300' => __('5 minutes'),
				'600' => __('10 minutes (default)'),
				'1800' => __('30 minutes'),
				'3600' => __('1 hour'),
				'43200' => __('12 hours'),
				'86400' => __('24 hours') 
				) 
			);?>
			<!-- JSON Server Cache  -->
			<?php 
			$itemjson = WEB_ROOT.'/plugins/'._PLUGIN_NAME_.str_replace(_PLUGIN_DIR_, '', _JSON_ITEMS_FILE_);
			$tourjson = WEB_ROOT.'/plugins/'._PLUGIN_NAME_.str_replace(_PLUGIN_DIR_, '', _JSON_TOURS_FILE_);
			$cacheDebugLinks = '<div class="explanation"><a class="button" target="_blank" href="/items/browse?output=mobile-json&curatescape_cache_break=debug">'.__('Clear & Debug Items Cache').'</a><a class="button" target="_blank" href="/tours/browse?output=mobile-json&curatescape_cache_break=debug">'.__('Clear & Debug Tours Cache').'</a></div>';
			echo $this->configFormSelect('curatescape_json_storage', 'JSON Server Cache', __('When enabled, certain Curatescape JSON feeds will be cached and stored on the server for the selected duration. Increase the duration to improve performance and reduce memory usage for large datasets. Disable this option if you experience errors relating to your server security configurations. All server cache files are automatically cleared when saving an item. The server cache is bypassed for logged in users. %s', $cacheDebugLinks), 
				array(
				'0' => __('Disable cache'),
				'60' => __('1 minute'),
				'300' => __('5 minutes (default)'),
				'600' => __('10 minutes'),
				'1800' => __('30 minutes'),
				'3600' => __('1 hour'),
				'43200' => __('12 hours'),
				'86400' => __('24 hours') 
				) 
			);?>
			<!-- Enhanced RSS-->
			<?php echo $this->configFormCheckBox('curatescape_rss', 'Enhanced RSS', __('If checked, the default RSS feed will be replaced with an <a href="%s" target="_blank">enhanced format</a> that has been customized for narrative content.', items_output_url('rss-plus')));?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Dashboard Settings'); ?></legend>
			<p><?php echo __('Use the following options to add new panels to the <a href="/admin/" target="_blank">admin dashboard</a>.');?></p>
			
			<!-- Content Audit -->
			<?php echo $this->configFormCheckBox('curatescape_dashboard_audit', 'Content Audit', __('If checked, the admin dashboard will display the results of a content audit to ensure that authors are employing the recommended best practices for %s. Audit results are cached on the server side for up to 1 week or until an item record is added or modified. This option may temporarily slow down loading of the dashboard when the cache is being refreshed.', _CURATESCAPE_ITEM_TYPE_NAME_PLURAL_) );?>
			<!-- File Stats -->
			<?php echo $this->configFormCheckBox('curatescape_dashboard_stats', 'File Statistics', __('If checked, the admin dashboard will display file statistics and format recommendations. File statistics are cached on the server side for up to 1 week or until an item record is added or modified. This option may temporarily slow down loading of the dashboard when the cache is being refreshed.', _PLUGIN_NAME_) );?>
			<!-- Project Admin -->
			<?php echo $this->configFormCheckBox('curatescape_dashboard_project_mgmt', 'Project Management', __('If checked, the admin dashboard will display convenient links to manage configured app store and web analytics accounts. See App Store Settings below. Website analytics are derived from theme settings (option name: <code>google_analytics</code>) and supported third-party plugins (e.g. Matomo).') );?>
			<!-- Resources -->
			<?php echo $this->configFormCheckBox('curatescape_dashboard_resources', 'Resources', __('If checked, the admin dashboard will display a list of useful %s resources.', _PLUGIN_NAME_) );?>
			
		</fieldset>

		<fieldset>
			<legend><?php echo __('App Store Settings'); ?></legend>
			<p><?php echo __('Did you know that Curatescape is a not-for-profit, university-based project with a mission to build (surprisingly affordable) mobile apps for humanities organizations? Learn more at %1s. The following options apply to projects with corresponding mobile apps. To display app store buttons use the shortcode: %2s. Refer to the %3s for full list of shortcode options.', '<a href="https://curatescape.org" target="_blank">curatescape.org</a>', '<code>[curatescape_app_buttons icons="true"]</code>', '<a href="https://omeka.org/classic/plugins/'._PLUGIN_NAME_.'" target="_blank">plugin documentation</a>');?></p>
		
			<!-- iOS-->
			<?php echo $this->configFormText('curatescape_app_ios', 'iOS App', __('Enter the numeric identifier for the iOS app, found in Apple App Store Connect account. When this option is enabled, an iOS Smart App Banner will be added to your site, and you will be able to use the %s shortcode to display iOS App Store links. Some themes may also use this option to systematically display iOS App Store links.', '<code>[curatescape_app_buttons]</code>'), 'Example: 0123456789');?>
			<!-- Android-->
			<?php echo $this->configFormText('curatescape_app_android', 'Android App', __('Enter the alphanumeric package name/identifier for the Android app, found in Google Play Console account. When this option is enabled, you will be able to use the %s shortcode to display Google Play app links. Some themes may also use this option to systematically display Google Play app links.', '<code>[curatescape_app_buttons]</code>'), 'Example: com.developer.appname');?>
			<!-- Smart Banner -->
			<?php echo $this->configFormCheckBox('curatescape_smart_banner', 'Safari App Banner', __('If checked, display a banner encouraging Safari users to download the configured iOS mobile app. (Android does not offer an equivalent functionality.)') );?>
		</fieldset>

		<fieldset>
			<legend><?php echo __('Advanced Settings'); ?></legend>
			<p><?php echo __('The following options may be especially useful for site administrators, designers, developers, and other advanced users. Come back here if you are troubleshooting a specific issue.');?></p>

			<!-- Plugin Styles -->
			<?php echo $this->configFormCheckBox('curatescape_plugin_styles', 'Plugin Styles', __('If checked, use CSS styles provided by the %1s plugin. Turning this off may be useful for theme developers, but should otherwise remain checked.', _PLUGIN_NAME_) );?>
			<!-- Theme Fixes -->
			<?php echo $this->configFormCheckBox('curatescape_theme_fixes', 'Theme Fixes', __('If checked, use theme-specific CSS styles provided by the %1s plugin. These styles apply to select themes and only affect elements related to the %2s plugin.', _PLUGIN_NAME_, _PLUGIN_NAME_));?>
			<!-- Omit Redundant -->
			<?php echo $this->configFormCheckBox('curatescape_omit_redundant_elements', 'Omit Redundant', __('If checked, the Title and Subtitle elements for the Item will be omitted from the metadata table since they already appear as part of the page heading. Likewise, the Coverage element will be omitted since it already appears as part of the map caption (which consists of the Street Address and Access Information texts). Theme authors may apply their own filter to omit additional terms.', _CURATESCAPE_ITEM_TYPE_NAME_) );?>
		</fieldset>
		
		<script type="text/javascript">
			function toggleMapSettings() {
				jQuery('.map-settings').toggle( jQuery('#curatescape_map_mirror_geolocation').prop('checked') == false );
			}
			function toggleCustomSettings(){
				jQuery('.custom-settings').toggle( 
					jQuery('#curatescape_map_primary_layer').val().startsWith('CUSTOM') ||
					jQuery('#curatescape_map_secondary_layer').val().startsWith('CUSTOM')
				);
			}
			function toggleStadiaSettings(){
				jQuery('.stadia-settings').toggle( 
					jQuery('#curatescape_map_primary_layer').val().startsWith('STADIA') ||
					jQuery('#curatescape_map_secondary_layer').val().startsWith('STADIA')	
				);
			}
			function toggleClusterSettings(){
				jQuery('.cluster-settings').toggle( jQuery('#curatescape_map_clusters').prop('checked') == true );
			}
			jQuery(document).ready(function () {
				toggleMapSettings();
				toggleCustomSettings();
				toggleStadiaSettings();
				toggleClusterSettings();
				jQuery('#curatescape_map_mirror_geolocation').on('change', toggleMapSettings);
				jQuery('#curatescape_map_primary_layer').on('change', toggleCustomSettings);
				jQuery('#curatescape_map_secondary_layer').on('change', toggleCustomSettings);
				jQuery('#curatescape_map_primary_layer').on('change', toggleStadiaSettings);
				jQuery('#curatescape_map_secondary_layer').on('change', toggleStadiaSettings);
				jQuery('#curatescape_map_clusters').on('change', toggleClusterSettings);
			});
		</script>
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