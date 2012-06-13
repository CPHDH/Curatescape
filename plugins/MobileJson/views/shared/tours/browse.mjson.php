<?php

// Start with an empty array of tours
$all_tours_metadata = array();

// Loop through all the tours
while( loop_tours() ) {
   $tour = get_current_tour();

   $tour_metadata = array( 
      'id'     => tour( 'id' ),
      'title'  => tour( 'title' ),
   );

   array_push( $all_tours_metadata, $tour_metadata );
}

$metadata = array(
   'tours'  => $all_tours_metadata,
);

// Encode and send
echo Zend_Json_Encoder::encode( $metadata );
