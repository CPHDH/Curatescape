<?php
class Curatescape_View_Helper_HookDefineRoutes extends Zend_View_Helper_Abstract{
	public function HookDefineRoutes($args){
		// TOURS/SHOW/[ID] (ToursController)
		$args['router']->addRoute('tourAction', new Zend_Controller_Router_Route(
			'tours/:action/:id',
			array(
				'module' => 'curatescape',
				'controller' => 'tours',
				'action' => 'show',
				'id' => "\d+"
			)
		));
		// TOURS/BROWSE (ToursController)
		$args['router']->addRoute('tourBrowse', new Zend_Controller_Router_Route(
			'tours/:action',
			array(
				'module' => 'curatescape',
				'controller' => 'tours',
				'action' => 'browse',
			)
		));
		// TOURS (ToursController)
		$args['router']->addRoute('tourIndex', new Zend_Controller_Router_Route(
			'tours',
			array(
				'module' => 'curatescape',
				'controller' => 'tours',
				'action' => 'index',
			)
		));
		// REDIRECT: CURATESCAPE-TOURS/SHOW/[ID] (CuratescapeToursController)
		$args['router']->addRoute('redirectDashShow', new Zend_Controller_Router_Route(
			'curatescape-tours/:action/:id',
			array(
				'module' => 'curatescape',
				'controller' => 'curatescape-tours',
				'action' => 'show',
				'id' => "\d+"
			)
		));
		// REDIRECT: CURATESCAPE-TOURS/BROWSE (CuratescapeToursController)
		$args['router']->addRoute('redirectDashBrowse', new Zend_Controller_Router_Route(
			'curatescape-tours/:action',
			array(
				'module' => 'curatescape',
				'controller' => 'curatescape-tours',
				'action' => 'browse',
			)
		));
		// REDIRECT: CURATESCAPE-TOURS (CuratescapeToursController)
		$args['router']->addRoute('redirectDashIndex', new Zend_Controller_Router_Route(
			'curatescape-tours',
			array(
				'module' => 'curatescape',
				'controller' => 'curatescape-tours',
				'action' => 'index',
			)
		));
		// REDIRECT: CURATESCAPE/TOURS/SHOW/[ID] (CuratescapeToursController)
		$args['router']->addRoute('redirectSlashShow', new Zend_Controller_Router_Route(
			'curatescape/tours/:action/:id',
			array(
				'module' => 'curatescape',
				'controller' => 'curatescape-tours',
				'action' => 'show',
				'id' => "\d+"
			)
		));
		// REDIRECT: CURATESCAPE/TOURS/BROWSE (CuratescapeToursController)
		$args['router']->addRoute('redirectSlashBrowse', new Zend_Controller_Router_Route(
			'curatescape/tours/:action',
			array(
				'module' => 'curatescape',
				'controller' => 'curatescape-tours',
				'action' => 'browse',
			)
		));
		// REDIRECT: CURATESCAPE/TOURS (CuratescapeToursController)
		$args['router']->addRoute('redirectSlashBrowse', new Zend_Controller_Router_Route(
			'curatescape/tours',
			array(
				'module' => 'curatescape',
				'controller' => 'curatescape-tours',
				'action' => 'index',
			)
		));
	}
}