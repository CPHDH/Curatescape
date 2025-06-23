<?php
class Curatescape_View_Helper_HookDefineRoutes extends Zend_View_Helper_Abstract{
	public function HookDefineRoutes($args){
		$router = $args['router'];
		$router->addConfig(
			new Zend_Config_Ini(
				_PLUGIN_DIR_.DIRECTORY_SEPARATOR.'routes.ini', 'routes'
			)
		);
	}
}