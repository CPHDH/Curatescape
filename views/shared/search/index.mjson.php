<?php
$searchResults = array();
foreach( loop('search_texts') as $searchText ){
	if($recordJson = get_view()->JsonSearchRecord( $searchText )){
		array_push( $searchResults, $recordJson );
	}
}
echo cacheConfig(option('curatescape_json_cache'));
echo json_encode(array('items' => $searchResults, 'total_items' => count( $searchResults )));