<?php
class RefreshDashboardCache extends Omeka_Job_AbstractJob
{
	public function perform()
	{
		$cache = new Curatescape_View_Helper_CuratescapeCache();
		$dashboard = new Curatescape_View_Helper_HookAdminDashboard();
		$dashboard->refreshDashboardWidgets($cache);
	}
}
