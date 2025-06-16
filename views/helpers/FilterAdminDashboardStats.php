<?php
class Curatescape_View_Helper_FilterAdminDashboardStats extends Zend_View_Helper_Abstract{
	public function FilterAdminDashboardStats($stats){
		if(is_allowed('Curatescape_CuratescapeTours', 'browse'))
		{
			$stats['tours'] = array(total_records('CuratescapeTours'), __('Tours'));
		}
		return $stats;
	}
}