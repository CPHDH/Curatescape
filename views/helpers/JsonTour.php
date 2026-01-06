<?php
class Curatescape_View_Helper_JsonTour extends Zend_View_Helper_Abstract
{
	public function JsonTour(){
		return $this;
	}

	public function JsonToursShow( $tour = null, $isExtended = false, $items = array() ){
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

	public function JsonToursBrowse($tours){
		if( count($tours) ){
			usort( $tours, 'sortByOrdinal' );
		}
		$output = array('tours'=>array());
		foreach( $tours as $tour ){
			if(!$tour->public) continue;
			if($tourMeta = $this->JsonToursShow( $tour )){
				$output['tours'][] = $tourMeta;
			}
		}
		return json_encode($output);
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
			if($isExtended){
				$custom = $tour->getTourItem($item->id);
				$itemMeta['custom']['subtitle'] = plainText($custom['subtitle']);
				$itemMeta['custom']['text'] = plainText($custom['text']);
			}
			return $itemMeta;
		}
		return null;
	}

	public function refreshJsonCache($cache)
	{
		// called from HookAfterSaveItem or via Job Dispatcher
		if(intval(option('curatescape_json_storage'))){
			$db = get_db();
			$table = $db->getTable('CuratescapeTour');
			$select = $table->getSelect();
			$select->where('public = ?', 1);
			$tours = $table->fetchObjects($select);
			$json = $this->JsonToursBrowse($tours);
			if($json) {
				$cache->WriteCacheFile(_JSON_TOURS_FILE_, $json, true);
			}
		}
		return $this;
	}
}