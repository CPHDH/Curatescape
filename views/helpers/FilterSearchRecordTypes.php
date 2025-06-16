<?php
class Curatescape_View_Helper_FilterSearchRecordTypes extends Zend_View_Helper_Abstract{
	public function FilterSearchRecordTypes($recordTypes){
		$recordTypes['CuratescapeTour'] = __('Tour');
		return $recordTypes;
	}
}