<?php
class Curatescape_View_Helper_RssItems extends Zend_View_Helper_Abstract{
	public function RssItems(){
		$feedId = option('curatescape_rss') ? 'rss2' : 'rss-plus';
		$location = WEB_ROOT.'/items/browse?output='.$feedId;
		$feed = new Zend_Feed_Writer_Feed;
		$feed->setTitle(option('site_title'));
		$feed->setLink(WEB_ROOT.'/');
		$feed->setFeedLink($location, 'atom');
		$feed->addAuthor(array(
			'name' => option('site_title'),
			'uri' => WEB_ROOT,
		));
		$feed->setDateModified(time());
		$feed->addHub('http://pubsubhubbub.appspot.com/');
		
		foreach( loop( 'items' ) as $item )
		{
			$url = WEB_ROOT.'/items/show/'.$item->id;
			$title = $this->rssTitleCombo($item);
			$author = $this->rssAuthors(dc( $item, 'Creator', array('all'=>true, 'no_filter'=>true)));
			$content = $this->rssArticle($item, $url);
			// entry output
			$entry = $feed->createEntry();
			$entry->setTitle($title);
			$entry->setLink($url);
			$entry->addAuthor(array('name' => $author ));
			$entry->setDateModified(strtotime($item->modified));
			$entry->setDateCreated(strtotime($item->added));
			$entry->setDescription($content);
			$feed->addEntry($entry);
		}
		echo get_view()->Cache()->Config(600); // 5 minutes
		echo $feed->export('atom');
	}
	
	private function rssMainText($item = null){
		if(!$item) return __('[Text Unavailable]');
		$dc_desc = dc($item, 'Description') ? normalizeTextBlocks(dc($item, 'Description')) : __('[Text Unavailable]');
		$story = itm($item, 'Story') ? normalizeTextBlocks(itm($item, 'Story')) : $dc_desc;
		return $story;
	}
	
	private function rssLede($item = null){
		if(!$item) return null;
		return itm($item, 'Lede') ? normalizeTextBlocks('<p><strong><em>'.itm($item, 'Lede').'</em></strong></p>') : null;
	}
	
	private function rssTitleCombo($item = null){
		if(!$item) return __('[Untitled]');
		$title = dc( $item, 'Title' ) ? dc( $item, 'Title', array('no_filter'=>true) ) : __('[Untitled]');
		$title .= itm($item, 'Subtitle') ? ': '.itm($item, 'Subtitle') : null;
		return $title;
	}
	
	private function rssAuthors($authors){
		if(count($authors)>0){
			$all_authors=array();
			foreach($authors as $author){
				$all_authors[]=$author;
			}
			$author=oxfordAmp($all_authors);
		}else{
			$author=option('site_title');
		}
		return strip_tags($author);
	}
	
	private function rssMediaInfo($item){
		$files=array();
		$images=array();
		$audio=array();
		$video=array();
		$statsArray=array();
		$media_info=array();
		foreach( $item->Files as $file ){
			$path = $file->getWebPath( 'original' );
			$mimetype = metadata( $file, 'MIME Type' );
			$filedata = array(
				'id' => $file->id,
				'mime-type' => $mimetype,
			);
			if( $ftitle = dc($file, 'Title') ) {
				$filedata['title'] = strip_formatting( $ftitle );
			}
			if( $description = dc($file, 'Description') ) {
				$filedata['description'] = $description;
			}
			if( $file->hasThumbnail() ) {
				$filedata['thumbnail'] = $file->getWebPath( 'square_thumbnail' );
				$filedata['fullsize'] = $file->getWebPath( 'fullsize' );
			}
			if( strpos($filedata['mime-type'], 'image/' )===0){
				$images[]=$filedata;
			}
			if( strpos($filedata['mime-type'], 'audio/' )===0){
				$audio[]=$filedata;
			}
			if( strpos($filedata['mime-type'], 'video/' )===0){
				$video[]=$filedata;
			}
		}
		if( count($images) > 0 ){
			$num=count($images);
			$heroSrc=isset($images[0]['fullsize']) ? $images[0]['fullsize'] : null;
			$heroTitle=isset($images[0]['title']) ? $images[0]['title'] : __('[Untitled]');
			if($heroSrc){
				$media_info['hero_img']['src']=$heroSrc;
				$media_info['hero_img']['title']=$heroTitle;
				$media_info['hero_img']['link']='<img alt="'.$heroTitle.'" src="'.$heroSrc.'"/>';
			}
			if($num > 1){
				$statsArray[]=$num.' '.__(plural('image', 'images', $num));
			}
		}
		if( count($audio) >0 ){
			$num=count($audio);
			$statsArray[]=$num.' '.__(plural('audio file', 'audio files', $num));
		}
		if( count($video) >0 ){
			$num=count($video);
			$statsArray[]=$num.' '.__(plural('video', 'videos', $num));
		}
		$item_file_stats= count($statsArray) > 0 ? __(' (including %s)', strip_tags(oxfordAmp($statsArray))) : null;
		
		$media_info['stats']=$item_file_stats;
		return $media_info;
	}
	
	private function rssReadMore($url, $parenthetical=null){
		if(!$url) return null; 
		$text = str_replace('  ', ' ', __('For more %s view the original article', $parenthetical));
		return '<p><em><strong><a href="'.$url.'">'.$text.'</a></strong></em></p>';
	}
	
	private function rssArticle($item, $url, $text=null){
		if(!$item || !$url) return __('[Text Unavailable]');
		$rss_media_info = $this->rssMediaInfo($item);
		$stats = isset($rss_media_info['stats']) ? $rss_media_info['stats'] : null;
		$text .= $this->rssLede($item);
		$text .= isset($rss_media_info['hero_img']) && isset($rss_media_info['hero_img']['src']) ? '<img src="'.$rss_media_info['hero_img']['src'].'" alt="'.$rss_media_info['hero_img']['title'].'" /><br/>' : null;
		$text .= $this->rssMainText($item);
		$text .= $this->rssReadMore($url, $stats);
		return $text;
	}
}