<?php
// this only applies to files/show
class Curatescape_View_Helper_FilterFileMarkup extends Zend_View_Helper_Abstract{
	public function FilterFileMarkup($html, $args){
		if(
			( !str_starts_with(current_url(),'/files/show/') ) ||
			!option('curatescape_file_markup') ||
			!isset($args) ||
			is_admin_theme()
		) return $html;
		if(
			isset($args['wrapper_attributes']) &&
			isset($args['wrapper_attributes']['class']) &&
			isset($args['file'])
		){
			// use the wrapper class as a convenient proxy for file type
			$classString = $args['wrapper_attributes']['class'];
			$file = $args['file'];
			switch (true) {
				case str_contains($classString,'image'):
					$type = __('Image');
					$schemaURI = 'https://schema.org/ImageObject';
					$markup = '<a href="'.$file->getWebPath('original').'"><img src="'.$file->getWebPath('original').'"/></a>';
					break;
				case str_contains($classString,'video'):
					$type = __('Video');
					$schemaURI = 'https://schema.org/VideoObject';
					$markup = '<video data-browser="'.browserCategory().'" controls src="'.$file->getWebPath('original').'"></video>';
					break;
				case str_contains($classString,'audio'):
					$type = __('Audio');
					$schemaURI = 'https://schema.org/AudioObject';
					$markup = '<audio data-browser="'.browserCategory().'" controls src="'.$file->getWebPath('original').'"></audio>';
					break;
				case str_contains($classString,'pdf'):
					$type = __('Document');
					$schemaURI = 'https://schema.org/DigitalDocument';
					if(browserCategory() == 'chromium'){
						$markup = '<iframe src="'.$file->getWebPath('original').'"></iframe>';
					}elseif(browserCategory() == 'firefox'){
						$markup = '<object data="'.$file->getWebPath('original').'"></object>';
					}else{
						$markup = '<a download href="'.$file->getWebPath('original').'"><img src="'.$file->getWebPath('fullsize').'"/></a>';
					}
					break;
				default:
					return $html;
			}
			$html = null;
			$html .= '<figure aria-label="'.$type.': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-file-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
				$html .= $markup;
			$html .= '</figure>';
		}
		return $html;
	}
}