<?php

// Add enumarations of the ordered items in this tour.
$items = array();
foreach( $tour->Items as $item ) {
$location = geolocation_get_location_for_item($item->id, true);
   $item_metadata = array(
      'id'     => $item->id,
      'title'  => $this->itemMetadata( $item, 'Dublin Core', 'Title' ),
      'latitude' => $location['latitude'],
      'longitude' => $location['longitude']
   );

   array_push( $items, $item_metadata );
}

// Create the array of data
$tour_metadata = array(
   'id'           => tour( 'id' ),
   'title'        => tour( 'title' ),
   'description'  => tour( 'description' ),
   'items'        => $items,
);

// Encode the array we've created.
echo Zend_Json_Encoder::encode( $tour_metadata );
