<?php
class Curatescape_Job_RefreshDashboardCache extends Omeka_Job_AbstractJob
{
	public function perform()
	{
		$view = get_view();
		$cache = $view->CuratescapeCache();
		$view->HookAdminDashboard()->refreshDashboardWidgets($cache);
	}
}
