<?php
// @todo: add documentation for accepted args: class, addclass, icon(s)
class Curatescape_View_Helper_ShortcodeCuratescapeAppButtons extends Zend_View_Helper_Abstract{
	public function ShortcodeCuratescapeAppButtons($args, $view, $html = null){
		if(
			!option('curatescape_app_android') && 
			!option('curatescape_app_ios')
		){
			return null;
		}

		$useIcon = (isset($args['icons']) && $args['icons'] == 'true');
		$addClass = isset($args['addclass']) ? trim($args['addclass']) : null;
		$buttonClass = isset($args['buttonclass']) ? trim($args['buttonclass']) : 'curatescape-shortcode-button';
		$containerclass = isset($args['containerclass']) ? trim($args['containerclass']) : 'curatescape-shortcode-app-buttons';
		$platform = isset($args['platform']) ? strtolower(trim($args['platform'])) : null;

		if($identifier = html_escape(option('curatescape_app_ios'))){
			if($url = $this->appStoreValidURL($identifier)){
				if($platform !== 'android'){
					$icon = $useIcon ? '<span class="curatescape-icon">'.svg('logo-apple-appstore').'</span>' : null;
					$html .= '<a class="'.$buttonClass.'" aria-label="'.__('App Store').'" href="'.$url.'">'.$icon.'<span>'.__('App Store').'</span></a>';
				}
			}
		}
		if($packageName = html_escape(option('curatescape_app_android'))){
			if($url = $this->playStoreValidURL($packageName)){
				if($platform !== 'ios'){
					$icon = $useIcon ? '<span class="curatescape-icon">'.svg('logo-google-playstore').'</span>' : null;
					$html .= '<a class="'.$buttonClass.'" aria-label="'.__('Google Play').'" href="'.$url.'">'.$icon.'<span>'.__('Google Play').'</span></a>';
				}	
			}
		}
		return '<div class="curatescape-shortcode-app-buttons-container"><div class="'.$containerclass.'">'.$html.'</div></div>';
	}
	
	private function appStoreValidURL($string, $root='https://itunes.apple.com/us/app/id'){
		$string = str_replace($root, '', $string); // make sure it's not the full url already
		if(strpos($string, 'id') == 0){
			$string = str_replace('id', '', $string); // remove leading 'id'
		}
		return $string ? $root.$string : null;
	}

	private function playStoreValidURL($string, $root='http://play.google.com/store/apps/details?id='){
		$string = str_replace($root, '', $string); // make sure it's not the full url already
		if(count(explode('.', $string)) < 3){
			$string = null; // make sure it's a valid package name like com.example.app 
		}
		return $string ? $root.$string : null;
	}
}