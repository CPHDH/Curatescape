<?php

// Get the basic data
$itemMetadata = array(
   'id'           => item( 'id'),
   'subject'      => item( 'Dublin Core', 'Subject'),
   'description'  => item( 'Dublin Core', 'Description'),
   'creator'      => item( 'Dublin Core', 'Creator'),
   'source'       => item( 'Dublin Core', 'Source'),
   'publisher'    => item( 'Dublin Core', 'Publisher'),
   'date'         => item( 'Dublin Core', 'Date'),
);

$itemMetadata['title'] = html_entity_decode(
   strip_formatting( item( 'Dublin Core', 'Title' ) ) );

//
// FILES
//
if( item_has_files() ) {
   $files = array();
   while( loop_files_for_item( $item ) ) {
      $file = get_current_file();
      $path = $file->getWebPath( 'archive' );

      $mimetype = $this->fileMetadata( $file, 'mime type' );
      $filedata = array(
         'id'        => $this->fileMetadata( $file, 'id' ),
         'mime-type' => $mimetype,
         'size'      => $this->fileMetadata( $file, 'size' ),
      );

      $title = $this->fileMetadata( $file, 'Dublin Core', 'Title' );
      if( $title ) {
         $filedata['title'] = strip_formatting( $title );
      }

      if( strpos( $mimetype, 'image' ) === 0 ) {
         list( $width, $height ) = getimagesize( $file->getPath( 'archive' ) );
         $filedata[ 'width' ] = $width;
         $filedata[ 'height' ] = $height;
      }

      $description = $this->fileMetadata( $file, 'Dublin Core', 'Description' );
      if( $description ) {
         $filedata['description'] = $description;
      }

      if( $file->hasThumbnail() ) {
         $filedata['thumbnail'] = $file->getWebPath( 'square_thumbnail' );
      }

      $files[ $path ] = $filedata;
   }
   $itemMetadata['files'] = $files;
}

//
// LOCATION
//
// Get the location of the object (if Geolocation is an enabled plugin)
//
$location = get_db()->getTable(
   'Location' )->findLocationByItem( $item, true );
if( $location ) {
   $itemLatitude = $location['latitude'];
   $itemLongitude = $location['longitude'];

   $itemMetadata = array_merge( $itemMetadata,
      array(
         'latitude' => $itemLatitude,
         'longitude' => $itemLongitude,
      )
   );

   /* DISABLED: I don't know where this function comes from.
   if( $itemLatitude && $itemLongitude ) {
      $itemMetadata['distance_away_miles'] = geocode_measure_distance(
         $_GET['latitude'], $_GET['longitude'],
         $itemLatitude, $itemLongitude );
   }
    */
}

// I've heard that the Zend JSON encoder is really slow,
// if this becomes a problem, use the second line.
echo Zend_Json_Encoder::encode( $itemMetadata );
//echo json_encode( $itemMetadata );
