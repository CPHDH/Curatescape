<?php
// require_once 'CuratescapeTour.php';
// require_once 'CuratescapeTourItem.php';

class Curatescape_ToursController extends Omeka_Controller_AbstractActionController
{
	public function init()
	{
		$this->_helper->db->setDefaultModelName('CuratescapeTour');
	}

	public function tagsAction()
	{
		$params = array_merge($this->_getAllParams(), array('type'=>'CuratescapeTour'));
		$tags = $this->_helper->db->getTable('Tag')->findBy($params);
		$this->view->assign(compact('tags'));
	}

	public function browseAction()
	{
		$table = get_db()->getTable('CuratescapeTour'); 
		$tours = $table->findAll();
		$total_results = count($tours);
		$this->view->tours = $tours;
		$this->view->total_results = $total_results;
	}

	public function showAction()
	{
		$id = $this->getRequest()->getParam('id');
		if (!$id) {
			throw new Omeka_Controller_Exception_404('Missing tour ID.');
		}
		$tour = $this->_helper->db->findById($id, 'CuratescapeTour');
		if (!$tour) {
			throw new Omeka_Controller_Exception_404('Tour not found.');
		}
		$this->view->tour = $tour;
	}
}