<?php
class Curatescape_View_Helper_FilterAdminNavigationMain extends Zend_View_Helper_Abstract{
	public function FilterAdminNavigationMain($nav){	
		$nav['CuratescapeTours'] = array( 
			'label' => __('Tours'),
			'action' => 'browse',
			'controller' => 'tours',
		);
		return $nav;
	}
}