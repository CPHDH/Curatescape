<?php
// 'curatescape_secondary-nav-fix-js' class used to fix double 'active' class, see secondary-nav-fix.js
class Curatescape_View_Helper_FilterPublicNavigationItems extends Zend_View_Helper_Abstract{
	public function FilterPublicNavigationItems($nav, $new=array()){
		foreach($nav as $n){
			// rename default labels to make room
			$n['label'] = option('curatescape_shorten_secondary_nav') ? str_replace('Browse All', 'All', $n['label']) : $n['label'];
			if($n['label'] === 'All'){
				$n['class'] = 'curatescape_secondary-nav-fix-js';
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
		$new[] = array('label'=> storyLabelString(true),'uri' => storiesItemTypeBrowseURL(), 'class'=>'curatescape_secondary-nav-fix-js');
		$new[] = array('label'=> __('Featured'),'uri' => url('items/browse?featured=1'), 'class'=>'curatescape_secondary-nav-fix-js');
		return $new;
	}
}