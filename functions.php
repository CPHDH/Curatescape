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

function preferredItemImageUrl($item, $size = 'fullsize', $returnArray = false, $nullresult = null)
{
	if(!$item) return $nullresult;
	if(!$item->hasThumbnail()) return $nullresult;
	foreach($item->getFiles() as $file){
		if($file->has_derivative_image){
			if($returnArray == true){
				$infoArray = dimensions($file, $size);
				$infoArray['url'] = record_image_url($file, $size);
				return $infoArray;
			}
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

function storiesItemTypeBrowseURL()
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

function availableTourItemsJSON($locationRequired = true)
{
		$db = get_db();
		$itemTable = $db->getTable( 'Item' );
		$items = $itemTable->fetchObjects(
			<<<SQL
			SELECT i.* FROM {$db->prefix}items i 
			ORDER BY i.modified DESC
			SQL
		);
		foreach($items as $index => $item) {
			if($locationRequired && !hasLocation($item)) continue;
			$items[$index]['label'] = dc( $item,'Title');
		}
		return json_encode($items);
}

function hasLocation($item)
{
	return boolval( get_db()->getTable( 'Location' )->findLocationByItem( $item, true ) );
}

function altLabelIsValid($text)
{
	return (strlen(trim(strip_tags($text))) > 3);
}

function tourLabelString($plural = false)
{
	$default = __('Tour');
	$alt = option('curatescape_alt_tour_name');
	if($plural){
		$default = __('Tours');
		$alt = option('curatescape_alt_tour_name_p');
	}
	if(altLabelIsValid($alt) && !is_admin_theme()){
		return trim(strip_tags($alt));
	}
	return $default;
}

function storyLabelString($plural = false)
{
	$default = __('Story');
	$alt = option('curatescape_alt_item_type_name');
	if($plural){
		$default = __('Stories');
		$alt = option('curatescape_alt_item_type_name_p');
	}
	if(altLabelIsValid($alt) && !is_admin_theme()){
		return trim(strip_tags($alt));
	}
	return $default;
}

function toursBrowsePageTitle($total_results = 0, $isTags = false)
{
	if($isTags){
		return __('%s Tags', tourLabelString());
	}
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
			$featured = __('Non-featured');
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
	return $all.$private.$featured.' '.tourLabelString(true).' '.$tagged.__('(%s total)', $total_results);
}

function hasTours()
{
	return (totalTours() > 0);
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

function publicNavTours()
{
	return nav(
		array(
			array(
			'label' => __('All'),
			'uri' => url('tours/browse'),
			'class'=>'curatescape_secondary-nav-fix-js',
			),
			array(
			'label' => __('Featured'),
			'uri' => url('tours/browse?featured=1'),
			'class'=>'curatescape_secondary-nav-fix-js',
			),
			array(
			'label' => __('Tags'),
			'uri' => url('tours/tags') 
			)
		) 
	);
}

function linkToTour($tour = null, $text = null, $props = array(), $action = 'show')
{
	if(!$tour) {
		throw new Exception('Missing tour object');
	}
	if(empty( $text)) {
		$title = metadata($tour, 'Title');
		$text = (!empty($title)) ? $title : '[Untitled]';
	}
	return link_to($tour, $action, $text, $props);
}

function linkToTourItem($item, $tour, $text = null, $props = array())
{
	if(!$item){
		throw new Exception('Missing item object');
	}
	if(!$tour) {
		throw new Exception('Missing tour object');
	}
	$tourItem = $tour->getTourItem($item->id);
	$tourItemIndex = $tourItem->ordinal;
	if(empty($text)){
		$title = $tour->getTourItemTitleString($tourItem);
		$text = (!empty($title)) ? $title : '[Untitled]';
	}
	return link_to($item, 'show', $text, $props, array('tour'=>$tour->id, 'index'=>$tourItemIndex));
}

function tourItemsOutput($tour, $galleryType = 'gallery-inline-captions', $isLazy = 'true', $html = null)
{
	if(
		!$tour ||
		$galleryType == 'none'
	) return null;
	$html .= '<div class="curatescape-files">';
		$html .= '<div id="pswp-container" class="curatescape-image-gallery '.$galleryType.'">';
		foreach($tour->Items as $tourItem){
			$html .= '<figure class="curatescape-image-figure" itemtype="https://schema.org/ImageObject">';
				$imgDetails = preferredItemImageUrl($tourItem, 'fullsize', true);
				//$dimensions = dimensions($imgUrl, 'fullsize');
				$tourItemImage = '<img '.($isLazy ? 'loading="lazy"' : '').' title="'.$tour->getTourItemTitleString($tourItem).'" src="'.$imgDetails['url'].'" class="item-file" width="'.$imgDetails['width'].'" height="'.$imgDetails['height'].'"/>';
				$html .= linkToTourItem($tourItem, $tour, $tourItemImage, array('class'=>'gallery-image '.$imgDetails['orientation'].' pswp-item', 'data-pswp-src'=>$imgDetails['url'], 'data-pswp-width'=>$imgDetails['width'], 'data-pswp-height'=>$imgDetails['height']), 'show');
				$html .= '<figcaption>'.tourItemCaption($tour, $tourItem).'</figcaption>';
			$html .= '</figure>';
		}
		$html .= '</div>';
	$html .= '</div>';
	return $html;
}

function tourItemCaption($tour, $tourItem, $meta = array())
{
	if(!$tour || !$tourItem) return null;
	if($title=$tour->getTourItemTitleString($tourItem)){
		$meta[] = '<span class="file-title" itemprop="name"><cite>'.linkToTourItem($tourItem, $tour, $title, array(), 'show').'</cite></span>';
	}
	if($text=$tour->getTourItemTextString($tourItem)){
		$meta[] = '<span class="file-text">'.strip_tags($text).'</span>';
	}
	$caption = implode(' | ', $meta);
	$actions = '<div class="curatescape-tour-button-container">'.linkToTourItem($tourItem, $tour, __('Read More'), array('class'=>'button curatescape-button curatescape-tour-button'), 'show').'</div>';
	
	return $caption.$actions;
}

function filesOutputFigures($images = array(), $audio = array(), $video = array(), $other = array(), $galleryType = 'gallery-inline-captions', $html = null)
{
	if(!count($images) && !count($audio) && !count($video)) return null;
	$html .= '<div id="pswp-container" class="curatescape-image-gallery '.$galleryType.'">';
		$html .= filesOutputImages($images);
		$html .= filesOutputAudio($audio);
		$html .= filesOutputVideo($video);
		$html .= filesOutputDocument($other);
	$html .= '</div>';
	return $html;
}

function imageLinkMarkup($file, $size='fullsize', $linkClass='gallery-image', $imgClass='item-file', $isLazy = true, $itemprop='associatedMedia', $html = null)
{
	if(!$file) return null;
	$dimensions = dimensions($file, $size);
	$fileHref = !option('link_to_file_metadata') ? record_image_url($file, $size) : $file->getProperty('permalink');
	$html .= '<a'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="pswp-item '.$linkClass.' '.$dimensions['orientation'].' file-'.$file->id.'" data-pswp-width="'.$dimensions['width'].'" data-pswp-height="'.$dimensions['height'].'" data-pswp-src="'.record_image_url($file, $size).'" data-pswp-type="image" data-pswp-fallbackmessage="'.__('Download').'">';
		$html .= '<img'.($isLazy ? ' loading="lazy"' : null).' class="'.$imgClass.'" src="'.record_image_url($file, 'fullsize').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" alt="'.htmlentities($file->getProperty('display_title')).'" />';
	$html .= '</a>';
	return $html;
}

function mediaLinkMarkup($file, $filetype, $linkClass='gallery-image', $imgClass='fallback', $isLazy = true, $itemprop='associatedMedia', $html = null)
{
	if(!$file || !$filetype) return null;
	$dimensions = array(
		'height' => round(option('fullsize_constraint') * 9/16),
		'width' => option('fullsize_constraint'),
	); // 16:9 placeholder/fallback dimensions (see JS)
	$fileHref = !option('link_to_file_metadata') ? $file->getProperty('uri') : $file->getProperty('permalink');
	$html .= '<a'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="pswp-item '.$linkClass.' square file-'.$file->id.'" data-pswp-width="'.$dimensions['width'].'" data-pswp-height="'.$dimensions['height'].'" data-pswp-src="'.$file->getProperty('uri').'" data-pswp-type="'.$filetype.'" data-pswp-fallbackmessage="'.__('Download').'">';
		$html .= '<img'.($isLazy ? ' loading="lazy"' : null).' class="'.$imgClass.'" src="'.record_image_url($file, 'fullsize').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" alt="'.htmlentities($file->getProperty('display_title')).'" />';
	$html .= '</a>';
	return $html;
}

function documentLinkMarkup($file, $linkClass='gallery-image', $imgClass='document', $isLazy = true, $itemprop='associatedMedia', $html = null)
{
	if(!$file ) return null;
	$dimensions = array(
		'height' => option('fullsize_constraint'),
		'width' => option('fullsize_constraint'),
	); // 16:9 placeholder/fallback dimensions (see JS)
	$fileHref = !option('link_to_file_metadata') ? $file->getProperty('uri') : $file->getProperty('permalink');
	$html .= '<a'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="pswp-item '.$linkClass.' square file-'.$file->id.'" data-pswp-width="'.$dimensions['width'].'" data-pswp-height="'.$dimensions['height'].'" data-pswp-src="'.$file->getProperty('uri').'" data-pswp-type="document" data-pswp-fallbackmessage="'.__('Download').'">';
		$html .= '<img'.($isLazy ? ' loading="lazy"' : null).' class="'.$imgClass.'" src="'.record_image_url($file, 'fullsize').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" alt="'.htmlentities($file->getProperty('display_title')).'" />';
	$html .= '</a>';
	return $html;
}

function filesOutputImages($files, $schemaURI = 'https://schema.org/ImageObject', $html = null)
{
	if(!$files) return null;
	
	foreach($files as $file){
		$html .= '<figure aria-label="'.__('Image').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
			$html .= imageLinkMarkup($file, 'fullsize');
			$html .= '<figcaption>'.mediaCaptionText($file).'</figcaption>';
		$html .= '</figure>';
	}
	
	return $html;
}

function filesOutputAudio($files, $schemaURI = 'https://schema.org/AudioObject', $html = null)
{
	if(!$files) return null;
	
	foreach($files as $file){
		$html .= '<figure aria-label="'.__('Audio').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
			$html .= mediaLinkMarkup($file, 'audio');
			$html .= '<figcaption>'.mediaCaptionText($file).'</figcaption>';
		$html .= '</figure>';
	}
	
	return $html;
}

function filesOutputDocument($files, $schemaURI = 'https://schema.org/DigitalDocument', $html = null)
{
	if(!$files) return null;
	
	foreach($files as $file){
		$html .= '<figure aria-label="'.__('Document').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
			$html .= documentLinkMarkup($file);
			$html .= '<figcaption>'.mediaCaptionText($file).'</figcaption>';
		$html .= '</figure>';
	}
	
	return $html;
}

function filesOutputVideo($files, $schemaURI = 'https://schema.org/VideoObject', $html = null)
{
	if(!$files) return null;
	
	foreach($files as $file){
		$html .= '<figure aria-label="'.__('Video').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
			$html .= mediaLinkMarkup($file, 'video');
			$html .= '<figcaption>'.mediaCaptionText($file).'</figcaption>';
		$html .= '</figure>';
	}
	
	return $html;
}

function filesOutputTable($files, $subhead = true, $html = null)
{
	if(!$files) return null;
	$html .= '<div class="filestablecontainer"><table class="curatescape-additional-files">';
		$html .= ($subhead ? '<caption><h3>'.__('Documents').'</h3></caption>' : null);
		$html .= '<thead><th>'.__('File Details').'</th><th>'.__('Download').'</th></thead>';
		$html .= '<tbody>';
		foreach($files as $file){
			$meta = array(oxfordAmp(dc($file,'Creator',array('all'=>'true'))),dc($file, 'Source'),dc($file, 'Date'));
			$info = implode(' | ', array_filter($meta));
			$title = mediaCaptionText($file);
			$type = '<span class="type">'.fileSubTypeString($file).' / '.formatSize($file->size).'</span>';
			$download = '<span class="file-download"><a class="button" download href="'.record_image_url($file, 'original').'">'.__('Download').'</a>'.$type.'</span>';

			$html .= '<tr><td>'.$title.'</td><td>'.$download.'</td></tr>';
		}
		$html .= '</tbody>';
	$html .= '</table></div>';
	return $html;
}

function formatSize($bytes){
	if(!$bytes) return '0 Kb';
	if($bytes < 1048576){
		return number_format($bytes / 1024).' KB';
	}
	if($bytes < 1073741824){
		return number_format($bytes / 1048576).' MB';
	}
	if ($size < 1099511627776){
		$bytes < number_format($bytes / 1073741824).' GB';
	}
	return '1 TB+';
}

function mediaCaptionText($file, $caption = array())
{
	if(!$file) return null;
	$title = dc($file, 'Title') ? dc($file, 'Title') : __('File #%s: [Untitled]', $file->getProperty('id'));
	$caption['title'] = '<span class="file-title" itemprop="name"><cite><a title="'.__('View File Record').'" href="'.$file->getProperty('permalink').'">'.strip_tags($title).'</a></cite></span>';
	if($description = dc($file, 'Description')) {
		$caption['description'] = '<span class="file-description">'.strip_tags($description,'<a><cite><em><i><strong><b>').'</span>';
	}
	if($source = dc($file, 'Source')) {
		$caption['source'] = '<span class="file-source"><span>'.__('Source').'</span>: '.strip_tags($source, '<a><cite><em><i><strong><b>').'</span>';
	}
	if($date = dc($file, 'Date')) {
		$caption['date'] = '<span class="file-date"><span>'.__('Date').'</span>: '.strip_tags($date).'</span>';
	}
	return implode(' | ', $caption);
}

function dimensions($file, $size = 'fullsize')
{
	if(!$file) return null;
	$info = array(
		'height'=>'',
		'width'=>'',
		'orientation'=>'',
	);
	$size = getimagesize(record_image_url($file, $size));
	if(!$size || !isset($size[1])) return $info;
	$info['width'] = $size[0];
	$info['height'] = $size[1];
	$info['orientation'] = $size[0] > $size[1] ? 'landscape' : 'portrait';
	return $info;
}

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
