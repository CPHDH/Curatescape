<?php
// Get the basic data
$itemMetadata = $this->itemJsonifier( $item , true);
echo json_encode( $itemMetadata );