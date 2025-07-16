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

function allowLinks($text){
	return $text ? trim(strip_tags(html_entity_decode($text),'<a>')) : null;
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
			return 'chromium'; // doc viewer, round/light audio player
		}
		if (strpos($user_agent, 'Firefox')) {
			return 'firefox'; // doc viewer, square/dark audio player
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
		$itemTable = $db->getTable( 'Item' );
		$items = $itemTable->fetchObjects(
			<<<SQL
			SELECT i.* FROM {$db->prefix}items i 
			ORDER BY i.modified DESC
			SQL
		);
		foreach($items as $index => $item) {
			if(!hasLocation($item) || !isCuratescapeStory($item)) continue;
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

function toursForItem($item_id = null)
{
	if(!is_int($item_id)) return null;

	$db = get_db();
	$prefix = $db->prefix;
	$select = $db->select()
	->from(array('ti' => $prefix.'curatescape_tour_items'))
	->join(array('t' => $prefix.'curatescape_tours'), 'ti.tour_id = t.id')
	->where("item_id=$item_id AND public=1");
	$q = $select->query();
	$results = $q->fetchAll();
	return $results;
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
	if(boolval(option('curatescape_gallery_style') === 'gallery-inline-captions')){
		$html .= '<div'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="curatescape-inline-media file-'.$file->id.'">';
			if($filetype == 'audio'){
				$html .= '<audio class="curatescape-inline-audio" data-browser="'.browserCategory().'" controls src="'.$file->getProperty('uri').'"></audio>';
			}elseif($filetype == 'video'){
				$html .= '<video class="curatescape-inline-video" data-browser="'.browserCategory().'" controls src="'.$file->getProperty('uri').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'"></video>';
			}
		$html .= '</div>';
	}else{
		$html .= '<a'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="pswp-item '.$linkClass.' square file-'.$file->id.'" data-pswp-width="'.$dimensions['width'].'" data-pswp-height="'.$dimensions['height'].'" data-pswp-src="'.$file->getProperty('uri').'" data-pswp-type="'.$filetype.'" data-pswp-fallbackmessage="'.__('Download').'">';
			$html .= '<img'.($isLazy ? ' loading="lazy"' : null).' class="'.$imgClass.'" src="'.record_image_url($file, 'fullsize').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" alt="'.htmlentities($file->getProperty('display_title')).'" />';
		$html .= '</a>';
	}
	
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
	if(boolval(option('curatescape_gallery_style') === 'gallery-inline-captions') && option('curatescape_lightbox_docs') === '0'){
		$html .= '<div'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="curatescape-inline-document file-'.$file->id.'">';
			//$html .= '<img'.($isLazy ? ' loading="lazy"' : null).' class="'.$imgClass.'" src="'.record_image_url($file, 'fullsize').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" alt="'.htmlentities($file->getProperty('display_title')).'" />';
			if(browserCategory() == 'chromium'){
				$html .= '<iframe width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" src="'.$file->getWebPath('original').'"></iframe>';
			}elseif(browserCategory() == 'firefox'){
				$html .= '<object width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" data="'.$file->getWebPath('original').'"></object>';
			}else{
				$html .= '<a width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" download href="'.$file->getWebPath('original').'"><img src="'.$file->getWebPath('fullsize').'"/></a>';
			}
		$html .= '</div>';
	}else{
		$html .= '<a'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="pswp-item '.$linkClass.' square file-'.$file->id.'" data-pswp-width="'.$dimensions['width'].'" data-pswp-height="'.$dimensions['height'].'" data-pswp-src="'.$file->getProperty('uri').'" data-pswp-type="document" data-pswp-fallbackmessage="'.__('Download').'">';
			$html .= '<img'.($isLazy ? ' loading="lazy"' : null).' class="'.$imgClass.'" src="'.record_image_url($file, 'fullsize').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" alt="'.htmlentities($file->getProperty('display_title')).'" />';
		$html .= '</a>';
	}
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
