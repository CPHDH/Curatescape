<?php
// @todo: https://github.com/omeka/plugin-Geolocation/issues/60
class Curatescape_View_Helper_FilterGeolocationMapBrowseUI extends Zend_View_Helper_Abstract{
	public function FilterGeolocationMapBrowseUI($html){
		if(is_admin_theme()) return $html;
		//var_dump($args['options']);
		return 'filtered';
	}
}