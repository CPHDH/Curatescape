<?php
echo '{"tours":[';
$index = 0;
if( count($tours) && function_exists('tb_sortByOrdinal') ){
	// via TourBuilder plugin
	usort( $tours, 'tb_sortByOrdinal' );
}
// Loop through all the tours
foreach( $tours as $tour ){
	if( $index > 0 )
	{
		echo ',';
	}
	set_current_record( 'tour', $tour );
	$tourMetadata = $this->tourJsonifier( $tour );
	echo json_encode( $tourMetadata );
	$index ++;
}
echo ']}';