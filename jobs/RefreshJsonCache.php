<?php
class RefreshJsonCache extends Omeka_Job_AbstractJob
{
	public function perform()
	{
		$cache = new Curatescape_View_Helper_CuratescapeCache();
		$jsonItem = new Curatescape_View_Helper_JsonItem();
		$jsonTour = new Curatescape_View_Helper_JsonTour();
		$jsonItem->refreshJsonCache($cache);
		$jsonTour->refreshJsonCache($cache);
	}
}
