<?php
$tourMetadata = $this->tourJsonifier( $tour, true );
echo json_encode( $tourMetadata );