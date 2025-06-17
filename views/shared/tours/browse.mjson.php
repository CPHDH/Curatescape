<?php
$cache = get_view()->Cache();
$cache->CacheBustManual(_JSON_TOURS_FILE_, false);
echo $cache->Config(option('curatescape_json_cache'));
if ($cacheFile = $cache->GetJsonFile(_JSON_TOURS_FILE_, option('curatescape_json_storage'))) {
	echo $cacheFile;
}else{
	$json = generateToursBrowseJson($tours);
	if(intval(option('curatescape_json_storage'))){
		$cache->WriteJsonFile(_JSON_TOURS_FILE_, $json);
	};
	echo $json;
}