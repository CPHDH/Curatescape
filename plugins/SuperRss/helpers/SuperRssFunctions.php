<?php
/*
** Format: Oxford Comma
*/
function srss_oxfordComma($items=null) {
	$count = count($items);
	if($count === 0) {
		return null;
	} else if($count === 1) {
			return $items[0];
		} else {
		return implode(' , ', array_slice($items, 0, $count - 1)) . ' and ' . $items[$count - 1];
	}
}

/*
** Format: BR to P
*/
function srss_br2p($data) {
	$data = preg_replace('#(?:<br\s*/?>\s*?){2,}#', '</p><p>', $data);
	return "<p>$data</p>";
}

/*
** Format: Site URL
*/
function srss_get_page_url() {
  $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
  $url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
  $url .= $_SERVER["REQUEST_URI"];
  return $url;
}

/*
** Format: Domain Name
*/
function srss_get_host($url) {
	$parsed = parse_url(trim($url));
	return trim($parsed[host] ? $parsed[host] : array_shift(explode('/', $parsed[path], 2)));
}

/*
** Item Footer
*/
function srss_footer(){
	$footer=null;

	// App Store Links
	if(get_option('srss_include_applink_footer')==1){
		$ios=get_option('srss_ios_id');
		$adk=get_option('srss_android_id');
		if( $ios || $adk ){
			$app_store=array();
			isset($adk) ? $app_store[]='<a href="http://play.google.com/store/apps/details?id='.$adk.'">Android</a>' : null;
			isset($ios) ? $app_store[]='<a href="https://itunes.apple.com/us/app/'.$ios.'">iPhone</a>' : null;
			$footer.='<small>'.__('Download the '.option('site_title').' app for %s', implode($app_store, ' and ')).'</small>';
		}
	}

	// Social Media Links
	if(get_option('srss_include_social_footer')==1){
		$fb=get_option('srss_facebook_link') ? get_option('srss_facebook_link') : null;
		$tw=get_option('srss_twitter_user') ? get_option('srss_twitter_user') : null;
		$yt= get_option('srss_youtube_user') ? get_option('srss_youtube_user') : null;

		if( $fb || $tw || $yt ){
			$social=array();
			isset($fb) ? $social[]='<a href="'.$fb.'">Facebook</a>' : null;
			isset($tw) ? $social[]='<a href="https://twitter.com/'.$tw.'">Twitter</a>' : null;
			isset($yt) ? $social[]='<a href="http://www.youtube.com/user/'.$yt.'">Youtube</a>' : null;
			$footer.='<br><small>'.__('Find us on %s', srss_oxfordComma($social)).'</small>';
		}
	}

	return $footer;
}

/*
** Primary Content
*/
function srss_the_text($item='item',$options=array()){
	$lede = element_exists('Item Type Metadata','Lede') ? '<p><strong><em>'.metadata($item,array('Item Type Metadata', 'Lede'),$options).'</em></strong></p>' : null;
	$dc_desc = metadata($item, array('Dublin Core', 'Description'),$options) ? metadata($item, array('Dublin Core', 'Description'),$options) : 'No Content Available';
	$story = element_exists('Item Type Metadata','Story') ? metadata($item,array('Item Type Metadata', 'Story'),$options) : $dc_desc;
	return $lede.srss_br2p($story);
}

/*
** Subtitle
*/
function srss_the_subtitle($item=null,$pre=null,$post=null){
	$dc_title2 = metadata($item, array('Dublin Core', 'Title'), array('index'=>1));
	$subtitle=element_exists('Item Type Metadata','Subtitle') ? metadata($item,array('Item Type Metadata', 'Subtitle')) : null;
	return  $subtitle ? $pre.$subtitle.$post : ($dc_title2!=='[Untitled]' ? $pre.$dc_title2.$post : null);
}

/*
** Authors
*/
function srss_authors($authors){
	if(count($authors)>0){
		$all_authors=array();
		foreach($authors as $author){
			$all_authors[]=$author;
		}
		$author=srss_oxfordComma($all_authors);
	}else{
		$author='The '.option('site_title').' Team';
	}
	return $author;
}

/*
** Media Info
*/
function srss_media_info($item){
	if(get_option('srss_include_mediastats_footer')==1){
		$files=array();
		$images=array();
		$audio=array();
		$video=array();
		foreach( $item->Files as $file )
		{
			$path = $file->getWebPath( 'original' );
			$mimetype = metadata( $file, 'MIME Type' );
			$filedata = array(
				'id'        => $file->id,
				'mime-type' => $mimetype,
			);
			if( $ftitle = metadata( $file, array( 'Dublin Core', 'Title' ) ) ) {
				$filedata['title'] = strip_formatting( $ftitle );
			}
			if( $description = metadata( $file, array( 'Dublin Core', 'Description' ) ) ) {
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
		$fstr=array();
		$hero=null;
		if( count($images) >0 ){
			$num=count($images);
			$fullsize=isset($images[0]['fullsize']) ? $images[0]['fullsize'] : null;
			$title=isset($images[0]['title']) ? $images[0]['title'] : null;
			$hero=array(
				'src'=>$fullsize,
				'title'=>$title,
				'link'=>'<img alt="'.$title.'" src="'.$fullsize.'"/>'
			);
			if($num > 1){
				// greater than one since we already include the first one
				$fstr[]=$num.' '.($num > 1 ? __('images') : __('image') );
			}

		}
		if( count($audio) >0 ){
			$num=count($audio);
			$fstr[]=$num.' '.($num > 1 ? __('sound clips') : __('sound clip') );
		}
		if( count($video) >0 ){
			$num=count($video);
			$fstr[]=$num.' '.($num > 1 ? __('videos') : __('video') );
		}
		$item_file_stats= count($fstr) > 0 ? __(' (including %s)', srss_oxfordComma($fstr)) : null;
		$media_info=array();
		$media_info['stats_link']=$item_file_stats;
		$media_info['hero_img']['src']=$hero['src'] ? $hero['src'] : null;
		$media_info['hero_img']['title']=$hero['title'] ? $hero['title'] : null;
		$media_info['hero_img']['link']=$hero['link'] ? $hero['link'] : null;
		return $media_info;
	}
}
