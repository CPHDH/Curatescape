<?php
cacheBustManual(_JSON_TOURS_FILE_, false);
echo cacheConfig(option('curatescape_json_cache'));
if ($cache = getJsonCacheFile(_JSON_TOURS_FILE_, option('curatescape_json_storage'))) {
	echo $cache;
}else{
	$json = generateToursBrowseJson($tours);
	if(intval(option('curatescape_json_storage'))){
		writeJsonCacheFile(_JSON_TOURS_FILE_, $json);
	};
	echo $json;
}