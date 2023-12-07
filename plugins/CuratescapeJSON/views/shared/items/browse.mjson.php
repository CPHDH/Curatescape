<?php
// helper functions
function sortByTitleIndex($a, $b){
	return $b['title_index'] <=> $a['title_index']; // title id desc
}
function sortById($a, $b){
	return $a['id'] <=> $b['id']; // id asc
}
function filenameToPath($filename, $dir){
	return WEB_ROOT.'/files/'.$dir.'/'
	.preg_replace('/\\.[A-Za-z]{3,4}/', '', $filename)
	.'.jpg'; // build path and replace any other extension with .jpg
}
function normalizeText($text){
	return isset($text) ? trim(html_entity_decode(strip_formatting($text))) : '';
}
function finalpassJSON($processing, $postProcessed = array()){
	usort($processing, 'sortByTitleIndex'); // for processing dupes with alt titles
	foreach($processing as $a){
		unset($a['title_index'],$a['filename']); // remove raw data
		$postProcessed[$a['id']] = $a; // (overwrites dupes based on sort)
	}
	usort($postProcessed, 'sortById'); // re-sort new to old
	echo json_encode(array(
		'items'=> $postProcessed,
		'total_items'=> count( $postProcessed ))
	);
}
function getLoopItemIds(){
	$itemIdArray = array();
	foreach( loop('items') as $item ){
		if($item->public){
			$itemIdArray[] = $item->id;
		}
	}
	return implode(',', $itemIdArray);
}
// Querying DB is way faster than using itemJsonifier for large sets, but results need processing
$db=get_db();
$prefix=$db->prefix;
$sql='SELECT
i.id,
i.featured,
i.modified,
l.latitude,
l.longitude,
et1.id title_index,
et1.text title,
et2.text address,
f.filename
FROM '.$prefix.'items AS i
JOIN '.$prefix.'locations AS l
	ON i.id = l.item_id
JOIN ('.$prefix.'element_texts AS et1, '.$prefix.'elements AS e1)
	ON (i.id = et1.record_id
	AND et1.record_type = "Item"
	AND et1.element_id = e1.id
	AND e1.name="Title"
	)
LEFT JOIN ('.$prefix.'element_texts AS et2, '.$prefix.'elements AS e2)
	ON (i.id = et2.record_id
	AND et2.record_type = "Item"
	AND et2.element_id = e2.id
	AND e2.name="Street Address"
	)
LEFT JOIN ('.$prefix.'files AS f)
	ON (i.id = f.item_id
	AND f.has_derivative_image = 1
	AND f.order = 1
	)
WHERE i.id IN ('.getLoopItemIds().')
ORDER BY i.id DESC;
';
$processing = array(); // build initial array from sql results
try {
	$result_array = $db->fetchAll($sql);
	foreach( $result_array as $record ){
		if (!isset($record['latitude']) || !isset($record['longitude'])) {
			continue; // skip items without location data
		}
		$record['title'] = normalizeText($record['title']);
		$record['address'] = normalizeText($record['address']);
		if (! is_null($record['filename'])) {
			$record['thumbnail'] = filenameToPath($record['filename'],'square_thumbnails');
			$record['fullsize'] = filenameToPath($record['filename'],'fullsize');
		}else{
			// If sql query didn't find an official "first file" use Omeka API
			// (cannot use record_image_url(), which may return fallback image for audio, etc.)
			$r=get_record_by_id('item', $record['id']);
			if($r->hasThumbnail()){
				foreach($r->getFiles() as $f){
					if( $f['has_derivative_image']==1 ){
						$record['thumbnail'] = filenameToPath($f['filename'],'square_thumbnails');
						$record['fullsize'] = filenameToPath($f['filename'],'fullsize');
						break 1; // exit on first match
					}
				}
			}else{
				$record['thumbnail']='';
				$record['fullsize']='';
			}
		}
		array_push($processing, $record);
	}
	return finalpassJSON($processing);
} catch (Exception $e) {
	// client will handle empty results...
}