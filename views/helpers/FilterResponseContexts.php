<?php // see also FilterActionContexts
class Curatescape_View_Helper_FilterResponseContexts extends Zend_View_Helper_Abstract{
	public function FilterResponseContexts($contexts){
		if( !plugin_is_active('CuratescapeJSON') ){
			$contexts['mobile-json'] = array(
				'suffix' => 'mjson',
				'headers' => array(
					'Content-Type' => 'application/json',
					'Access-Control-Allow-Origin'=> '*',
				)
			);
		}
		if( !plugin_is_active('SuperRss') ){
			$contexts['rss-plus'] = array(
				'suffix' => 'rss-plus',
				'headers' => array(
					'Content-Type' => 'text/xml',
					'Access-Control-Allow-Origin'=> '*',
				),
			);
			if(option('curatescape_rss')){
				$contexts['rss2'] = $contexts['rss-plus'];
			}
		}
		return $contexts;
	}
}