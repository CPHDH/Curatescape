<?php
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
		$request = $this->getRequest();

		$featured = $request->getParam('featured');
		$public = $request->getParam('public');
		$tags = $request->getParam('tags');

		$db = get_db();
		$table = $db->getTable('CuratescapeTour');
		$select = $table->getSelect();

		if ($featured !== null && $featured !== '') {
			$select->where("featured = ?", (int)$featured);
		}

		if ($public !== null && $public !== '') {
			$select->where("public = ?", (int)$public);
		}

		if (!empty($tags)) {
			$tagArray = array_map('trim', explode(',', $tags));

			$select->joinInner(
				array('records_tags' => $db->RecordsTags),
				'curatescape_tours.id = records_tags.record_id AND records_tags.record_type = "CuratescapeTour"',
				array());
			$select->joinInner(
				array('tags' => $db->Tag),
				'records_tags.tag_id = tags.id',
				array());
			$select->where('tags.name IN (?)', $tagArray);
			$select->group('curatescape_tours.id');
			$select->having('COUNT(DISTINCT tags.name) = ?', count($tagArray)); // matches multiple tags
		}
		$tours = $table->fetchObjects($select);
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

	public function editAction()
	{
		$request = $this->getRequest();
		// view
		$id = $request->getParam('id');
		if (!$id) {
			throw new Omeka_Controller_Exception_404('Missing tour ID.');
		}
		$tour = $this->_helper->db->findById($id, 'CuratescapeTour');
		if (!$tour) {
			throw new Omeka_Controller_Exception_404('Tour not found.');
		}
		$this->view->tour = $tour;
		// form
		if ($request->isPost()) {
			$post = $request->getPost();
			$tour->editTourMeta($post);
			$tour->editTourItems($post);
			if($post['tags']){
				$tour->applyTagString($post['tags']);
			}
			if ($tour->save()) {
				$this->_helper->flashMessenger(__('Tour updated successfully.'), 'success');
				$this->_helper->redirector->gotoRoute(array('action' => 'show', 'id' => $tour->id), 'tourAction');
			} else {
				$this->_helper->flashMessenger(__('There was an error updating the tour.'), 'error');
			}
		}
	}

}