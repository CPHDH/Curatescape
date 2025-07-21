<?php
class Curatescape_View_Helper_FilterPublicNavigationMain extends Zend_View_Helper_Abstract{
	public function FilterPublicNavigationMain($nav){
		// Tours
		$toursLabel = tourLabelString(true);
		$nav[_PLUGIN_NAME_.' '.$toursLabel] = array(
			'label'=>__($toursLabel),
			'uri'=>url('tours/browse'),
		);
		// Stories
		$storiesLabel = storyLabelString(true);
		$nav[_PLUGIN_NAME_.' '.$storiesLabel] = array(
			'label'=>__($storiesLabel),
			'uri'=>storiesItemTypeBrowseURL(),
			'visible'=>false,
		);
		return $nav;
	}
}