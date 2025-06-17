<?php
class Curatescape_View_Helper_JsonItem extends Zend_View_Helper_Abstract
{
	public function JsonItem( $item, $isExtended = false )
	{
		if($location = get_db()->getTable( 'Location' )->findLocationByItem( $item, true )){
			$itemMetadata = array(
				'id' => $item->id,
				'featured' => $item->featured,
				'modified' => $item->modified,
				'latitude' => $location[ 'latitude' ],
				'longitude' => $location[ 'longitude' ],
				'title' => dc($item, 'Title', array('no_filter'=>true)),
				'subtitle' => itm($item, 'Subtitle'),
				'thumbnail' => preferredItemImageUrl($item, 'square_thumbnail'),
				'fullsize' => preferredItemImageUrl($item),
				'address' => itm($item, 'Street Address'),
			);

			if($isExtended){
				$itemMetadata['creator'] = $this->getCreators($item);
				$itemMetadata['description'] = itm($item, 'Story');
				$itemMetadata['sponsor'] = itm($item, 'Sponsor');
				$itemMetadata['accessinfo'] = itm($item, 'Access Information');
				$itemMetadata['lede'] = itm($item, 'Lede');
				$itemMetadata['website'] = itm($item, 'Official Website');
				$itemMetadata[ 'related_resources' ] = $this->getRelatedResources($item);
				$itemMetadata[ 'factoids' ] = $this->getFactoids($item);
				$itemMetadata[ 'files' ] = $this->getFiles($item);
			}
			return $itemMetadata;
		}
		return false;
	}
	
	public function JsonItemsBrowse($items){
		$output = array('items'=>array());
		foreach( $items as $item ){
			if(!$item->public) continue;
			if($itemMeta = $this->JsonItem( $item, false )){
				$output['items'][] = $itemMeta;
			}
		}
		return json_encode($output);
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
		if($authors = dc($item, 'Creator', array( 'all' => true ) )){
			foreach($authors as $author){
				array_push($output, html_entity_decode(strip_formatting($author)));
			}
		}else{
			array_push($output, get_option('site_title'));
		}
	}

}