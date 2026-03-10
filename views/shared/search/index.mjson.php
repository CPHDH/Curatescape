<?php
$searchResults = array();
foreach( loop('search_texts') as $searchText ){
	if($recordJson = get_view()->JsonSearchRecord( $searchText )){
		array_push( $searchResults, $recordJson );
	}
}
echo get_view()->CuratescapeCache()->Config(option('curatescape_json_cache'));
echo json_encode(array('items' => $searchResults, 'total_items' => count( $searchResults )), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);