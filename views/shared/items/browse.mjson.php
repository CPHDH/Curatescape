<?php
$cache = get_view()->Cache();
$cache->CacheBustManual(_JSON_ITEMS_FILE_, false);
echo $cache->Config(option('curatescape_json_cache'));
if ($cacheFile = $cache->GetJsonFile(_JSON_ITEMS_FILE_, option('curatescape_json_storage'))) {
	echo $cacheFile;
}else{
	$json = generateItemsBrowseJson($items);
	if(intval(option('curatescape_json_storage'))){
		$cache->WriteJsonFile(_JSON_ITEMS_FILE_, $json);
	}
	echo $json;
}
