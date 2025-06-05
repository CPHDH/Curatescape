<?php
// no pagination for mobile-json responses
class Curatescape_View_Helper_FilterPerPage extends Zend_View_Helper_Abstract{
	public function FilterPerPage($perPage){
		if( isset($_GET["output"]) && $_GET["output"] == 'mobile-json'){
			$perPage = null;
		}
		return $perPage;
	}
}