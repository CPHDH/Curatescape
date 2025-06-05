<?php
// @todo: $record->getItems(); see below
// subtitle omitted by default pending layout tests in apps
class Curatescape_View_Helper_JsonSearchRecord extends Zend_View_Helper_Abstract
{
	public function JsonSearchRecord( $searchText, $withSubtitle = false )
	{
		if(!$searchText) return false;
		$record = get_record_by_id($searchText->record_type, $searchText->record_id );
		if(!$record) return false;

		/* ITEM */
		if($searchText->record_type == 'Item'){
			if(
				get_db()->getTable( 'Location' )->findLocationByItem( $searchText->record_id, true ) &&
				isCuratescapeStory($record)
			){
				return array(
					'result_id' => $searchText->record_id,
					'result_type' => $searchText->record_type,
					'result_title' => $withSubtitle ? metadata($record, 'display_title') : $searchText->title,
					'result_thumbnail' => preferredItemImageUrl($record, 'square_thumbnail'),
				);
			}
		}

		/* FILE */
		if($searchText->record_type == 'File'){
			$parentRecord = get_record_by_id('Item', $record->item_id );
			if(
				$parentRecord && 
				get_db()->getTable( 'Location' )->findLocationByItem( $record->item_id, true ) &&
				isCuratescapeStory($parentRecord)
			){
				return array(
					'result_id' => $searchText->record_id,
					'result_type' => $searchText->record_type,
					'result_title' => $searchText->title,
					'result_thumbnail' => preferredFileImageUrl($record, 'square_thumbnail', '', true),
					'result_subtype' => fileTypeString($record),
					'result_parent_id' => $record->item_id,
					'result_parent_title' => metadata($parentRecord, array('Dublin Core', 'Title')),
				);	
			}
		}

		/* TOUR */
		if(
			$searchText->record_type == 'Tour'
		){
			return array(
				'result_id' => $searchText->record_id,
				'result_type' => $searchText->record_type,
				'result_title' => $searchText->title,
				'result_tour_items' => count($record->getItems()),
			);
		}

		return false;
	}
}