<?php
/*
** Used only to redirect inferred/default routes
** See also routes.ini
*/

class Curatescape_CuratescapeToursController extends Omeka_Controller_AbstractActionController
{
	public function indexAction()
	{
		$this->_helper->redirector->gotoUrl('/tours');
	}
	public function showAction()
	{
		$this->_helper->redirector->gotoUrl('/tours/show/' . $this->_getParam('id') );
	}
	public function browseAction()
	{
		$this->_helper->redirector->gotoUrl('/tours/browse');
	}
}