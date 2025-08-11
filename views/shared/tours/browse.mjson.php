<?php
$cache = get_view()->CuratescapeCache();
$cache->CacheBustManual(_JSON_TOURS_FILE_, false);
echo $cache->Config(option('curatescape_json_cache'));
if ($cacheFile = $cache->GetCacheFile(_JSON_TOURS_FILE_, option('curatescape_json_storage'))) {
	echo $cacheFile;
}else{
	$json = $this->getHelper('JsonTour')->JsonToursBrowse($tours);
	if(intval(option('curatescape_json_storage')) && !is_admin_theme()){
		$cache->WriteCacheFile(_JSON_TOURS_FILE_, $json);
	};
	echo $json;
}