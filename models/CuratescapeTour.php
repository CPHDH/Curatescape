<?php
require_once 'CuratescapeTourTable.php';

class CuratescapeTour extends Omeka_Record_AbstractRecord
{
	public $title;

	public $description;

	public $credits;

	public $featured = 0;

	public $public = 0;

	public $postscript_text;

	public $ordinal = 0;
	
	public $modified; // @todo
	
	public $added; // @todo

	protected $_related = array(
		'Items' => 'getItems',
		'Tags'=> 'getTags'
	);

	public function _initializeMixins()
	{
		$this->_mixins[] = new Mixin_Search($this);
		$this->_mixins[] = new Mixin_Tag($this);
		$this->_mixins[] = new Mixin_Timestamp($this);
		$this->_mixins[] = new Mixin_PublicFeatured($this);
	}

	public function addTourItem($itemId, $ordinal = null, $item_subtitle = null, $item_text = null)
	{
		if(!is_numeric($itemId)) {
			$itemId = $itemId->id; // @todo: is this really necessary?
		}
		// get the next ordinal
		$db = get_db();
		$tiTable = $db->getTable('CuratescapeTourItem');
		$select = $tiTable->getSelectForCount();
		$select->where('tour_id = ?', array( $this->id));
		if($ordinal === null) {
			$ordinal = $tiTable->fetchOne($select);
		}
		// clean up text content
		$item_subtitle = trim(strip_tags($item_subtitle));
		$item_text = trim(strip_tags($item_text));
		// create new tour item
		$tourItem = new CuratescapeTourItem;
		$tourItem->tour_id = $this->id;
		$tourItem->item_id = $itemId;
		$tourItem->ordinal = $ordinal;
		$tourItem->subtitle = $item_subtitle;
		$tourItem->text = $item_text;
		$tourItem->save();
	}

	private function addTourItemsByPost($post)
	{
		$ids = explode(',', trim($post['tour_item_ids']));
		foreach($ids as $id){
			$id = intval($id);
			$item_subtitle = $post['ti_sub_'.$id];
			$item_text = $post['ti_text_'.$id];
			if($id){
				$this->addTourItem( $id, $index, $item_subtitle, $item_text);
				$index++;
			}
		}
	}

	public function editTourMeta($post)
	{
		$this->public = $post['public'];
		$this->featured = $post['featured'];
		$this->ordinal = $post['ordinal'] ? $post['ordinal'] : 0;
		$this->title = $post['title'];
		$this->credits = $post['credits'];
		$this->description = $post['description'];
		$this->postscript_text = $post['postscript_text'];
		$this->updateSearchIndex();
	}

	public function editTourItems($post, $index = 0)
	{
		$this->removeAllTourItems();
		$this->addTourItemsByPost($post);
	}

	private function removeAllTourItems(){
		$db = get_db();
		$tiTable = $db->getTable('CuratescapeTourItem');
		$select = $tiTable->getSelect();
		$select->where( 'tour_id = ?', array( $this->id ) );
		$tourItems = $tiTable->fetchObjects( $select );
		for($i = 0; $i < count($tourItems); $i++) {
			$tourItems[$i]->delete();
		}
	}

	private function updateSearchIndex()
	{
		if (!$this->public) {
			$this->setSearchTextPrivate();
		}
		$this->setSearchTextTitle($this->title);
		$this->addSearchText($this->title);
		$this->addSearchText($this->description);
	}

	protected function afterSave($args, $index = 0)
	{
		if($post = $args['post']){
			if(!$args['insert']){
				$this->removeAllTourItems();
			}
			if($post['tags']){
				$this->applyTagString($post['tags']);
			}
			$this->addTourItemsByPost($post);
		}
		$this->updateSearchIndex();
	}

	protected function beforeDelete()
	{
		$this->removeAllTourItems();
		$this->deleteTaggings();
	}

	protected function _validate()
	{
		if(empty( $this->title)){
			$this->addError( 'title', 'Tour must be given a title.' );
		}
		if(strlen( $this->title) > 255){
			$this->addError( 'title', 'Title for a tour must be 255 characters or fewer.' );
		}
		if(!$this->fieldIsUnique('title')){
			$this->addError('title', 'The Title is already in use by another tour. Please choose another.');
		}
		if(intval($this->ordinal) < 0 || $this->ordinal == '' || !is_numeric($this->ordinal)){
			$this->addError('custom order', 'The value for the custom order must be a number equal to or greater than 0.');
		}
	}

	public function getItems()
	{
		return $this->getTable()->findItemsByTourId($this->id);
	}

	public function getFile()
	{
		// register default record_image
		$file = null;
		if($tourItems = $this->getItems()){
			if($files = $tourItems[0]->getFiles()){
				foreach($files as $f){
					if($f->has_derivative_image){
						$file = $f;
						return $file;
					}
				}
			}
		}
		return $file;
	}

	public function getFileCustom(){
		if(option('curatescape_tour_thumb_style') == 'composite'){
			return $this->compositeSvg();
		}else{
			return record_image($this);
		}
	}

	private function getFilesForComposite($max = 4)
	{
		$output = array();
		if($tourItems = $this->getItems()){
			foreach($tourItems as $item){
				if(count($output) >= $max) return $output;
				if($files = $item->getFiles()){
					foreach($files as $f){
						if($f->has_derivative_image){
							array_push($output, record_image_url($f, 'square_thumbnail'));
							continue 2;
						}
					}
				}
			}
		}
		return $output;
	}

	private function compositeSvg($class = 'composite', $imgSize = 200, $svgSize = 400)
	{
		$svg = null;
		$imgSources = $this->getFilesForComposite(4);
		if($imgSize = intval(option('square_thumbnail_constraint'))){
			$svgSize = intval($imgSize * 2);
		}
		$coords = array(
			array('x'=>0,'y'=>0),
			array('x'=>$imgSize,'y'=>0),
			array('x'=>0,'y'=>$imgSize),
			array('x'=>$imgSize,'y'=>$imgSize),
		);
		$svg .= '<svg
			class="'.$class.'"
			xmlns="http://www.w3.org/2000/svg" 
			xmlns:xlink="http://www.w3.org/1999/xlink" 
			viewBox ="0 0 '.$svgSize.' '.$svgSize.'" 
			version="1.1" 
			width="'.$imgSize.'" 
			height="'.$imgSize.'" 
			>';
			foreach($imgSources as $i=>$src){
				$svg .= '<image href="'.$src.'" x="'.$coords[$i]['x'].'" y="'.$coords[$i]['y'].'" height="'.$imgSize.'px" width="'.$imgSize.'px"/>';
			}
		$svg .= '</svg>';
		return $svg;
	}

	public function getTourItem($itemId)
	{
		$db = get_db();
		$tiTable = $db->getTable('CuratescapeTourItem');
		$select = $tiTable->getSelect();
		$select->where( 'tour_id='.$this->id.' AND item_id='.$itemId);
		return $tiTable->fetchObject($select);
	}

	public function getTourItemByIndex($i){
		if($tourItems = $this->getItems()){
			if(isset($tourItems[$i])){
				return $tourItems[$i];
			}
		}
		return null;
	}

	public function tourItemTitleString($item)
	{
		$tourItemColumns = $this->getTourItem($item->id);
		if($subtitle = $tourItemColumns->subtitle){
			$unfilteredTitle = dc($item,'Title', array('no_filter'=>true));
			return $unfilteredTitle.': '.$subtitle;
		}
		return dc($item,'Title');
	}

	public function tourItemTextString($item)
	{
		$tourItemColumns = $this->getTourItem($item->id);
		if($text = $tourItemColumns->text){
			return $text;
		}
		if($text = itm($item,'Lede')){
			return $text;
		}
		return dc($item,'Description'); // default snippet
	}

	public function tourColophon($colophonArray = array(), $separator = ' | ')
	{
		if($credits = $this->credits){
			$colophonArray[] = __('Tour curated by: %s', $credits);
		}
		if($ps = $this->postscript_text){
			$colophonArray[] = $ps;
		}
		return normalizeTextBlocks(implode($separator, $colophonArray));
	}

	public function tourItemCaption($tourItem, $meta = array(), $mapShowButton = null)
	{
		if(!$tourItem) return null;
		if($title=$this->tourItemTitleString($tourItem)){
			$meta[] = '<span class="file-title" itemprop="name"><cite>'.$this->linkToTourItem($tourItem, $title, array(), 'show').'</cite></span>';
		}
		if($text=$this->tourItemTextString($tourItem)){
			$meta[] = '<span class="file-text">'.strip_tags($text).'</span>';
		}
		$caption = implode(' | ', $meta);
		if(hasLocation($tourItem)){
			$mapShowButton = '<a class="button" data-item-id="'.$tourItem->id.'" href="javascript:void(0)">'.__('Show on Map').'</a>';
		}
		$actions = '<div class="curatescape-tour-button-container">'.$this->linkToTourItem($tourItem, __('Read More'), array('class'=>'button curatescape-button curatescape-tour-button'), 'show').$mapShowButton.'</div>';
		
		return $caption.$actions;
	}

	public function linkToTourItem($item, $text = null, $props = array())
	{
		if(!$item){
			throw new Exception('Missing item object');
		}
		$tourItem = $this->getTourItem($item->id);
		$tourItemIndex = $tourItem->ordinal;
		if(empty($text)){
			$title = $this->tourItemTitleString($tourItem);
			$text = (!empty($title)) ? $title : '[Untitled]';
		}
		return link_to($item, 'show', $text, $props, array('tour'=>$this->id, 'index'=>$tourItemIndex));
	}

	public function tourGeolocationMap($html = null)
	{
		$height = option('geolocation_item_map_height') ? 'height='.option('geolocation_item_map_height') : null;
		$range = implode(',', array_column($this->getItems(), 'id'));
		$html .= '<figure class="tour-items-map" data-tour-id="'.$this->id.'" data-tour-items="'.$range.'">';
			$html .=  get_view()->shortcodes('[geolocation range='.$range.' '.$height.']');
			$html .= '<figcaption class="curatescape-map-caption">';
				$html .=  __('%1s Map', tourLabelString());
			$html .= '</figcaption>';
		$html .= '</figure>';
		return $html;
	}

	public function tourItemsOutput($galleryType = 'gallery-inline-captions', $isLazy = 'true', $html = null)
	{
		if($galleryType == 'none') return null;
		$html .= '<div class="curatescape-files">';
			$html .= '<div id="pswp-container" class="curatescape-image-gallery '.$galleryType.'">';
			foreach($this->Items as $tourItem){
				$html .= '<figure class="curatescape-image-figure" itemtype="https://schema.org/ImageObject">';
					$imgDetails = preferredItemImageUrl($tourItem, 'fullsize', true);
					$tourItemImage = '<img '.($isLazy ? 'loading="lazy"' : '').' title="'.$this->tourItemTitleString($tourItem).'" src="'.$imgDetails['url'].'" class="item-file" width="'.$imgDetails['width'].'" height="'.$imgDetails['height'].'"/>';
					$html .= $this->linkToTourItem($tourItem, $tourItemImage, array('class'=>'gallery-image '.$imgDetails['orientation'].' pswp-item', 'data-pswp-src'=>$imgDetails['url'], 'data-pswp-width'=>$imgDetails['width'], 'data-pswp-height'=>$imgDetails['height']), 'show');
					$html .= '<figcaption>'.$this->tourItemCaption($tourItem).'</figcaption>';
				$html .= '</figure>';
			}
			$html .= '</div>';
		$html .= '</div>';
		return $html;
	}
}
