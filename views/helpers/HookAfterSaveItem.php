<?php
class Curatescape_View_Helper_HookAfterSaveItem extends Zend_View_Helper_Abstract{
	public function HookAfterSaveItem($args)
	{
		if(
			!isset($args['post']) || 
			!isset($args['post']['Elements']) || 
			!isset($args['record']) 
		) return;
		$rules = $this->filterRules();
		$warnings = $this->formatWarningRules();
		$elementsToFilter=array_column($rules, 'elements');
		$elementsToWarn=array_column($warnings, 'elements');
		$record = $args['record'];
		$db = get_db();
		$elementTable = $db->getTable('Element');
		$warningsNotify = array();
		foreach($args['post']['Elements'] as $id=>$elementTexts){
			foreach($elementTexts as $i=>$elementText){
				$elementObj = get_record_by_id('Element',$id);
				$setName = $elementObj->set_name;
				$elementName = $elementObj->name;
				if(in_array($elementName, array_merge(...$elementsToFilter)) && option('curatescape_filter_text')==1){
					if($elementText['text'] && $elementText['html'] == '1'){
						$newElText = $elementTable->findByElementSetNameAndElementName($setName, $elementName);
						$allowedTags = $this->allowedTagsByElementName($elementName, $rules);
						$filteredText = $this->customFilter($elementText['text'], $allowedTags);
						$record->deleteElementTextsByElementId(array($newElText->id));
						$record->addTextForElement($newElText, $filteredText, $elementText['html']);
					}
				}
				if(in_array($elementName, array_merge(...$elementsToWarn)) && option('curatescape_format_warnings')==1){
					$avoid = array_column($warnings, 'avoid');
					if($w = $this->getWarningsByTextAndElementName($elementText['text'], $elementName, $warnings)){
						$warningsNotify[$elementName] = $elementName.' '.__('Warning').': '.$w;
					}
				}
				
			}
		}
		// save filtered element texts
		$record->saveElementTexts();
		// show format notifications
		if(count($warningsNotify)){
			$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
			foreach($warningsNotify as $warningNotification){
				$flash->addMessage($warningNotification, 'curatescape-warning');
			}
		}
		// cache
		$cache = get_view()->CuratescapeCache();
		// clear and rebuild json files
		$cache->CacheBustManual(_JSON_ITEMS_FILE_, true);
		$cache->CacheBustManual(_JSON_TOURS_FILE_, true);
		// clear and rebuild dashboard files
		$cache->CacheBustManual(_HTML_DASHBOARD_FILE_STATS_, true);
		$cache->CacheBustManual(_HTML_DASHBOARD_CONTENT_AUDIT_, true);
		// dispatch background jobs (with fallback to synchronous)
		// $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
		try {
			$jobDispatcher = Zend_Registry::get('bootstrap')->getResource('jobs');
			$jobDispatcher->sendLongRunning('Curatescape_Job_RefreshJsonCache');
			$jobDispatcher->sendLongRunning('Curatescape_Job_RefreshDashboardCache');
			// $flash->addMessage(__('The cache is being reset.'), 'success');
		} catch (Throwable $e) {
			// error_log('Curatescape sendLongRunning failed: ' . $e->getMessage());
			try {
				$jobDispatcher = Zend_Registry::get('job_dispatcher');
				$jobDispatcher->send('Curatescape_Job_RefreshJsonCache');
				$jobDispatcher->send('Curatescape_Job_RefreshDashboardCache');
				// $flash->addMessage(__('The cache has been reset.'), 'success');
			} catch (Throwable $e) {
				// error_log('Curatescape send failed: ' . $e->getMessage());
				get_view()->JsonItem()->refreshJsonCache($cache);
				get_view()->JsonTour()->refreshJsonCache($cache);
				get_view()->HookAdminDashboard()->refreshDashboardWidgets($cache);
				// $flash->addMessage(__('Improve the time it takes to save items by configuring the PHP background path.'), 'curatescape-warning');
			}
		}
	}

	private function filterRules()
	{
		return array(
			array(
				'rule'=>'none',
				'elements'=>array('Subtitle', 'Street Address'),
				'allowed'=>null
				),
			array(
				'rule'=>'link',
				'elements'=>array('Sponsor','Official Website'),
				'allowed'=>'<a>'
				),
			array(
				'rule'=>'formatting',
				'elements'=>array('Lede','Related Resources', 'Access Information'),
				'allowed'=>'<b><i><em><strong><cite><a>'
				),
			array(
				'rule'=>'structure',
				'elements'=>array('Factoid'),
				'allowed'=>'<b><i><em><strong><br><cite><blockquote><a><ul><ol><li>'
			),
			array(
				'rule'=>'full',
				'elements'=>array('Story'),
				'allowed'=>'<b><i><em><strong><br><cite><blockquote><a><ul><ol><li><h2><h3><h4><h5>'
				),
			);
	}

	private function formatWarningRules(){
		return array(
		array(
			'warning'=>__('Use the "Add Input" button to create separate fields for each Subject. Avoid using HTML and new lines. Warnings may include false positives and can be disabled in %s plugin settings.', _PLUGIN_NAME_),
			'elements'=>array('Subject'),
			'avoid'=>array(';',',','/>', '/ >','<br', "\n")
			),
		array(
			'warning'=>__('Use the "Add Input" button to create separate fields for each Creator. Avoid using HTML and new lines. Warnings may include false positives and can be disabled in %s plugin settings.', _PLUGIN_NAME_),
			'elements'=>array('Creator'),
			'avoid'=>array(';',',','/>', '/ >','<br', "\n")
			),
		array(
			'warning'=>__('Use the "Add Input" button to create separate fields for each Related Resource. Avoid using new lines. Warnings may include false positives and can be disabled in %s plugin settings.', _PLUGIN_NAME_),
			'elements'=>array('Related Resources'),
			'avoid'=>array('<li>','<br',"\n")
			),
		);
	}

	private function getWarningsByElementName($name, $warnings)
	{
		if(!isset($name) || !count($warnings)) return null;
		if(!isset($warnings[0]['warning'])) return null;
		foreach($warnings as $warningSet){
			$els = $warningSet['elements'];
			if(array_search($name, $els) !== false){
				return isset($warningSet['warning']) ? $warningSet['warning'] : null;
			}
		}
		return null;
	}

	private function getWarningsByTextAndElementName($text, $name, $warnings)
	{
		if(!isset($text) || !isset($name) || !count($warnings)) return null;
		if(!isset($warnings[0]['warning'])) return null;
		foreach($warnings as $warningSet){
			if(array_search($name, $warningSet['elements']) !== false){
				foreach($warningSet['avoid'] as $string){
					if(str_contains($text, $string)){
						return isset($warningSet['warning']) ? $warningSet['warning'] : null;
					}
				}
			}
		}
		return null;
	}

	private function allowedTagsByElementName($name, $rules)
	{
		if(!isset($name) || !count($rules)) return null;
		if(!isset($rules[0]['rule'])) return null;
		foreach($rules as $ruleSet){
			$els = $ruleSet['elements'];
			if(array_search($name, $els) !== false){
				return $ruleSet['allowed'];
			}
		}
		return null;
	}

	/* 
	clean up "html" generated by desktop word processors
	strip tags except $allowed_tags whitelist
	source: https://gist.github.com/dave1010/674071
	*/
	private function customFilter($text, $allowed_tags = '<b><i><em><strong><br><cite><blockquote><a><ul><ol><li>')
	{
		if(!isset($text)) return null;
		mb_regex_encoding('UTF-8');
		$search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
		$replace = array('\'', '\'', '"', '"', '-');
		$text = preg_replace($search, $replace, $text);
		$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		if(mb_stripos($text, '/*') !== FALSE){
			$text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
		}
		$text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
		$text = strip_tags($text, $allowed_tags);
		$text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
		$search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
		$replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
		$text = preg_replace($search, $replace, $text);
		$num_matches = preg_match_all("/\<!--/u", $text, $matches);
		if($num_matches){
			$text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
		}
		return $text;
	}
}