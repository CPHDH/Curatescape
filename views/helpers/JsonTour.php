<?php // @todo: see below: getTourItem()
class Curatescape_View_Helper_JsonTour extends Zend_View_Helper_Abstract
{
	public function JsonTour( $tour, $isExtended = false, $items = array() ){
		foreach( $tour->Items as $item ){
			if($item->public){
				set_current_record( 'Item', $item );
				if($tourItem = $this->tourItem($item, $tour, $isExtended)){
					array_push( $items, $tourItem );
				}
			}
		}
		return array(
			'id' => $tour->id,
			'ordinal' => isset($tour->ordinal) ? $tour->ordinal : 0,
			'featured' => isset($tour->featured) ? $tour->featured : 0,
			'title' => plainText($tour->title),
			'creator' => plainText($tour->credits),
			'description' => normalizeTextBlocks($tour->description),
			'postscript_text' => $tour->postscript_text,
			'tour_img' => isset($items[0]['fullsize']) ? $items[0]['fullsize'] : '', // @todo
			'items' => $items,
		);
	}

	private function tourItem($item, $tour, $isExtended = false){
		if(!$item || !$tour) return null;
		if($location = get_db()->getTable('Location')->findLocationByItem($item, true)){
			$itemMeta = array(
				'id' => $item->id,
				'title' => plainText( dc( $item, 'Title', array('no_filter'=>true) ) ),
				'latitude' => $location['latitude'],
				'longitude' => $location['longitude'],
				'thumbnail' => preferredItemImageUrl($item, 'square_thumbnail', ''),
				'fullsize' => preferredItemImageUrl($item, 'fullsize', ''),
				'subtitle' => plainText( itm($item, 'Subtitle') ),
				'address' => plainText( itm($item, 'Street Address') ),
			);
			if($isExtended && method_exists($tour,'getTourItem')){ // @todo: migrate from tour builder
				$custom = $tour->getTourItem($item->id);
				$itemMeta['custom']['subtitle'] = plainText($custom['subtitle']);
				$itemMeta['custom']['text'] = plainText($custom['text']);
			}
			return $itemMeta;
		}
		return null;
	}
}