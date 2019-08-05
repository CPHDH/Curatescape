<?php
$tourMetadata = $this->tourJsonifier( $tour );
echo Zend_Json_Encoder::encode( $tourMetadata );