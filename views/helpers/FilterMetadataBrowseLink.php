<?php
class Curatescape_View_Helper_FilterMetadataBrowseLink extends Zend_View_Helper_Abstract{
	public function FilterMetadataBrowseLink($text, $elementId){
		if(!$text) return null;
		if(
			!$elementId || 
			!is_numeric($elementId) || 
			!option('curatescape_metadata_browse') ||
			plugin_is_active('SearchByMetadata')
		) return $text;
		$safetext = htmlspecialchars_decode(strip_tags($text), ENT_QUOTES);
		$browseParams = array(
			'advanced' => array(
					array(
					'element_id' => $elementId,
					'type' => 'is exactly',
					'terms' => $safetext,
				),
			),
		);
		return link_to_items_browse($safetext, $browseParams);
	}
}