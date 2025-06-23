<?php
function dc($record, $elementName, $options = array())
{
	if(!$record || !$elementName) return null;
	return metadata($record, array('Dublin Core', $elementName), $options);
}

function itm($record, $elementName, $options = array())
{
	if(!$record || !$elementName) return null;
	if(!element_exists('Item Type Metadata', $elementName)) return null;
	return metadata($record, array('Item Type Metadata', $elementName), $options);
}

function svg($name){
	// @todo: cacheable "sprite sheet"
	if(!$name) return null;
	$path = _PLUGIN_DIR_."/views/shared/images/svg/$name.svg";
	if(!file_exists($path)) return null;
	return file_get_contents($path);
}

function isCuratescapeStory($record)
{
	if(!$record) return false;
	if($record->getRecordUrl()['controller'] !== 'items') return false;
	if($record->getProperty('item_type_name') !== _CURATESCAPE_ITEM_TYPE_NAME_) return false;
	return true;
}

function normalizeTextBlocks($text, $output=null)
{
	if(!$text) return null;
	// breaks to paragraphs
	$text = str_replace(array('<p>','</p>'),'', $text);
	foreach(preg_split("#(<br */?>\s*)+#i", $text) as $p){
		$output .= '<p>'.$p.'</p>';
	}
	// remove empty tags
	$output =  preg_replace('/<[^\/>]*>([\s]?)*<\/[^>]*>/', '', $output); 
	return $output;
}

function plainText($text){
	return $text ? trim( html_entity_decode( strip_formatting($text))) : null;
}

function oxfordAmp($names=array(), $html = null)
{
	if(!$names) return array();
	for($index = 1; $index <= count($names); $index++){
		switch ($index) {
			case (count($names)):
			$delim ='';
			break;
			case (count($names)-1):
			$delim =(count($names) > 2 ? ',' : '').'&#32;<span class="curatescape-amp">&amp;</span>&#32;';
			break;
			default:
			$delim =', ';
			break;
		}
		$html .= $names[$index-1].$delim;
	}
	return $html;
}

function preferredItemImageUrl($item, $size = 'fullsize', $nullresult = null)
{
	if(!$item) return $nullresult;
	if(!$item->hasThumbnail()) return $nullresult;
	foreach($item->getFiles() as $file){
		if($file->has_derivative_image){
			return record_image_url($file, $size);
		}
	}
	return $nullresult;
}

function preferredFileImageUrl($file, $size = 'fullsize', $nullresult = null, $omitVideo = false)
{
	if(!$file) return $nullresult;
	if($file->has_derivative_image){
		if($omitVideo && fileTypeString($file) == 'video') return $nullresult;
		return record_image_url($file, $size);
	}
	return $nullresult;
}

function fileTypeString($file, $nullresult = 'Unknown')
{
	if(!$file) return $nullresult;
	$mimetype = metadata($file, 'mime_type');
	$filetype = explode('/', $mimetype);
	return isset($filetype[0]) ? $filetype[0] : $nullresult;
}

function fileSubTypeString($file, $nullresult = 'Unknown')
{
	if(!$file) return $nullresult;
	$mimetype = metadata($file, 'mime_type');
	$filetype = explode('/', $mimetype);
	return isset($filetype[1]) ? $filetype[1] : $nullresult;
}

function comboTitle($title, $subtitle, $pre = ': ', $post = null)
{
	if(!$subtitle) return $title;
	return $title.$pre.html_entity_decode(strip_formatting($subtitle)).$post;
}

function storiesURL()
{
	$itemType=get_record('ItemType', array('name'=>_CURATESCAPE_ITEM_TYPE_NAME_));
	if(!$itemType) return url();
	$url = 'items/browse?type='.$itemType->id;
	return url($url);
}

function itemTypeURL()
{
	$itemType=get_record('ItemType', array('name'=>_CURATESCAPE_ITEM_TYPE_NAME_));
	if(!$itemType) return url();
	$url = 'item-types/show/'.$itemType->id;
	return admin_url($url);
}

function allowedExtensionImg($filepath, $allowed = array('jpg','jpeg','png', 'webp')){
	$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
	return in_array($ext, $allowed);
}

function browserCategory(){
	// used to determine document viewer availability
	// used to determine audio player characteristics
	if($user_agent = $_SERVER['HTTP_USER_AGENT']){
		if (strpos($user_agent, 'Chrome')) {
			return 'chromium'; // Chrome, Edge, Opera, etc
		}
		if (strpos($user_agent, 'Firefox')) {
			return 'firefox';
		};
	}	
	return 'other';
}

function sortByTitle($a, $b)
{
	return $a['title'] <=> $b['title']; // title ASC
}

function sortByTitleReverse($a, $b)
{
	return $b['title'] <=> $a['title']; // title DESC
}

function sortById($a, $b){
	return $a['id'] <=> $b['id']; // id ASC
}

function sortByIdReverse($a, $b)
{
	return $b['id'] <=> $a['id']; // id DESC
}

function sortByOrdinal($a, $b)
{
	// 0/default value will always follow ASC custom values: 1,2,3,0,0
	if ($a['ordinal'] == $b['ordinal']) return 0;
	if ($a['ordinal'] == 0) return 1;
	if ($b['ordinal'] == 0) return -1;
	return $a['ordinal'] > $b['ordinal'] ? 1 : -1;
}

function sortByOrdinalReverse($a, $b)
{
	// 0/default value will always precede DESC custom values: 0,0,3,2,1
	if ($a['ordinal'] == $b['ordinal']) return 0;
	if ($b['ordinal'] == 0) return 1;
	if ($a['ordinal'] == 0) return -1;
	return $b['ordinal'] > $a['ordinal'] ? 1 : -1;
}

function activeSort($objects, $sort = array())
{
	if(count($sort) !== 2){
		$sortParam = Omeka_Db_Table::SORT_PARAM;
		$sortDirParam = Omeka_Db_Table::SORT_DIR_PARAM;
		$req = Zend_Controller_Front::getInstance()->getRequest();
		$currentSort = $req->getParam($sortParam); // title, id, ordinal
		$currentDir = $req->getParam($sortDirParam); // a, d
		$sort=array($currentSort,$currentDir);
	}
	switch($sort){
		case array('title','a'):
			usort($objects, 'sortByTitle');
			break;
		case array('title', null):
			usort($objects, 'sortByTitle');
			break;
		case array('id','a'):
			usort($objects, 'sortById');
			break;
		case array('id', null):
			usort($objects, 'sortById');
			break;
		case array('ordinal','a'):
			usort($objects, 'sortByOrdinal');
			break;
		case array('ordinal', null):
			usort($objects, 'sortByOrdinal');
			break;
		case array('title','d'):
			usort($objects, 'sortByTitleReverse');
			break;
		case array('id','d'):
			usort($objects, 'sortByIdReverse');
			break;
		case array('ordinal','d'):
			usort($objects, 'sortByOrdinalReverse');
			break;
	}
	return $objects;
}

function availableTourItemsJSON()
{
		$db = get_db();
		$prefix=$db->prefix;
		$itemTable = $db->getTable( 'Item' );
		if($tour = getCurrentTour()){
			$items = $itemTable->fetchObjects(
				"SELECT i.*, (SELECT count(*) FROM ".$prefix."curatescape_tour_items ti WHERE ti.item_id = i.id AND ti.tour_id = ?) AS `in_tour`
				FROM ".$prefix."items i ORDER BY i.modified DESC",
				array( $tour->id ) );
		}else{
			$items = $itemTable->fetchObjects( "SELECT i.* FROM ".$prefix."items i ORDER BY i.modified DESC");
		}
		foreach($items as $key => $arr) {
			$items[$key]['label'] = metadata( $arr, array( 'Dublin Core', 'Title' ) );
		}
	
		return json_encode($items);
}

function toursBrowsePageTitle($total_results)
{
	$featured = null;
	$private = null;
	$tagged = null;
	$all = null;
	if (isset($_GET['public'])) {
		if($_GET['public'] == 0){
			$private = __('Private');
		}else{
			$private = __('Public');
		}
	}
	if (isset($_GET['featured'])) {
		if($_GET['featured'] == 1){
			$featured = __('Featured');
		}else{
			$featured = __('Not featured');
		}
		if($private){
			$featured = ' '.strtolower($featured);
		}
	}
	if(isset($_GET['tags']) ){
		$tag = htmlspecialchars($_GET['tags']);
		$tagged = ' '.__('tagged "%s"', $tag).' ';
	}
	if(!$private && !$featured && !$tagged){
		$all = __('All');
	}
	return $all.$private.$featured.' '.__('Tours').' '.$tagged.__('(%s total)', $total_results);
}

function hasTours()
{
	return ( totalTours() > 0 );
}

function hasToursForLoop()
{
	$view = get_view();
	return $view->tours && count( $view->tours );
}

function setCurrentTour($tour)
{
	get_view()->tour = $tour;
}

function getCurrentTour()
{
	return get_view()->tour;
}

function totalTours()
{
	return count(get_view()->tours);
}

	// function link_to_tour($text = null, $props = array(), $action = 'show', $tourObj = null)
// {
// 	# Use the current tour object if none given
// 	if( ! $tourObj ) {
// 		$tourObj = getCurrentTour();
// 	}
// 	# Create default text, if it was not passed in.
// 	if( empty( $text ) ) {
// 		$tourName = tour('title', array(), $tourObj);
// 		$text = (! empty( $tourName )) ? $tourName : '[Untitled]';
// 	}
// 	return link_to($tourObj, $action, $text, $props);
// }
// 
// 
// function public_nav_tours( array $navArray = null, $maxDepth = 0 )
// {
// 	if( !$navArray )
// 	{
// 		$navArray = array(
// 			array(
// 			'label' => __('All'),
// 			'uri' => url('tours/browse') ),
// 			array(
// 			'label' => __('Tags'),
// 			'uri' => url('tours/tags') )
// 		);
// 	}
// 	return nav( $navArray );
// }
// 
// /*
// ** Display the thumb for the tour.
// ** Used to generate slideshow, etc.
// ** TODO: expand $userDefined option to encompass either a user-set globally-defined img URL or a user-set tour-specific img URL
// ** USAGE: display_tour_thumb(tour,0)
// */
// function display_tour_thumb($tour,$i,$userDefined=null){
// 
// 	$firstTourItem=tour_item_id($tour,$i);
// 
// 	$html='<div class="item-thumb hidden">';
// 	$html .= '<a href="'.html_escape(public_url('tours/show/'.tour('id'))).'">';
// 
// 	if($userDefined){
// 		$html .= '<img src="'.$userDefined.'"/>';
// 
// 	}elseif($firstTourItem){
// 		// use the thumb for the first item in the tour
// 		$item = get_record_by_id('item', $firstTourItem);
// 		$html .= item_image('square_thumbnail',array(),0,$item);
// 
// 	}else{
// 		// use the fallback if their are no items in the tour
// 		$html .= '<img src="'.public_url('plugins/TourBuilder/views/public/images/default_thumbnail.png').'"/>';
// 	}
// 
// 	$html .= '</a></div>';
// 
// 	return $html;
// }
// /*
// ** Get an ID of an item in a tour
// ** $tour sets the tour object
// ** $i is used to choose the position in the item array
// ** USAGE: tour_item_id(tour,0)
// */
// function tour_item_id($tour,$i){
// 	$tourItems =array();
// 	foreach( $tour->Items as $items ){
// 		array_push($tourItems,$items->id);
// 	}
// 	return isset($tourItems[$i]) ? $tourItems[$i] : null;
// }
// 
// /*
// ** Uses the query parameters posted from the tour location links on tours/show
// ** Adds a prev/info/next link to items/show for navigating tour locations
// */
// 
// function tour_nav( $html=null, $label='Tour', $alwaysShow=false, $item_id=null )
// {
// 	$intlLabel = __($label);
// 
// 	if ( (isset($_GET['tour'])) && (isset($_GET['index'])) )
// 	{
// 		$index = htmlspecialchars($_GET[ 'index' ]);
// 		$tour_id = htmlspecialchars($_GET['tour']);
// 		$tour = get_record_by_id( 'tour', $tour_id );
// 
// 		$prevIndex = $index -1;
// 		$nextIndex = $index +1;
// 
// 		$tourTitle = metadata( $tour, 'title' );
// 		$tourURL = html_escape( public_url( 'tours/show/'.$tour_id ) );
// 
// 		// Items
// 		$current = tour_item_id( $tour, $index );
// 		$next = tour_item_id( $tour, $nextIndex );
// 		$prev = tour_item_id( $tour, $prevIndex );
// 
// 		// Begin building the tour navigation
// 		$html = ''
// 			. '<div class="tour-nav">'
// 			. "$intlLabel " . __('navigation') . ':&nbsp;&nbsp;'
// 			. '<span class="tour-nav-links">';
// 
// 		// Add the previous item to the navigation if present.
// 		if( $prev )
// 		{
// 			$prevUrl = public_url( "items/show/$prev?tour=$tour_id&index=$prevIndex");
// 			$html .= ''
// 				. '<a title="' . __('Previous stop on %s', $intlLabel) .'"'
// 				. "href=\"$prevUrl\">" . __('Previous') . '</a>'
// 				. ' | ';
// 		}
// 
// 		if( $tourURL )
// 		{
// 			$html .= '<a title= "'.__('View %1$s: %2$s', $intlLabel, $tourTitle).'"href="'.$tourURL.'">'.__('%s Info', $intlLabel).'</a>';
// 		}
// 
// 		// Add the next item to the navigation if present
// 		if( $next )
// 		{
// 			$nextUrl = public_url( "items/show/$next?tour=$tour_id&index=$nextIndex");
// 			$html .= ' | '
// 				. '<a title="' . __('Next stop on %s', $intlLabel).'" href="'.$nextUrl.'">' . __('Next') . '</a>';
// 		}
// 
// 		$html .= '</span>'
// 			. '</div>';
// 
// 		return $html;
// 	}else{
// 		if($alwaysShow && $item_id){
// 			// theme designers can set $alwaysShow to true and $item_id to [an item id] to show the tour info for all items if it exists (e.g. for sites where all items are part of a tour)
// 			$html .= cta_tour_for_item($item_id,$intlLabel);
// 			return $html;
// 		}else{
// 			return null;
// 		}
// 	}
// }
// 
// /* get a list of related tour links for a given item, for use on items/show template */
// function tours_for_item($item_id=null,$heading=null){
// 
// 	if(is_int($item_id)){
// 		$db = get_db();
// 		$prefix=$db->prefix;
// 		$select = $db->select()
// 		->from(array('ti' => $prefix.'tour_items')) // SELECT * FROM omeka_tour_items as ti
// 		->join(array('t' => $prefix.'tours'),    	// INNER JOIN omeka_tours as t
// 			'ti.tour_id = t.id')					// ON ti.tour_id = t.id
// 		->where("item_id=$item_id AND public=1");   // WHERE item_id=$item_id
// 		$q = $select->query();
// 		$results = $q->fetchAll();
// 
// 		$html=null;
// 		if($results){
// 			$h=(count($results)>1) ? __('Related Tours') : __('Related Tour');
// 			$h = ($heading) ? $heading : $h;
// 			$html.='<div id="tour-for-item"><h3>'.$h.'</h3><ul>';
// 			foreach($results as $result){
// 				$html.='<li><a class="tour-for-item" href="/tours/show/'.$result['id'].'">';
// 				$html.=$result['title'];
// 				$html.='</a></li>';
// 			}
// 			$html.='</ul></div>';
// 		}
// 		return $html;
// 	}
// }
// 
// /* generate a call to action prompting users to navigate from an item page to the tour page (must be enabled at theme level by setting optional params in tour_nav() function) */
// function cta_tour_for_item($item_id=null,$intlLabel='Tour'){
// 
// 	if(is_int($item_id)){
// 		$db = get_db();
// 		$prefix=$db->prefix;
// 		$select = $db->select()
// 		->from(array('ti' => $prefix.'tour_items')) // SELECT * FROM omeka_tour_items as ti
// 		->join(array('t' => $prefix.'tours'),		// INNER JOIN omeka_tours as t
// 			'ti.tour_id = t.id')					// ON ti.tour_id = t.id
// 		->where("item_id=$item_id AND public=1");   // WHERE item_id=$item_id
// 		$q = $select->query();
// 		$results = $q->fetchAll();
// 
// 		$html=null;
// 		if($results){
// 			$html .= '<div class="tour-nav-cta">';
// 			$html .= '<p>'.__('This is an entry from a multi-part %1s:<br><strong><em>%2s</em></strong>',strtolower($intlLabel),$results[0]['title']).'</p>';
// 			$html .= '<a class="button button-primary" href="/tours/show/'.$results[0]['id'].'" title="'.$results[0]['title'].'">'.__('View %s Info',$intlLabel).'</a>';
// 			$html .= '</div>';
// 		}
// 		return $html;
// 	}
// }
