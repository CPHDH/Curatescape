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
			$this->Bypass($bypassLoggedIn) ||
			!file_exists($filepath) ||
			!is_readable($filepath) ||
			!$this->FileIsCurrent($filepath, $maxSeconds)
		) return false;
		if($content = file_get_contents($filepath)) return $content;
		return false;
	}
	public function WriteCacheFile($filepath, $content = '', $bypassPathCheck = false){
		if(!$bypassPathCheck && !$this->cachablePath()) return false;
		if(!is_dir(_CURATESCAPE_CACHE_DIR_) && !mkdir(_CURATESCAPE_CACHE_DIR_, 0755, true)) return false;
		$this->SweepTempFiles($filepath);
		// write beside the target and rename over it
		if($tmp = @tempnam(dirname($filepath), basename($filepath).'.')){
			// full disk gives a short count rather than false
			$complete = file_put_contents($tmp, $content) === strlen((string) $content);
			if($complete && @rename($tmp, $filepath)){
				@chmod($filepath, 0644); // tempnam() creates at 0600
				return true;
			}
			@unlink($tmp);
			if(!$complete) return false;
		}
		// if no temp file or no rename, fall back to writing in place
		if(!file_exists($filepath)){
			return boolval(file_put_contents($filepath, $content));
		}
		if(!is_writable($filepath)) return false;
		return boolval(file_put_contents($filepath, $content));
	}
	/*
	** Remove stranded temp files.
	** Matches cache file with suffix after extension
	** Age floor prevents deleting an in-use temp file
	*/
	private function SweepTempFiles($filepath, $maxAgeSeconds = 3600){
		foreach(glob($filepath.'.*') ?: array() as $tmp){
			if(is_file($tmp) && (time() - filemtime($tmp)) > $maxAgeSeconds){
				@unlink($tmp);
			}
		}
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
			if(isset($_GET['curatescape_cache_break']) && $_GET['curatescape_cache_break'] == 'debug'){
				date_default_timezone_set("UTC");
				$timeUpdated = date('H:i:s');
				$webpath = WEB_FILES . '/curatescape/' . basename($filepath);
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
		$base = parse_url(WEB_ROOT, PHP_URL_PATH); // subdirectory prefix, if any
		$requestUri = $_SERVER['REQUEST_URI'];
		if($base && str_starts_with($requestUri, $base)){
			$requestUri = substr($requestUri, strlen($base));
		}
		return boolval(in_array($requestUri, _CACHEABLE_PATHS_));
	}
}