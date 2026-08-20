<?php
class Curatescape_Job_RefreshJsonCache extends Omeka_Job_AbstractJob
{
	public function perform()
	{
		// In CLI/background context, bootstrap.php derives WEB_FILES from
		// $_SERVER['SCRIPT_NAME'] (the path to background.php) rather than the
		// actual site URL, producing bad values like http:///var/www/html/...
		// HookAfterSaveItem saves WEB_ROOT (correct in web context) before
		// dispatching this job so we can fix the storage adapter here.
		try {
			$webRoot = rtrim(get_option('curatescape_web_root'), '/');
			if ($webRoot) {
				$storage = Zend_Registry::get('storage');
				$adapter = $storage->getAdapter();
				if (method_exists($adapter, 'setWebDir')) {
					$adapter->setWebDir($webRoot . '/files');
				}
			}
		} catch (Exception $e) {}

		$view = get_view();
		$cache = $view->CuratescapeCache();
		$view->JsonItem()->refreshJsonCache($cache);
		$view->JsonTour()->refreshJsonCache($cache);
	}
}
