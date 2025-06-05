<?php
class Curatescape_View_Helper_FilterAllElementTextsOptions extends Zend_View_Helper_Abstract{
	public function FilterAllElementTextsOptions($options, $args){
		if(!isset($args['record'])) return $options;
		if(
			!is_admin_theme() && 
			option('curatescape_template') && 
			isCuratescapeStory($args['record'])
		){ 
			$options['partial'] = 'common/item-metadata-partial.php';
		}
		return $options;
	}
}
