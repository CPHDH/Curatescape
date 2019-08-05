<?php
if(metadata('simple_pages_page', 'is_published')){
	$pageData = array(
		'title'  => metadata('simple_pages_page', 'title'),
		'updated' => metadata('simple_pages_page', 'updated'),
		'text'  => metadata('simple_pages_page', 'text', array('no_escape' => true)),
	);
	echo Zend_Json_Encoder::encode( $pageData );
}
?>