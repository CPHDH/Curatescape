<?php
$tourMetadata = $this->tourJsonifier( $tour );
echo json_encode( $tourMetadata );