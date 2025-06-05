<?php
// settings for: add home and stories menu options
class Curatescape_View_Helper_FilterPublicNavigationMain extends Zend_View_Helper_Abstract{
	public function FilterPublicNavigationMain($nav){
		if(!option('curatescape_append_primary_nav')) return $nav;
		$storiesLabel = _CURATESCAPE_ITEM_TYPE_NAME_PLURAL_;
		if(
			$this->altNameIsValid(option('curatescape_alt_item_type_name_p'))
		){
			$storiesLabel = trim(strip_tags(option('curatescape_alt_item_type_name_p')));
		}
		$nav[_PLUGIN_NAME_.' '.$storiesLabel] = array('label'=>__($storiesLabel), 'uri'=>storiesURL(), 'visible'=>false);
		$nav[_PLUGIN_NAME_.' Home'] = array('label'=>__('Home'), 'uri'=>url('/'));
		return $nav;
	}

	private function altNameIsValid($text){
		return (strlen(trim(strip_tags($text))) > 3);
	}
}