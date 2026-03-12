<?php
class Curatescape_View_Helper_FilterTitleWithSubtitle extends Zend_View_Helper_Abstract{
	public function FilterTitleWithSubtitle($text, $args, $isRich = false){
		if(
			is_current_url('/admin/items/show') || 
			!option('curatescape_auto_subtitle')
		) return $text;
		if(!isset($args['record'])) return $text;
		if(!$text) return null;
		$text = trim(strip_tags($text));
		if($isRich){
			if(option('curatescape_subtitle_styles')){
				$wrapperPre = '<span class="curatescape-article-header-styles">';
				$wrapperPost = '</span>';
			}
			// accessible and easily customized with css
			$separator = '<span class="curatescape-subtitle-separator">:&#32;</span>';
			$pre = (isset($wrapperPre) ? $wrapperPre : null).'<span class="curatescape-subtitle">'.$separator;
			$post = '</span>'.(isset($wrapperPost) ? $wrapperPost : null);
			return comboTitle($text, itm($args['record'], 'Subtitle'), $pre, $post);
		}
		return comboTitle($text, itm($args['record'], 'Subtitle'));
	}
}