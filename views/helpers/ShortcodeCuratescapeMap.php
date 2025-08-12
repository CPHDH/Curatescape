<?php
class Curatescape_View_Helper_ShortcodeCuratescapeMap extends Zend_View_Helper_Abstract{
	public function ShortcodeCuratescapeMap($args, $view, $html = null){
		$includeSubjectsSelect = isset($args['subjects']) && boolval($args['subjects']) ? true : false;
		$class = $includeSubjectsSelect ? 'shortcode-subjects' : 'shortcode-no-subjects';
		$src = (isset($args['src']) && !$includeSubjectsSelect) ? plainText($args['src']) : WEB_ROOT.'/items/browse?output=mobile-json';
		$figcaption = (isset($args['caption'])) ? plainText($args['caption']) : null;
		// note that the subjects arg overrides custom src; subjects avail only for default/global src 
		return get_view()->CuratescapeMap()->Multi($figcaption, true, $class, null, $src);
	}
}