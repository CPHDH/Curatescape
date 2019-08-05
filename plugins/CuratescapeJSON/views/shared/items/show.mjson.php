<?php
// Get the basic data
$itemMetadata = $this->itemJsonifier( $item , true);
echo Zend_Json_Encoder::encode( $itemMetadata );