<?php
class Curatescape_View_Helper_HookPublicItemsShow extends Zend_View_Helper_Abstract{
	public function HookPublicItemsShow($args){
		if(get_option('curatescape_map_mirror_geolocation')) return;
		// @todo: https://github.com/omeka/plugin-Geolocation/pull/64
		if(get_option('geolocation_item_map_enable') === '1') set_option('geolocation_item_map_enable', '0');
		return get_view()->CuratescapeMap()->Single();
	}
}