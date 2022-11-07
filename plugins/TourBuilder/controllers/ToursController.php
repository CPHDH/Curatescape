<?php
require_once 'Tour.php';
require_once 'TourItem.php';

class TourBuilder_ToursController extends Omeka_Controller_AbstractActionController
{
	public function init()
	{
		$this->_helper->db->setDefaultModelName( 'Tour' );
	}
	
	public function tagsAction()
	{
		$params = array_merge($this->_getAllParams(), array('type'=>'Tour'));
		$tags = $this->_helper->db->getTable('Tag')->findBy($params);
		$this->view->assign(compact('tags'));
	}
	
}