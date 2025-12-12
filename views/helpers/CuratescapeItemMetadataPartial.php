<?php
class Curatescape_View_Helper_CuratescapeItemMetadataPartial extends Zend_View_Helper_Abstract{
	public function CuratescapeItemMetadataPartial($elementsForDisplay, $html=null)
	{
		if(!count($elementsForDisplay)) return __('Text unavailable.');
		$bylineElements = array();
		$articleElements = array();
		$mapCaptionElements = array();
		$factoidElements = array();
		$metaHtml = null;
		if($tours = apply_filters('curatescape_tours_for_item_meta',toursForItem(get_current_record('item')->id))){
			$label = count($tours) > 1 ? tourLabelString('plural') : tourLabelString();
			$elementsForDisplay[_CURATESCAPE_ITEM_TYPE_SETNAME_][__('Related %s', $label)]['texts'] = $this->getTourLinks($tours);
		}
		foreach ($elementsForDisplay as $setName => $setElements){
			foreach ($setElements as $elementName => $elementInfo){
				if($setName == 'Dublin Core'){
					if($elementName == 'Creator'){
						$bylineElements['creators'] = $elementInfo['texts'];
					}else{
						$metaHtml.='<div id="'.text_to_id(html_escape("$setName $elementName")).'" class="element">';
							$metaHtml.='<h3>'.html_escape(__($elementName)).'</h3>';
							foreach ($elementInfo['texts'] as $text){
								$metaHtml.='<div class="element-text">'.$text.'</div>';
							}
						$metaHtml.='</div>';
					}
				}elseif($setName == _CURATESCAPE_ITEM_TYPE_SETNAME_){
					if($elementName == 'Sponsor'){
						$bylineElements['sponsors'] = $elementInfo['texts'];
					}elseif($elementName == 'Lede'){
						$articleElements['lede'] = $elementInfo['texts'];
					}elseif($elementName == 'Story'){
						$articleElements['story'] = $elementInfo['texts'];
					}elseif($elementName == 'Street Address'){
						$mapCaptionElements[] = $elementInfo['texts'];
					}elseif($elementName == 'Access Information'){
						$mapCaptionElements[] = $elementInfo['texts'];
					}elseif($elementName == 'Factoid'){
						$factoidElements[] = $elementInfo['texts'];
					}else{
						$metaHtml.='<div id="'.text_to_id(html_escape("$setName $elementName")).'" class="element">';
							$metaHtml.='<h3>'.html_escape(__($elementName)).'</h3>';
							foreach ($elementInfo['texts'] as $text){
								$metaHtml.='<div class="element-text">'.$text.'</div>';
							}
						$metaHtml.='</div>';
					}
				}
			}
		}
		$html .= $this->story($articleElements, $bylineElements, $factoidElements, option('curatescape_byline'));
		$html .= $this->storyMapCaption($mapCaptionElements);
		$html .= $metaHtml ? '<div class="element-set"><h2>'.__('Metadata').'</h2>'.$metaHtml.'</div>' : null;
		return $html;
	}

	private function getTourLinks($tours = array(), $results = array()){
		foreach($tours as $tour){
			$results[] = '<a href="/tours/show/'.$tour['id'].'">'.$tour['title'].'</a>';
		}
		return $results;
	}

	private function story($articleElements = array(), $bylineElements = array(), $factoidElements = array(), $bylineLocation='after_lede', $html = null, $factoidCount = 0)
	{
		if(isset($articleElements['lede'])){
			$html .= '<div class="curatescape-lede"><p>'.$articleElements['lede'][0].'</p></div>';
		}
		if(count($bylineElements) && ($bylineLocation !== 'none')){
			$byline = '<div class="curatescape-byline"><p>'.$this->storyByline($bylineElements).'</p></div>';
			if($bylineLocation == 'before_lede'){
				$html = $byline.$html;
			}else{
				$html .= $byline;
			}
		}
		if(isset($articleElements['story'])){
			$text = normalizeTextBlocks($articleElements['story'][0]);
			if((option('curatescape_inline_factoids')) && count($factoidElements) > 0 && substr_count($text, '<p>') > 4){
				$text = insertAfterNth($text, '</p>', factoid($factoidElements), floor(substr_count($text, '<p>') / 2)); // 2.1 to
				$factoidCount++;
			}
			$html .= '<div class="curatescape-text">'.$text.'</div>';
			$html .= ($factoidCount == 0) ? factoid($factoidElements) : null;
		}
		return $html;
	}

	public function storyByline($bylineElements, $html = null)
	{
		if(isset($bylineElements['creators'])){
			$creators = oxfordAmp($bylineElements['creators']);
		}else{
			$creators = get_option('site_title');
		}
		$html .= '<span class="curatescape-author">'.__('By %s', $creators).'</span>';
		if(isset($bylineElements['sponsors'])){
			$sponsors = oxfordAmp($bylineElements['sponsors']);
			$html .= '<span class="curatescape-sponsor"> '.__('with research support from %s', $sponsors).'</span>';
		}
		return $html;
	}

	public function storyMapCaption($mapCaptionElements, $html=null)
	{
		if(!count($mapCaptionElements)) return null;
		$flattened = array_merge(...array_values($mapCaptionElements));
		$html .= implode(' | ', $flattened);
		// hidden; used for JS map figure
		return '<figcaption class="curatescape-map-caption" data-curatescape-hidden="true">'.strip_tags($html,'<a>').'</figcaption>';
	}

}