<?php
class Curatescape_View_Helper_CuratescapeCache extends Zend_View_Helper_Abstract{
	public function CuratescapeCache(){
		return $this;
	}
	public function Config($seconds = 300, $bypassLoggedIn = true){
		if($this->Bypass($bypassLoggedIn)) return null;
		header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + intval($seconds)));
		header('Cache-Control: public, max-age='.intval($seconds));
	}
	public function Bypass($bypassLoggedIn = true){
		if(!$this->cachablePath()) return true;
		return boolval($bypassLoggedIn && current_user());
	}
	public function FileIsCurrent($filepath, $maxSeconds){
		return boolval( time()-filemtime($filepath) < intval($maxSeconds) );
	}
	public function GetCacheFile($filepath, $maxSeconds = 0, $bypassLoggedIn = true){
		if(
			/* $maxSeconds = '0' means cache is disabled, return false and generate from db */
			!boolval($maxSeconds) || 
			!file_exists($filepath) || 
			!is_readable($filepath) || 
			!$this->FileIsCurrent($filepath, $maxSeconds) ||
			$this->Bypass($bypassLoggedIn) 
		) return false;
		if($content = file_get_contents($filepath)) return $content;
		return false;
	}
	public function WriteCacheFile($filepath, $content = '', $bypassPathCheck = false){
		if(!$bypassPathCheck && !$this->cachablePath()) return false;
		if(!file_exists($filepath)){
			return boolval(file_put_contents($filepath, $content));
		}
		if(!is_writable($filepath)) return false;
		return boolval(file_put_contents($filepath, $content));
	}
	public function CacheBustManual($filepath, $afterSave = false){ 
		if( $afterSave ||
			(
				current_user() && 
				is_allowed('Settings', 'edit') &&
				isset($_GET['curatescape_cache_break'])
			) 
		){
			file_put_contents($filepath, null);
			// ?output=mobile-json&curatescape_cache_break=debug
			if($_GET['curatescape_cache_break'] == 'debug'){
				date_default_timezone_set("UTC");
				$timeUpdated = date('H:i:s');
				$webpath = WEB_ROOT.'/plugins/'._PLUGIN_NAME_.str_replace(_PLUGIN_DIR_, '', $filepath);
				$livefeed = current_url(array('output'=>'mobile-json', 'curatescape_cache_break'=>'live'));
				echo '<code><ul><li>'.implode('</li><li>', array( 
					__('API Endpoint: %s', '<a href="'.$livefeed.'">'.str_replace('&curatescape_cache_break=live','',$livefeed).'</a>'),
					__('Cache file path (WEB): %s', '<a href="'.$webpath.'">'.$webpath.'</a>' ),
					__('Cache file path (SERVER): %s', $filepath ),
					__('Cache file exists: %s', boolval(file_exists($filepath)) ? 'YES' : 'NO' ),
					__('Cache file is readable: %s', boolval(is_readable($filepath)) ? 'YES' : 'NO' ),
					__('Cache file is writable: %s', boolval(is_writable($filepath)) ? 'YES' : 'NO' ),
					__('Cache file size (as of %1s UTC): %2s bytes', $timeUpdated, filesize($filepath) ),
				)).'</li></ul></code>';
				exit;
			 }
		}
	}
	private function cachablePath(){
		// this protects against caching the wrong content (e.g. json results with query params)
		return boolval(in_array($_SERVER['REQUEST_URI'], _CACHEABLE_PATHS_));
	}
}