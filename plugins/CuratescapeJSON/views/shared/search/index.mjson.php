<?php

// Start with an empty array of item metadata
$multipleItemMetadata = array();
$searchRecordTypes = get_search_record_types();

// Just get the items that can be mapped...
foreach( loop('search_texts') as $searchText )
{
	$isLegacyItemSearch = $searchRecordTypes[ $searchText[ 'record_type' ] ] == 'Item';
	
	$id = $searchText->record_id;	
	
	if( $isLegacyItemSearch )
	{
		// If it doesn't have location data, we're not interested.
		$item = get_record_by_id( 'item', $id );
		$hasLocation = get_db()->getTable( 'Location' )->findLocationByItem( $item, true );
		if( $hasLocation )
		{
			$itemMetadata = $this->itemJsonifier( $item );
			array_push( $multipleItemMetadata, $itemMetadata );
		}
	}else{
		// Search everything
		$searchTextsMetadata = $this->searchJsonifier( $searchText );
		array_push( $multipleItemMetadata, $searchTextsMetadata );
	}

}

$metadata = array(
	'items'        => $multipleItemMetadata,
	'total_items'  => count( $multipleItemMetadata )
);

echo Zend_Json_Encoder::encode( $metadata );