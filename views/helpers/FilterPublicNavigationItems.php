<?php
class Curatescape_View_Helper_FilterPublicNavigationItems extends Zend_View_Helper_Abstract{
	public function FilterPublicNavigationItems($nav, $new=array()){
		// 'curatescape_js_fix' class used to fix double 'active' class, see globals.js
		foreach($nav as $n){
			// rename default labels to make room
			$n['label'] = option('curatescape_shorten_secondary_nav') ? str_replace('Browse All', 'All', $n['label']) : $n['label'];
			if($n['label'] === 'All'){
				$n['class'] = 'curatescape_js_fix';
			}
			$n['label'] = option('curatescape_shorten_secondary_nav') ? str_replace('Browse by Tag', 'Tags', $n['label']) : $n['label'];
			$n['label'] = option('curatescape_shorten_secondary_nav') ? str_replace('Search Items', 'Search', $n['label']) : $n['label'];
			$n['label'] = option('curatescape_shorten_secondary_nav') ? str_replace('Browse Map', 'Map', $n['label']) : $n['label'];
			$new[] = $n;
		}
		
		if(!option('curatescape_append_secondary_nav')){
			return $new;
		}
		
		// additional items
		$storiesLabel = _CURATESCAPE_ITEM_TYPE_NAME_PLURAL_;
		if( 
			$this->altNameIsValid(option('curatescape_alt_item_type_name_p')) 
		){
			$storiesLabel = trim(strip_tags(option('curatescape_alt_item_type_name_p')));
		}
		$new[] = array('label'=> $storiesLabel,'uri' => storiesURL(), 'class'=>'curatescape_js_fix');
		$new[] = array('label'=> __('Featured'),'uri' => url('items/browse?featured=1'), 'class'=>'curatescape_js_fix');
		return $new;
	}

	private function altNameIsValid($text){
		return (strlen(trim(strip_tags($text))) > 3);
	}
}