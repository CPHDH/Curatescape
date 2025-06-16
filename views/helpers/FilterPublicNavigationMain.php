<?php
class Curatescape_View_Helper_FilterPublicNavigationMain extends Zend_View_Helper_Abstract{
	public function FilterPublicNavigationMain($nav){
		// Tours (Required)
		$toursLabel = __('Tours');
		if(
			$this->altNameIsValid(option('curatescape_alt_tour_name_p'))
		){
			$toursLabel = trim(strip_tags(option('curatescape_alt_tour_name_p')));
		}
		$nav[_PLUGIN_NAME_.' '.$toursLabel] = array(
			'label'=>__($toursLabel),
			'uri'=>url('tours/browse'),
		);
		// Stories and Home (Optional)
		if(option('curatescape_append_primary_nav')){
			$storiesLabel = _CURATESCAPE_ITEM_TYPE_NAME_PLURAL_;
			if(
				$this->altNameIsValid(option('curatescape_alt_item_type_name_p'))
			){
				$storiesLabel = trim(strip_tags(option('curatescape_alt_item_type_name_p')));
			}
			$nav[_PLUGIN_NAME_.' '.$storiesLabel] = array(
				'label'=>__($storiesLabel),
				'uri'=>storiesURL(),
				'visible'=>false,
			);
			$nav[_PLUGIN_NAME_.' Home'] = array(
				'label'=>__('Home'),
				'uri'=>url('/'),
			);
		}
		return $nav;
	}

	private function altNameIsValid($text){
		return (strlen(trim(strip_tags($text))) > 3);
	}
}