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
	
	public function filesDisplayGallery($files, $galleryType = 'gallery-inline-captions', $html=null)
	{
		if(!$files) return null;
		if($galleryType == 'gallery-slides'){ // @todo: https://github.com/omeka/Omeka/pull/1056
			$supportedFiles = array_merge($files['images'], $files['audio'], $files['video']);
			$html .= lightgallery($supportedFiles);
			$html .= $this->filesOutputTable($files['other']);
		}elseif($galleryType == 'gallery-table'){
			$html .= $this->filesOutputTable(array_merge($files['images'], $files['audio'], $files['video'], $files['other']), false);
		}else{
			// grid or inline
			if(option('curatescape_lightbox_docs')==1){
				$html .= $this->filesOutputFigures($files['images'], $files['audio'], $files['video'], $files['other'], $galleryType);
			}else{
				$html .= $this->filesOutputFigures($files['images'], $files['audio'], $files['video'], array(),$galleryType);
				$html .= $this->filesOutputTable($files['other'], false);
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

	private function filesOutputFigures($images = array(), $audio = array(), $video = array(), $other = array(), $galleryType = 'gallery-inline-captions', $html = null)
	{
		if(!count($images) && !count($audio) && !count($video)) return null;
		$html .= '<div id="pswp-container" class="curatescape-image-gallery '.$galleryType.'">';
			$html .= $this->filesOutputImages($images);
			$html .= $this->filesOutputAudio($audio);
			$html .= $this->filesOutputVideo($video);
			$html .= $this->filesOutputDocument($other);
		$html .= '</div>';
		return $html;
	}

	private function imageLinkMarkup($file, $size='fullsize', $linkClass='gallery-image', $imgClass='item-file', $isLazy = true, $itemprop='associatedMedia', $html = null)
	{
		if(!$file) return null;
		$dimensions = $this->dimensions($file, $size);
		$fileHref = !option('link_to_file_metadata') ? record_image_url($file, $size) : $file->getProperty('permalink');
		$html .= '<a'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="pswp-item '.$linkClass.' '.$dimensions['orientation'].' file-'.$file->id.'" data-pswp-width="'.$dimensions['width'].'" data-pswp-height="'.$dimensions['height'].'" data-pswp-src="'.record_image_url($file, $size).'" data-pswp-type="image" data-pswp-fallbackmessage="'.__('Download').'">';
			$html .= '<img'.($isLazy ? ' loading="lazy"' : null).' class="'.$imgClass.'" src="'.record_image_url($file, 'fullsize').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" alt="'.htmlentities($file->getProperty('display_title')).'" />';
		$html .= '</a>';
		return $html;
	}
	
	private function mediaLinkMarkup($file, $filetype, $linkClass='gallery-image', $imgClass='fallback', $isLazy = true, $itemprop='associatedMedia', $html = null)
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
	
	private function documentLinkMarkup($file, $linkClass='gallery-image', $imgClass='document', $isLazy = true, $itemprop='associatedMedia', $html = null)
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
	
	private function filesOutputImages($files, $schemaURI = 'https://schema.org/ImageObject', $html = null)
	{
		if(!$files) return null;
		
		foreach($files as $file){
			$html .= '<figure aria-label="'.__('Image').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
				$html .= $this->imageLinkMarkup($file, 'fullsize');
				$html .= '<figcaption>'.$this->mediaCaptionText($file).'</figcaption>';
			$html .= '</figure>';
		}
		
		return $html;
	}

	private function filesOutputAudio($files, $schemaURI = 'https://schema.org/AudioObject', $html = null)
	{
		if(!$files) return null;
		
		foreach($files as $file){
			$html .= '<figure aria-label="'.__('Audio').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
				$html .= $this->mediaLinkMarkup($file, 'audio');
				$html .= '<figcaption>'.$this->mediaCaptionText($file).'</figcaption>';
			$html .= '</figure>';
		}
		
		return $html;
	}
	
	private function filesOutputDocument($files, $schemaURI = 'https://schema.org/DigitalDocument', $html = null)
	{
		if(!$files) return null;
		
		foreach($files as $file){
			$html .= '<figure aria-label="'.__('Document').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
				$html .= $this->documentLinkMarkup($file);
				$html .= '<figcaption>'.$this->mediaCaptionText($file).'</figcaption>';
			$html .= '</figure>';
		}
		
		return $html;
	}

	private function filesOutputVideo($files, $schemaURI = 'https://schema.org/VideoObject', $html = null)
	{
		if(!$files) return null;
		
		foreach($files as $file){
			$html .= '<figure aria-label="'.__('Video').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
				$html .= $this->mediaLinkMarkup($file, 'video');
				$html .= '<figcaption>'.$this->mediaCaptionText($file).'</figcaption>';
			$html .= '</figure>';
		}
		
		return $html;
	}

	private function filesOutputTable($files, $subhead = true, $html = null)
	{
		if(!$files) return null;
		$html .= '<div class="filestablecontainer"><table class="curatescape-additional-files">';
			$html .= ($subhead ? '<caption><h3>'.__('Documents').'</h3></caption>' : null);
			$html .= '<thead><th>'.__('File Details').'</th><th>'.__('Download').'</th></thead>';
			$html .= '<tbody>';
			foreach($files as $file){
				$meta = array(oxfordAmp(dc($file,'Creator',array('all'=>'true'))),dc($file, 'Source'),dc($file, 'Date'));
				$info = implode(' | ', array_filter($meta));
				$title = $this->mediaCaptionText($file);
				$type = '<span class="type">'.fileSubTypeString($file).' / '.$this->formatSize($file->size).'</span>';
				$download = '<span class="file-download"><a class="button" download href="'.record_image_url($file, 'original').'">'.__('Download').'</a>'.$type.'</span>';

				$html .= '<tr><td>'.$title.'</td><td>'.$download.'</td></tr>';
			}
			$html .= '</tbody>';
		$html .= '</table></div>';
		return $html;
	}
	
	private function formatSize($bytes){
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

	private function mediaCaptionText($file, $caption = array())
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

	private function dimensions($file, $size = 'fullsize')
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
}