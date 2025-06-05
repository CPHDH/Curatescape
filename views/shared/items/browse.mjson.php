<?php
cacheBustManual(_JSON_ITEMS_FILE_, false);
echo cacheConfig(option('curatescape_json_cache'));
if ($cache = getJsonCacheFile(_JSON_ITEMS_FILE_, option('curatescape_json_storage'))) {
	echo $cache;
}else{
	$json = generateItemsBrowseJson($items);
	if(intval(option('curatescape_json_storage'))){
		writeJsonCacheFile(_JSON_ITEMS_FILE_, $json);
	}
	echo $json;
}
