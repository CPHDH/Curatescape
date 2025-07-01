<?php
class Curatescape_View_Helper_FilterPublicNavigationMain extends Zend_View_Helper_Abstract{
	public function FilterPublicNavigationMain($nav){
		// Tours (Required)
		$toursLabel = tourLabelString(true);
		$nav[_PLUGIN_NAME_.' '.$toursLabel] = array(
			'label'=>__($toursLabel),
			'uri'=>url('tours/browse'),
		);
		// Stories and Home (Optional)
		if(option('curatescape_append_primary_nav')){
			$storiesLabel = storyLabelString(true);
			$nav[_PLUGIN_NAME_.' '.$storiesLabel] = array(
				'label'=>__($storiesLabel),
				'uri'=>storiesItemTypeBrowseURL(),
				'visible'=>false,
			);
			$nav[_PLUGIN_NAME_.' Home'] = array(
				'label'=>__('Home'),
				'uri'=>url('/'),
			);
		}
		return $nav;
	}
}