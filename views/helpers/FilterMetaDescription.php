<?php
class Curatescape_View_Helper_FilterMetaDescription extends Zend_View_Helper_Abstract{
	public function FilterMetaDescription($text, $options = array('snippet'=>300)){
		if(is_admin_theme() || !option('curatescape_meta_tags')) return $text;
		$view = $this->view;
		if($view->item){
			return metadata($view->item, array('Dublin Core', 'Description'), $options);
		}
		return $text;
	}
}