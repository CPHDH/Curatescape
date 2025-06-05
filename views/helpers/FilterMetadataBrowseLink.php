<?php
class Curatescape_View_Helper_FilterMetadataBrowseLink extends Zend_View_Helper_Abstract{
	public function FilterMetadataBrowseLink($text, $elementId){
		if(!$text) return null;
		if(!$elementId || !is_numeric($elementId) || !option('curatescape_metadata_browse')) return $text;
		$browseParams = array(
			'advanced' => array(
					array(
					'element_id' => $elementId,
					'type' => 'is exactly',
					'terms' => strip_tags($text),
				),
			),
		);
		return link_to_items_browse(strip_tags($text), $browseParams);
	}
}