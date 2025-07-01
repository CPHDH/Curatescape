<?php
class Curatescape_View_Helper_FilterFilesForItem extends Zend_View_Helper_Abstract{
	public function FilterFilesForItem($html, $args)
	{
		if(
			!isset($args) || 
			!isset($args['item']) || 
			!is_array($args['item']['Files']) ||
			option('curatescape_gallery_style') == 'none'
		){
			return $html;
		}else{
			$html = null;
		}
		return $this->filesDisplayGallery($this->filesByType($args['item']['Files']), option('curatescape_gallery_style'));
	}
	
	public function filesDisplayGallery($files, $galleryType = 'gallery-inline-captions', $html = null)
	{
		if(!$files) return null;
		if($galleryType == 'gallery-slides'){ // @todo: https://github.com/omeka/Omeka/pull/1056
			$supportedFiles = array_merge($files['images'], $files['audio'], $files['video']);
			$html .= lightgallery($supportedFiles);
			$html .= filesOutputTable($files['other']);
		}elseif($galleryType == 'gallery-table'){
			$html .= filesOutputTable(array_merge($files['images'], $files['audio'], $files['video'], $files['other']), false);
		}else{
			// grid or inline
			if(option('curatescape_lightbox_docs')==1){
				$html .= filesOutputFigures($files['images'], $files['audio'], $files['video'], $files['other'], $galleryType);
			}else{
				$html .= filesOutputFigures($files['images'], $files['audio'], $files['video'], array(),$galleryType);
				$html .= filesOutputTable($files['other'], false);
			}
		}
		return '<div class="curatescape-files">'.$html.'</div>';
	}

	private function filesByType($files)
	{
		$byType = array(
			'images' => array(),
			'audio' => array(),
			'video' => array(),
			'other' => array(),
		);
		if(!$files) return $byType;
		foreach($files as $file){
			$type = $file->mime_type;
			switch($type){
				case stripos($type,'image') !== false:
					$byType['images'][] = $file;
					break;
				case stripos($type,'audio') !== false:
					$byType['audio'][] = $file;
					break;
				case stripos($type,'video') !== false:
					$byType['video'][] = $file;
					break;
				default:
					$byType['other'][] = $file;
					break;
			}
		}
		return $byType;
	}
}