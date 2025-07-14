<?php
class Curatescape_View_Helper_HookPublicContent extends Zend_View_Helper_Abstract{
	public function HookPublicContent($args)
	{
		return $this;
	}
	public function homeEnd($heading = null)
	{
		if(isset($heading)){
			$heading = '<h2>'.strip_tags(trim($heading)).'</h2>';
		}
		// @todo!
		echo '<div id="home-end">'.$heading.'</div>';
	}
	public function homeTop($heading = null)
	{
		if(!is_current_url('/')) return null;
		if(isset($heading)){
			$heading = '<h2>'.strip_tags(trim($heading)).'</h2>';
		}
		echo '<div id="home-top">'.$heading.$this->homeGeolocationMap().'</div>';
	}
	public function homeGeolocationMap($range = array(), $html = null)
	{
		$db = get_db();
		$itemTable = $db->getTable( 'Item' );
		$items = $itemTable->fetchObjects(
			<<<SQL
			SELECT i.* FROM {$db->prefix}items i
			WHERE i.public
			SQL
		);
		$range = array_filter(array_map(
			function($item) use ($range){
				if(hasLocation($item) && isCuratescapeStory($item)){
					return $item->id;
				}
				return null;
			}, $items));
		$height = option('geolocation_item_map_height') ? 'height='.option('geolocation_item_map_height') : null;
		$html .= '<figure class="home-items-map">';
			$html .=  get_view()->shortcodes('[geolocation range='.implode(',',$range).' '.$height.']');
			$html .= '<figcaption class="curatescape-map-caption" data-curatescape-invisible="true">';
				$html .=  __('%1s Map: %2s total', storyLabelString(), count($range));
			$html .= '</figcaption>';
		$html .= '</figure>';
		return $html;
	}
}