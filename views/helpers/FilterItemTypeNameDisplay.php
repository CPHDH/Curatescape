<?php
class Curatescape_View_Helper_FilterItemTypeNameDisplay extends Zend_View_Helper_Abstract{
	public function FilterItemTypeNameDisplay($text=null, $displayArray=array()){
		if(
			!is_admin_theme() &&
			isset($text) &&
			($text == _CURATESCAPE_ITEM_TYPE_NAME_)
		){
			return storyLabelString();
		}
		if(
			!is_admin_theme() && 
			count($displayArray)
		){
			$text = null;
			foreach($displayArray as $key=>$value){
				if($key == 'Item Type'){
					if( 
						($displayArray[$key] == _CURATESCAPE_ITEM_TYPE_NAME_)
					){
						$displayArray[$key] = storyLabelString();
					}
				}
			}
			return $displayArray;
		}
		return $text ? $text : $displayArray;
	}

}