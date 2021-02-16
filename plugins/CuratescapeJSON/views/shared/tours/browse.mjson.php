<?php

echo '{"tours":[';

$tourCount = 0;

// Loop through all the tours
foreach( $tours as $tour )
{
	if( $tourCount > 0 )
	{
		echo ',';
	}

	set_current_record( 'tour', $tour );
	$tourMetadata = $this->tourJsonifier( $tour );
	echo json_encode( $tourMetadata );

	$tourCount += 1;
}

echo ']}';