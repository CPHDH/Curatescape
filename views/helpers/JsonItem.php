<?php
class Curatescape_View_Helper_JsonItem extends Zend_View_Helper_Abstract
{
	public function JsonItem()
	{
		return $this;
	}
	public function JsonItemsShow($item, $isExtended = false){
		if($locationData = getLocationData($item)){
			$location = keyLocationOnly($locationData);
			$itemMetadata = array(
				'id' => $item->id,
				'featured' => $item->featured,
				'modified' => $item->modified,
				'latitude' => $location['latitude'],
				'longitude' => $location['longitude'],
				'title' => trim(dc($item, 'Title', array('no_filter'=>true))),
				'subtitle' => trim(itm($item, 'Subtitle')),
				'fullsize' => preferredItemImageUrl($item),
				'address' => strip_tags(trim(itm($item, 'Street Address'))),
			);
			if($isExtended){
				$itemMetadata['all_locations'] = $this->additionalLocations($locationData);
				$itemMetadata['zoom'] = $location['zoom_level'];
				$itemMetadata['creator'] = $this->getCreators($item);
				$itemMetadata['description'] = trim(itm($item, 'Story'));
				$itemMetadata['sponsor'] = itm($item, 'Sponsor');
				$itemMetadata['accessinfo'] = strip_tags(trim(itm($item, 'Access Information')));
				$itemMetadata['lede'] = trim(itm($item, 'Lede'));
				$itemMetadata['website'] = trim(itm($item, 'Official Website'));
				$itemMetadata['related_resources' ] = $this->getRelatedResources($item);
				$itemMetadata['factoids'] = $this->getFactoids($item);
				$itemMetadata['files'] = $this->getFiles($item);
			}
			return $itemMetadata;
		}
		return false;
	}

	public function JsonItemsBrowse($items, $allItemTypes = false){
		$output = array('items'=>array());
		foreach( $items as $item ){
			if(!$item->public) continue;
			if(!$allItemTypes && $item->item_type_id !== itemTypeID()) continue;
			if($itemMeta = $this->JsonItemsShow( $item, false )){
				$output['items'][] = $itemMeta;
			}
		}
		return json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}

	private function getFiles($item, $files = array()){
		foreach( $item->Files as $file ){
			$mimetype = metadata( $file, 'MIME Type' );
			$filedata = array(
				'id' => $file->id,
				'mime-type' => $mimetype,
				'title' => strip_formatting(metadata( $file, 'display_title')),
				'description' => $this->getFileCaption($file),
			);
			if( strpos( $mimetype, 'image/' ) === 0 )
			{
				$path = $file->getWebPath( 'fullsize' );
			}else{
				$path = $file->getWebPath( 'original' );
			}
			if( $file->hasThumbnail() )
			{
				$filedata[ 'thumbnail' ] = $file->getWebPath( 'square_thumbnail' );
			}
			$files[ $path ] = $filedata;
		}
		return $files;
	}

	private function getFileCaption($file)
	{
		return implode(" | ", array_filter(array(
			dc( $file, 'Description'),
			dc( $file, 'Source'),
			dc( $file, 'Date'),
			dc( $file, 'Creator'),
		)));
	}

	private function getFactoids($item, $output = array())
	{
		$texts = itm( $item, 'Factoid', array( 'all' => true ) );
		foreach($texts as $text){
			array_push($output, trim(html_entity_decode($text)));
		}
		return $output;
	}

	private function getRelatedResources($item, $output = array())
	{
		$texts = itm( $item, 'Related Resources', array( 'all' => true ) );
		foreach($texts as $text){
			array_push($output, trim(html_entity_decode($text)));
		}
		return $output;
	}

	private function getCreators($item, $output = array())
	{
		if($authors = dc($item, 'Creator', array( 'all' => true, 'no_filter' => true ) )){
			foreach($authors as $author){
				array_push($output, html_entity_decode(strip_formatting($author)));
			}
		}else{
			array_push($output, get_option('site_title'));
		}
		return $output;
	}

	public function refreshJsonCache($cache)
	{
		// called from HookAfterSaveItem or via Job Dispatcher
		if(intval(option('curatescape_json_storage'))){
			$items = get_records('Item', array('public' => true), 0);
			$json = $this->JsonItemsBrowse($items);
			if($json) {
				$cache->WriteCacheFile(_JSON_ITEMS_FILE_, $json, true);
			}
		}
		return $this;
	}
	private function additionalLocations($locationData, $values = array())
	{
		if(is_array($locationData)){
			foreach($locationData as $keyloc){
				if(is_array($keyloc)){ // v4
					foreach($locationData as $locs){
						foreach($locs as $loc){
							$values[] = array(
								'latitude' => $loc['latitude'],
								'longitude' => $loc['longitude'],
								'label' => $loc['label'],
							);
						}
					}
				} else { // v3
					$values[] = array(
						'latitude' => $locationData['latitude'],
						'longitude' => $locationData['longitude'],
						'label' => null,
					);
				}
				break;
			}
		}
		return $values;
	}
}