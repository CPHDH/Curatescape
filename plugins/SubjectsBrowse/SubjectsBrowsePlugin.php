<?php 
class SubjectsBrowsePlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_hooks = array('define_routes');
	
	public function hookDefineRoutes($args)
	{
		if (is_admin_theme()) {
			return;
		}
		
		$router = $args['router'];
		$subjectsRoute = new Zend_Controller_Router_Route(
			'items/subjects',
			array(
				'module' => 'subjects-browse',
				'controller' => 'items',
				'action' => 'browse',
			)
		);
		$router->addRoute('subjects_browse', $subjectsRoute);
	}
}