<?php
/* 
** IMPORTANT!!!
** This is a dummy controller
** Used only to redirect inferred/default routes
** See also routes.ini
** These workarounds are related in part to the `curatescape_tours` table name...
** ...which was chosen to avoid conflict with legacy TourBuilder plugin `tours` table
** Mostly edge cases but some common, e.g. search record urls 
*/

class Curatescape_CuratescapeToursController extends Omeka_Controller_AbstractActionController
{
	public function indexAction()
	{
		$this->_helper->redirector->gotoUrl('/tours');
	}
	public function showAction()
	{
		$id = $this->_getParam('id');
		$this->_helper->redirector->gotoUrl('/tours/show/' . $id);
	}
	public function browseAction()
	{
		$this->_helper->redirector->gotoUrl('/tours/browse');
	}
}