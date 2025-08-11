<?php
$cache = get_view()->CuratescapeCache();
$cache->CacheBustManual(_JSON_ITEMS_FILE_, false);
echo $cache->Config(option('curatescape_json_cache'));
if ($cacheFile = $cache->GetCacheFile(_JSON_ITEMS_FILE_, option('curatescape_json_storage'))) {
	echo $cacheFile;
}else{
	$json = $this->getHelper('JsonItem')->JsonItemsBrowse($items);
	if(intval(option('curatescape_json_storage')) && !is_admin_theme()){
		$cache->WriteCacheFile(_JSON_ITEMS_FILE_, $json);
	}
	echo $json;
}
