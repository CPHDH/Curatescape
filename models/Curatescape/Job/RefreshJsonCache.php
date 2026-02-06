<?php
class Curatescape_Job_RefreshJsonCache extends Omeka_Job_AbstractJob
{
	public function perform()
	{
		$view = get_view();
		$cache = $view->CuratescapeCache();
		$view->JsonItem()->refreshJsonCache($cache);
		$view->JsonTour()->refreshJsonCache($cache);
	}
}
