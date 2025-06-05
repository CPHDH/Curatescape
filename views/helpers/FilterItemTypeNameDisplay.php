<?php
class Curatescape_View_Helper_FilterItemTypeNameDisplay extends Zend_View_Helper_Abstract{
	public function FilterItemTypeNameDisplay($text=null, $displayArray=array()){
		if(
			!is_admin_theme() &&
			isset($text) &&
			($text == _CURATESCAPE_ITEM_TYPE_NAME_) &&
			$this->altNameIsValid(option('curatescape_alt_item_type_name'))
		){
			return trim(strip_tags(option('curatescape_alt_item_type_name')));
		}
		if(!is_admin_theme() && count($displayArray)){
			$text = null;
			foreach($displayArray as $key=>$value){
				if($key == 'Item Type'){
					if( 
						($displayArray[$key] == _CURATESCAPE_ITEM_TYPE_NAME_) &&
						$this->altNameIsValid(option('curatescape_alt_item_type_name_p'))
					){
						$displayArray[$key] = trim(strip_tags(option('curatescape_alt_item_type_name')));
					}
				}
			}
			return $displayArray;
		}
		return $text ? $text : $displayArray;
	}

	private function altNameIsValid($text){
		return (strlen(trim(strip_tags($text))) > 3);
	}
}