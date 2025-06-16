<?php
class Curatescape_View_Helper_HookDefineRoutes extends Zend_View_Helper_Abstract{
	public function HookDefineRoutes($args){
		$router = $args['router'];
		$router->addRoute(
			'tours', 
			new Zend_Controller_Router_Route(
			'tours/:action',
			array(
				'module' => 'curatescape',
				'controller' => 'Tours',
				'action' => 'browse',
				)
			)
		);
		$router->addRoute(
			'toursAction', 
			new Zend_Controller_Router_Route(
			'tours/show/:id',
			array(
				'module' => 'curatescape',
				'controller' => 'Tours',
				'action' => 'show',
				)
			),
			array('id' => '\d+')
		);
	}
}