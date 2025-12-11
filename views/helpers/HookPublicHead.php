<?php
class Curatescape_View_Helper_HookPublicHead extends Zend_View_Helper_Abstract{
	public function HookPublicHead($args)
	{
		if(option('curatescape_app_ios') && option('curatescape_smart_banner')){ 
			$this->smartBanner(option('curatescape_app_ios'));
		}
		// CSS global
		if(option('curatescape_plugin_styles')){
			queue_css_file('curatescape-global', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
		}
		// CSS theme fixes
		if(option('curatescape_theme_fixes')){ 
			queue_css_file('curatescape-theme-fixes', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
		}
		// CSS tours
		if(is_current_url('/tours') && option('curatescape_plugin_styles')){
			queue_css_file('tours', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
		}
		// JS home
		if(is_current_url('/')){
			$this->jsonPreloadHome();
			queue_css_file('curatescape-tours', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
		}
		// JS tours/show @todo
		if(is_current_url('/tours/show')){
			queue_js_file('curatescape-tour', 'javascripts', array('defer'=>'defer'));
		}
		// JS items/show
		if(is_current_url('/items/show')){
			queue_js_file('curatescape-items-show', 'javascripts', array('defer'=>'defer'));
			if($params = getQueryParams()){
				if(isset($params['tour']) && isset($params['index'])){
					$this->curatescapeTourNavModule();
				}
			}
		}
		// JS items/browse and tours/browse
		if(is_current_url('/items/browse') || is_current_url('/tours/browse')){
			queue_js_file('curatescape-secondary-nav-fix', 'javascripts', array('defer'=>'defer'));
		}
		// Omit unused Geolocation scripts
		if(!get_option('curatescape_map_mirror_geolocation')) {
			$this->removeHeadAssets( $args['view'], array('/plugins/Geolocation') );
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
	
	private function removeHeadAssets($view=null, $paths=array())
	{
		if ($view) {
			$scripts = $view->headScript();
			foreach ($scripts as $key=>$file) {
				foreach ($paths as $path) {
					if(
						0 === strpos(current_url(), '/exhibits/show') && 
						$path == '/plugins/Geolocation'
					){
						// do nothing if this is an exhibit (allow map)
					}elseif(
						0 === strpos(current_url(), '/guest-user/') && 
						$path == '/plugins/GuestUser/views/public/javascripts'
					){
						// do nothing if this is a guest user page
					}elseif (
						isset($file->attributes['src']) && 
						strpos($file->attributes['src'], $path) !== false
					) {
						$scripts[$key]->type = null;
						$scripts[$key]->attributes['src'] = null;
						$scripts[$key]->attributes['source'] = null;
					}
				}
			}
			$styles = $view->headLink();
			foreach ($styles as $key=>$file) {
				foreach ($paths as $path) {
					if(
						0 === strpos(current_url(), '/exhibits/show') && 
						$path == '/plugins/Geolocation'
					){
						// do nothing if this is an exhibit (allow map)
					}elseif (
						$file->href && 
						strpos($file->href, $path) !== false
					) {
						$styles[$key]->href = null;
						$styles[$key]->type = null;
						$styles[$key]->rel = null;
						$styles[$key]->media = null;
						$styles[$key]->conditionalStylesheet = null;
					}
				}
			}
		}
	}
}