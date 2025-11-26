<?php
class Curatescape_View_Helper_HookPublicHead extends Zend_View_Helper_Abstract{
	public function HookPublicHead($args)
	{
		if(option('curatescape_app_ios') && option('curatescape_smart_banner')){ 
			$this->smartBanner(option('curatescape_app_ios'));
		}
		// CSS global
		if(option('curatescape_plugin_styles')){
			queue_css_file('global', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
		}
		// CSS theme fixes
		if(option('curatescape_theme_fixes')){ 
			queue_css_file('theme-fixes', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
		}
		// CSS tours
		if(is_current_url('/tours') && option('curatescape_plugin_styles')){
			queue_css_file('tours', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
		}
		// CSS tours/show @todo
		if(is_current_url('/tours/show') && option('curatescape_plugin_styles')){
			// if(option('curatescape_gallery_style_tour') == 'gallery-inline-captions'){
			// 	queue_css_file('gallery-inline-captions', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
			// }
			// if(option('curatescape_gallery_style_tour') == 'gallery-grid'){
			// 	queue_css_file('gallery-grid', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
			// }
		}
		// JS home
		if(is_current_url('/')){
			$this->jsonPreloadHome();
		}
		// JS tours/show
		if(is_current_url('/tours/show')){
			queue_js_file('tour', 'javascripts', array('defer'=>'defer'));
			if(option('curatescape_gallery_style_tour') !== 'gallery-inline-captions'){
				$this->photoSwipeModule();
			}
		}
		// JS items/show
		if(is_current_url('/items/show')){
			queue_js_file('items-show', 'javascripts', array('defer'=>'defer'));
			if($params = getQueryParams()){
				if(isset($params['tour']) && isset($params['index'])){
					$this->curatescapeTourNavModule();
				}
			}
		}
		// JS items/browse and tours/browse
		if(is_current_url('/items/browse') || is_current_url('/tours/browse')){
			queue_js_file('secondary-nav-fix', 'javascripts', array('defer'=>'defer'));
		}
		// Omit unused Geolocation scripts
		if(!get_option('curatescape_map_mirror_geolocation')) {
			curatescapeRemoveHeadAssets( $args['view'], array('/plugins/Geolocation') );
		}
	}
	
	private function jsonPreloadHome()
	{
	if(option('curatescape_home_map') === 'none') return null;
	?>
	<link rel="preload" href="<?php echo WEB_ROOT.'/items/browse?output=mobile-json';?>"  as="fetch"/>
	<?php
	}

	private function curatescapeTourNavModule()
	{
	?>
	<!-- CuratescapeTourNav (Curatescape plugin) -->
	<script type="module" src="<?php echo src('curatescape-tour-nav.js', 'javascripts');?>"></script>
	<?php
	}
	
	private function smartBanner($iosIdentifier=null)
	{
	if(!$iosIdentifier) return null;
	$iosIdentifier = trim(strip_tags($iosIdentifier));
	if(strlen($iosIdentifier) <= 7) return null;
	?>
	<!-- App Store Banner (Curatescape plugin) -->
	<meta name="apple-itunes-app" content="app-id=<?php echo $iosIdentifier;?>">
	<?php
	}
}