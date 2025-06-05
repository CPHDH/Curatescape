<?php
class Curatescape_View_Helper_FilterPublicNavigationAdminBar extends Zend_View_Helper_Abstract{
	public function FilterPublicNavigationAdminBar($nav)
	{
		if(!option('curatescape_admin_bar_edit')) return $nav;
		$user = current_user();
		$view = $this->view;
		if (isset($user) && isset($view)){
			$acl = get_acl();
			if(isset($view->item) && $acl->isAllowed($user, $view->item, 'edit')){
				$link = array(
					'class' =>'curatescape-admin-edit',
					'label' => __('Edit Item'),
					'uri' => admin_url('/items/edit/' . metadata('items', 'id')),
				);
			}elseif(isset($view->collection) && $acl->isAllowed($user, $view->collection, 'edit')){
				$link = array(
					'class' =>'curatescape-admin-edit',
					'label' => __('Edit Collection'),
					'uri' => admin_url('/collections/edit/' . metadata('collections', 'id')),
				);
			}elseif(isset($view->file) && $acl->isAllowed($user, $view->file, 'edit')){
				$link = array(
					'class' =>'curatescape-admin-edit',
					'label' => __('Edit File'),
					'uri' => admin_url('/files/edit/' . metadata('files', 'id')),
				);
			}elseif(isset($view->exhibit) && $acl->isAllowed($user, $view->exhibit, 'edit')){
				$link = array(
					'class' =>'curatescape-admin-edit',
					'label' => __('Edit Exhibit'),
					'uri' => admin_url('/exhibits/edit/' . metadata('exhibit', 'id')),
				);
			}elseif(isset($view->simple_pages_page) && $acl->isAllowed($user, $view->simple_pages_page, 'edit')){
				$link = array(
					'class' =>'curatescape-admin-edit',
					'label' => __('Edit Page'),
					'uri' => admin_url('/simple-pages/index/edit/id/' . $view->simple_pages_page->id),
				);
			}elseif(isset($view->tour)){
				// @todo: user can edit?
				$link = array(
					'class' =>'curatescape-admin-edit',
					'label' => __('Edit Tour'),
					'uri' => admin_url('/tours/edit/'.$view->tour->id),
				);
			}
			if(isset($link)){
				// 2nd from last position
				$shift = array_pop($nav);
				$nav[] = $link;
				$nav[] = $shift;
			}
		}
		return $nav;	
	}
}