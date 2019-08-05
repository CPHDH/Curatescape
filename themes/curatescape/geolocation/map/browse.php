<?php 
// We don't need to use the Geolocation plugin on the front-end
$path=realpath(__DIR__ . '/../..')."/error/404.php";
include_once($path);
?>