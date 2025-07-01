<?php
class Curatescape_View_Helper_HookPublicHead extends Zend_View_Helper_Abstract{
	public function HookPublicHead($args)
	{
		if(option('curatescape_meta_tags')){ 
			$this->metaTags($args);
		}
		if($ga = option('curatescape_google_analytics')){
			$this->gaTags($ga);
		}
		if(strlen(trim(strip_tags(option('curatescape_app_ios')))) > 7 ){ 
			$this->smartBanner(trim(strip_tags(option('curatescape_app_ios'))));
		}
		if(option('curatescape_plugin_styles')){ 
			queue_css_file('global', 'all', false, 'css', 
				get_plugin_ini(_PLUGIN_NAME_, 'version'));
			if(option('curatescape_theme_fixes')){ 
				queue_css_file('theme-fixes', 'all', false, 'css', 
					get_plugin_ini(_PLUGIN_NAME_, 'version'));
			}
		}
		if(is_current_url('/tours')){
			queue_css_file('tours', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
		}
		if(is_current_url('/tours/show')){
			if(option('curatescape_gallery_style_tour') == 'gallery-inline-captions'){
				queue_css_file('gallery-inline-captions', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
			}
			if(option('curatescape_gallery_style_tour') == 'gallery-grid'){
				queue_css_file('gallery-grid', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
			}
		}
		if(is_current_url('/items/show')){ 
			if(option('curatescape_gallery_style') == 'gallery-inline-captions'){
				queue_css_file('gallery-inline-captions', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
			}
			if(option('curatescape_gallery_style') == 'gallery-grid'){
				queue_css_file('gallery-grid', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
			}
			if(option('curatescape_gallery_style') == 'gallery-slides'){
				queue_css_file('gallery-slides', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
			}
		}
		queue_js_file('global', 'javascripts');
		if(
			is_current_url('/tours/show') && 
			option('curatescape_lightbox_tours') && 
			option('curatescape_gallery_style_tour') !== 'gallery-inline-captions'
		){
			$this->photoSwipeModule();
		}
		if(is_current_url('/items/show')){ 
			if(
				option('curatescape_lightbox') && 
				option('curatescape_gallery_style') !== 'gallery-slides'
			){
				$this->photoSwipeModule();
			}
			if(option('curatescape_gallery_style') == 'gallery-slides'){
				if(!get_theme_option('lightgallery_caption')){
					set_theme_option('lightgallery_caption', 'none'); // @todo: add title/description option
				}
				queue_lightgallery_assets();
				queue_js_file('lightgallery', 'javascripts', array('defer'=>'defer'));
			}
			queue_js_file('items-show', 'javascripts', array('defer'=>'defer'));
		}
		if(
			is_current_url('/items/browse') || 
			is_current_url('/tours/browse')
		){
			queue_js_file('secondary-nav-fix', 'javascripts', array('defer'=>'defer'));
		}
	}

	private function photoSwipeModule()
	{
	?>

	<!-- PhotoSwipe (Curatescape plugin) -->
	<link rel="stylesheet" href="<?php echo src('photoswipe.css', 'javascripts/PhotoSwipe/dist');?>">
	<script defer type="module" src="<?php echo src('photoswipe.js', 'javascripts');?>"></script>

	<?php
	}
	
	private function metaImage($url = ''){
		if(option('curatescape_meta_image') && strlen(option('curatescape_meta_image')) > 6){
			// string/url (plugin option)
			$url = trim(option('curatescape_meta_image'));
		}elseif(get_theme_option('curatescape_meta_image') && strlen(get_theme_option('curatescape_meta_image')) > 5){
			// theme upload (available for theme developers)
			$url = WEB_ROOT.'/files/theme_uploads/'.trim(get_theme_option('curatescape_meta_image')); 
		}elseif(get_theme_option('custom_meta_img') && strlen(get_theme_option('custom_meta_img')) > 5){
			// theme upload (legacy)
			$url = WEB_ROOT.'/files/theme_uploads/'.trim(get_theme_option('custom_meta_img')); 
		}
		if(!$url) return '';
		// validate/sanitize
		$url = html_escape(filter_var($url, FILTER_SANITIZE_URL));
		if(substr($url,0,4) !== "http" || !allowedExtensionImg($url)){
			return '';
		}
		if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === FALSE) {
			return '';
		}
		return $url;
	}

	private function metaTags($args)
	{
		$metaTitle= get_option('site_title');
		$metaText= get_option('description');
		$metaImg= $this->metaImage();
		$metaUrl = WEB_ROOT.current_url();
		$metaTitle = isset($args['view']->title) ? $args['view']->title : $metaTitle;
		if($item = $args['view']->getCurrentRecord('item', false)){
			$metaText = dc($item, 'Description') ? dc($item, 'Description') : $metaText;
			$metaImg = preferredItemImageUrl($item);
		}
		elseif($page = $args['view']->getCurrentRecord('simple_pages_page', false)){
			$metaText = $page->text ? $page->text : $metaText;
		}	
		elseif($tour = $args['view']->getCurrentRecord('tour', false)){
			$metaText = $tour->description ? $tour->description : $metaText;
			if($firstTourItem = $tour->getTourItemByIndex(0)){
				$metaImg = preferredItemImageUrl($firstTourItem);
			}
		}
		elseif($exhibit = $args['view']->getCurrentRecord('exhibit', false)){
			$metaText = $exhibit->description ? $exhibit->description : $metaText;
			$metaImg = record_image_url($exhibit, 'fullsize') ? record_image_url($exhibit, 'fullsize') : $metaImg;
		}
		elseif($collection = $args['view']->getCurrentRecord('collection', false)){
			$metaText = dc($collection, 'Description') ? dc($collection, 'Description') : $metaText;
			$metaImg = record_image_url($collection, 'fullsize') ? record_image_url($collection, 'fullsize') : $metaImg;
		}
		elseif(isset($args['view']->title) && str_starts_with($args['view']->title, 'Contribution')){
			$metaTitle = $metaTitle.(get_option('site_title') !== $metaTitle ? ' | '.get_option('site_title') : null);
		}
		elseif(isset($args['view']->title) && str_starts_with($args['view']->title, 'Browse Items on the Map')){
			$metaTitle = __('Map').(get_option('site_title') !== $metaTitle ? ' | '.get_option('site_title') : null);
		}
		if(empty($metaImg) || !empty($metaImg) && str_contains($metaImg, 'application/views/scripts/images/fallback')){
			$metaImg = $this->metaImage();
		}
	?>

	<!-- Meta Tags (Curatescape plugin) -->
	<meta name="title" content="<?php echo strip_formatting($metaTitle);?>" />
	<meta name="description" content="<?php echo snippet($metaText, 0, 500);?>" />
	<meta property="og:site_name" content="<?php echo option('site_title');?>">
	<meta property="og:type" content="website" />
	<meta property="og:url" content="<?php echo $metaUrl;?>" />
	<meta property="og:title" content="<?php echo strip_formatting($metaTitle);?>" />
	<meta property="og:description" content="<?php echo snippet($metaText, 0, 500);?>" />
	<meta property="og:image" content="<?php echo $metaImg;?>" />
	<meta property="twitter:card" content="summary_large_image" />
	<meta property="twitter:url" content="<?php echo $metaUrl;?>" />
	<meta property="twitter:title" content="<?php echo strip_formatting($metaTitle);?>" />
	<meta property="twitter:description" content="<?php echo snippet($metaText, 0, 500);?>" />
	<meta property="twitter:image" content="<?php echo $metaImg;?>" />

	<?php 
	}
	
	private function smartBanner($iosIdentifier=null)
	{
	if(!$iosIdentifier) return null;
	?>
	
	<!-- App Store Banner (Curatescape plugin) -->
	<meta name="apple-itunes-app" content="app-id=<?php echo $iosIdentifier;?>">
	
	<?php
	}
	
	private function gaTags($ga)
	{
	if(!$ga) return null;
	if(get_theme_option('google_analytics') == $ga) return null;
	?>

	<!-- Google Analytics (Curatescape plugin) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $ga;?>"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', '<?php echo $ga;?>');
	</script>

	<?php
	}
	
}