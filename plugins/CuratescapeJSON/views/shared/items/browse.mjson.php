<?php
// Start with an empty array of item metadata
$multipleItemMetadata = array();

// Loop through each item, picking up the minimum information needed.
// There will be no pagination, since the amount of information for each
// item will remain quite small.
$itemJsonifier=false; // keeping this as an options for a few specific use cases
if
($itemJsonifier)
{
	foreach
	( loop( 'item' ) as $item )
	{
		if
		($itemMetadata = $this->itemJsonifier( $item , false))
		{
			array_push( $multipleItemMetadata, $itemMetadata );
		}

	}
}else
{

	// Querying DB is way faster than using itemJsonifier for large sets but is currently kind of ugly!
	$itemIdArray = array();
	foreach
	( $items as $item )
	{
		if
		($item->public)
		{
			$itemIdArray[] = $item->id;
		}
	}
	$itemIdList=implode(',', $itemIdArray);

	$db=get_db();
	$prefix=$db->prefix;
	$sql = "
	SELECT i.id,
	       i.featured,
	       i.modified,
	       l.latitude,
	       l.longitude,
	       et1.id 'title_index',
	       et1.text 'title',
	       et2.text 'address',
	       f.filename
	FROM ".$prefix."items AS i
	JOIN ".$prefix."locations AS l
	  ON i.id = l.item_id
	JOIN (".$prefix."element_texts AS et1, ".$prefix."elements AS e1)
	  ON (i.id = et1.record_id
	      AND et1.record_type = 'Item'
	      AND et1.element_id = e1.id
	      AND e1.name='Title'
	      )
	LEFT JOIN (".$prefix."element_texts AS et2, ".$prefix."elements AS e2)
	  ON (i.id = et2.record_id
	      AND et2.record_type = 'Item'
	      AND et2.element_id = e2.id
	      AND e2.name='Street Address'
	      )
	LEFT JOIN (".$prefix."files AS f)
	  ON (i.id = f.item_id
	      AND f.has_derivative_image = 1
	      AND f.order = 1
	      )
	WHERE i.id IN ($itemIdList)
	ORDER BY i.id DESC;
	";
	// @TODO: accurately and consistently get the FIRST file w/ derivative image (first by by assigned order then by lowest id)
	// @TODO: make sure to only get the FIRST title (by lowest id)


	try {

		$result_array = $db->fetchAll($sql);

		foreach
		( $result_array as $record )
		{

			// Check Location
			if (!isset($record['latitude']) || !isset($record['longitude'])) {
				continue;
			}

			// Normalize title
			$record['title'] = trim(html_entity_decode(strip_formatting($record['title'])));

			// Normalize address
			$record['address'] = isset($record['address']) ? $record['address'] : '';

			// Process thumbnail URLs
			if (! is_null($record['filename'])) {
				// Replace any other extension with .jpg
				$filename = preg_replace('/\\.[A-Za-z]{3,4}/', '', $record['filename']) . ".jpg";
				$record['thumbnail'] = WEB_ROOT . "/files/square_thumbnails/$filename";
				$record['fullsize'] = WEB_ROOT . "/files/fullsize/$filename";
			}else{
				// If db query didn't find a "first file" use this slower method
				$r=get_record_by_id('item', $record['id']);
				if($r->hasThumbnail()){
					foreach($r->getFiles() as $f){
						if( $f['has_derivative_image']==1 ){
							$record['thumbnail'] = WEB_ROOT . "/files/square_thumbnails/" . preg_replace('/\\.[A-Za-z]{3,4}/', '', $f['filename']) . ".jpg" ;
							$record['fullsize'] = WEB_ROOT . "/files/fullsize/" . preg_replace('/\\.[A-Za-z]{3,4}/', '', $f['filename']) . ".jpg" ;
							break 1;
						}
					}
				}else{
					$record['thumbnail']='';
					$record['fullsize']='';
				}
			}
			// Remove unprocessed filename from JSON output
			unset($record['filename']);

			array_push($multipleItemMetadata, $record);
		}

		function sortByTitleIndex($a, $b)
		{
			return $a['title_index'] < $b['title_index'];
		}


		function sortById($a, $b)
		{
			return $a['id'] < $b['id'];
		}


		$postProcessed = array();
		usort($multipleItemMetadata, 'sortByTitleIndex');
		foreach($multipleItemMetadata as $a){
			unset($a['title_index']);
			$postProcessed[$a['id']] = $a;
		}
		usort($postProcessed, 'sortById');
		$multipleItemMetadata = $postProcessed;


	} catch (Exception $e) {
		// client will handle empty results...
	}

}
$metadata = array(
	'items'        => $multipleItemMetadata,
	'total_items'  => count( $multipleItemMetadata )
);

echo json_encode( $metadata );