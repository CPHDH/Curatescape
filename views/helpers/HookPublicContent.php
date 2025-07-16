<?php
class Curatescape_View_Helper_HookPublicContent extends Zend_View_Helper_Abstract{
	public function HookPublicContent($args)
	{
		return $this;
	}
	public function homeEnd()
	{
		if(option('curatescape_home_map') == 'bottom'){
			echo $this->homeGeolocationMap('bottom');
		}
	}
	public function homeTop()
	{
		if(!is_current_url('/')) return null;

		if(option('curatescape_home_map') == 'top'){
			echo $this->homeGeolocationMap('top');
		}
	}
	public function homeGeolocationMap($class = null)
	{
		$html = null;
		$range = $this->commaSeparatedItemIds();
		if(!count($range)) return null;
		$height = option('geolocation_item_map_height') ? 'height='.option('geolocation_item_map_height') : null;
		$heading = option('curatescape_home_map_heading') ? plainText(option('curatescape_home_map_heading')) : null;
		if(isset($heading)){
			$heading = '<h2 class="curatescape-map-title">'.$heading.'</h2>';
		}
		$caption = option('curatescape_home_map_caption') ? allowLinks(option('curatescape_home_map_caption')) : __('Map containing %1s %2s', count($range), strtolower(storyLabelString('plural')));
		$html .= '<div class="curatescape-home-content '.$class.'">'.$heading;
			$html .= '<figure class="home-items-map">';
				$html .=  get_view()->shortcodes('[geolocation range='.implode(',',$range).' '.$height.']');
				$html .= '<figcaption class="curatescape-map-caption" data-curatescape-screenreader-only="'.(option('curatescape_home_map_caption') ? 'false' : 'true').'">';
					$html .= $caption;
				$html .= '</figcaption>';
			$html .= '</figure>';
		$html .= '</div>';
		return $html;
	}
	private function commaSeparatedItemIds()
	{
		$range = array();
		$db = get_db();
		$itemTable = $db->getTable( 'Item' );
		$items = $itemTable->fetchObjects(
			<<<SQL
			SELECT i.* FROM {$db->prefix}items i
			WHERE i.public
			SQL
		);
		return array_filter(array_map(
			function($item) use ($range){
				if(hasLocation($item) && isCuratescapeStory($item)){
					return $item->id;
				}
				return null;
			}, $items ) );
	}
}