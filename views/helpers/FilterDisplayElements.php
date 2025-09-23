<?php
class Curatescape_View_Helper_FilterDisplayElements extends Zend_View_Helper_Abstract{
	public function FilterDisplayElements($elementSets){
		if(
			is_admin_theme() || 
			!option('curatescape_omit_redundant_elements') || 
			!option('curatescape_template')
		) return $elementSets;
		if(!isCuratescapeStory(get_view()->getCurrentRecord('item', false))) return $elementSets;
		$redundantDC = array('Title','Coverage');
		$redundantCSIT = array('Subtitle');
		foreach($elementSets as $set => $elements) {
			if ($set == 'Dublin Core') {
				foreach ($elements as $key => $element) {
					if (in_array($element->name, $redundantDC)) {
						unset($elementSets['Dublin Core'][$key]);
					}
				}
			}
			if ($set == _CURATESCAPE_ITEM_TYPE_SETNAME_) {
				foreach ($elements as $key => $element) {
					if (in_array($element->name, $redundantCSIT)) {
						unset($elementSets[_CURATESCAPE_ITEM_TYPE_SETNAME_][$key]);
					}
				}
			}
		}
		return $elementSets;
	}
}