<?php // see also FilterResponseContexts
class Curatescape_View_Helper_FilterActionContexts extends Zend_View_Helper_Abstract{
	public function FilterActionContexts($contexts, $args){
		$controller = $args['controller'];
		if( !plugin_is_active('CuratescapeJSON') ){
			if( is_a( $controller, 'ItemsController' ) ||
				is_a( $controller, 'Curatescape_ToursController' ) ||
				is_a( $controller, 'SimplePages_PageController' ) ){
				$contexts['show'][] = 'mobile-json';
			}
			if( is_a( $controller, 'ItemsController' ) ||
				is_a( $controller, 'Curatescape_ToursController' ) ){
				$contexts['browse'][] = 'mobile-json';
			}
			if( is_a( $controller, 'SearchController' ) ){
				$contexts['index'][] = 'mobile-json';
			}
		}
		if( !plugin_is_active('SuperRss') ){
			if( is_a($controller, 'ItemsController') ){
				if( option('curatescape_rss') ){
					$contexts['browse']['rss2'] = 'rss-plus';
				}else{
					$contexts['browse'][] = 'rss-plus';
				}
			}
		}
		return $contexts;
	}
}