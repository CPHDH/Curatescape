<?php
class Curatescape_View_Helper_FilterBodyTagAttributes extends Zend_View_Helper_Abstract{
	// if .curatescape-fix-[themeName] class is present, use theme-specific css fixes
	// admin gets a version class so stylesheets can shim markup differences in older core themes
	public function FilterBodyTagAttributes($attributes)
	{
		if(is_admin_theme()){
			if(version_compare(OMEKA_VERSION, '3.2.1', '<')){
				$attributes['class'] = $attributes['class'].' curatescape--is-omeka-version-lte-3-2';
			}
			return $attributes;
		}
		$attributes['class'] = $attributes['class'].
		(option('curatescape_theme_fixes') ? ' curatescape-fix-'.$this->themeClass() : '').
		' type-'.$this->typeClass();
		return $attributes;
	}

	private function formatClass($text)
	{
		if(!$text) return null;
		return strtolower(str_ireplace(' ', '-', $text));
	}

	private function typeClass()
	{
		if($record = get_current_record('item', false)){
			return $this->formatClass(metadata($record, 'Item Type Name', array('no_filter'=>true)));
		}
		return 'none';
	}

	private function themeClass()
	{
		if($themeDir = Theme::getCurrentThemeName()){
			return $this->formatClass($themeDir);
		}
		return 'unknown';
	}
}