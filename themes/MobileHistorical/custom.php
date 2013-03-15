<?php 
// Use this file to define customized helper functions, filters or 'hacks' defined
// specifically for use in your Omeka theme. Note that helper functions that are
// designed for portability across themes should be grouped into a plugin whenever
// possible.

// mobile device detection 
require_once('mobile_device_detect.php');

/** == Global navigation == **/

function mh_global_nav($context)
{
		if($context == 'desktop'){
			return public_nav_main(array('Home' => uri('/'), 'Stories' => uri('items'), 'Tours' => uri('/tour-builder/tours/browse/')));
		}	
		if($context == 'mobile_foot'){
			$html = '<ul class="navigation">';
    		$html .= '<li><a href="'.uri('/').'"';
    		if (get_theme_option('stealth_mode')==1){
    				$html .= 'target="_self"';
    				}
    				else{
    				$html .= 'data-direction="reverse"';
    				}
    		$html .= '>Home</a></li>';
    		$html .= '<li><a href="'.uri('/items/').'"  >Browse</a></li>';
    		$html .= '<li><a href="'.uri('/tour-builder/tours/browse/').'"  >Tours</a></li>';
    		$html .= '</ul>';
    		
    		return $html;
		}			
		if($context == 'mobile_head'){
    			$html = '<ul class="navigation">';
    			$html .= '<li><a href="'.uri('/').'" data-icon="home"';
    			if (get_theme_option('stealth_mode')==1){
    				$html .= 'target="_self"';
    				}
    				else{
    				$html .= 'data-direction="reverse"';
    				}
    			$html .='>Home</a></li>';
    			$html .= '<li><a href="'.uri('/items/').'" data-icon="grid" >Browse</a></li>';
    			$html .= '<li><a href="'.uri('/tour-builder/tours/browse/').'" data-icon="star" >Tours</a></li>';
    			$html .= '</ul>';
    			
    			return $html;
    	}	
}

/** -- GET THE TOUR LIST -- **/
function display_tour_items($num = 15)

//display_tour_items($items)

{
	
    // Get the database.
    $db = get_db();

    // Get the Tour table.
    $table = $db->getTable('Tour');

    // Build the select query.
    $select = $table->getSelect();
    $select->from(array(), 'RAND() as rand');
    $select->where('public = 1');
    $select->order('title ASC');
    $select->limit($num);    
	 
    // Fetch some items with our select.
    $items = $table->fetchObjects($select);
    
   // echo count($items);
    $tot = count($items);
   
    echo "<ul>";
    
	for ($i = 0; $i < $tot; $i++) {
    	echo "<li><a href='" . WEB_ROOT . "/tour-builder/tours/show/id/". $items[$i]['id']."'>" . $items[$i]['title'] . "</a></li>";
	}
	echo "</ul>";
	
	return $items;
}

function toursList() {

// Start with an empty array of tours
$all_tours_metadata = array();

// Loop through all the tours
while( loop_tours() ) {
   $tour = get_current_tour();

   $tour_metadata = array( 
      'id'     => tour( 'id' ),
      'title'  => tour( 'title' ),
   );

   array_push( $all_tours_metadata, $tour_metadata );
}

$metadata = array(
   'tours'  => $all_tours_metadata,
);

// Encode and send
echo Zend_Json_Encoder::encode( $metadata );

}


function display_random_featured_item_CH($withImage=false)
 {
    $featuredItem = random_featured_item($withImage);
 	$html = '<h2 class="col-heading">Featured Story</h2>';
 	if ($featuredItem) {
 	    $itemTitle = item('Dublin Core', 'Title', array(), $featuredItem);
        
 	  
 	   if (item_has_thumbnail($featuredItem)) {
 	       $html .= '<div class="thumb">' . link_to_item(item_square_thumbnail(array(), 0, $featuredItem), array('class'=>'image'), 'show', $featuredItem) . '</div>';
 	   }
 	   
 	   $html .= '<h3>' . link_to_item($itemTitle, array(), 'show', $featuredItem) . '</h3>';
 	   
 	   // Grab the 1st Dublin Core description field (first 150 characters)
 	   if ($itemDescription = item('Dublin Core', 'Description', array('snippet'=>150), $featuredItem)) {
 	       $html .= '<p class="item-description">' . $itemDescription . '</p><p class="view-items-link">'. link_to_item('More about '.$itemTitle, array(), 'show', $featuredItem) .'</p>';
       }
 	} else {
 	   $html .= '<p>No featured items are available.</p>';
 	}

     return $html;
 }
 
function display_recent_item_CH($withImage=false)
 {
    $recentItem = set_items_for_loop(recent_items(1));
 	$html = '<h2>Recently Added</h2>';
 	if ($recentItem) {
 	    $itemTitle = item('Dublin Core', 'Title', array(), $recentItem);
        
 	   $html .= '<h3>' . link_to_item($itemTitle, array(), 'show', $recentItem) . '</h3>';
 	   if (item_has_thumbnail($recentItem)) {
 	       $html .= '<div class="thumb">' . link_to_item(item_square_thumbnail(array(), 0, $recentItem), array('class'=>'image'), 'show', $recentItem) . '</div>';
 	   }
 	   // Grab the 1st Dublin Core description field (first 150 characters)
 	   if ($itemDescription = item('Dublin Core', 'Description', array('snippet'=>150), $recentItem)) {
 	       $html .= '<p>' . $itemDescription . '</p>';
       }
 	} else {
 	   $html .= '<p>No featured items are available.</p>';
 	}
     return $html;
 }
 
/*mobile simple search */
function mobile_simple_search($buttonText = null, $formProperties=array('id'=>'simple-search'), $uri = null) 
{ 
    // Always post the 'items/browse' page by default (though can be overridden).
    if (!$uri) {
        $uri = apply_filters('simple_search_default_uri', uri('items/browse'));
    }
    
    $searchQuery = array_key_exists('search', $_GET) ? $_GET['search'] : '';    
    $formProperties['action'] = $uri;
    $formProperties['method'] = 'get';
    $html  = '<form ' . _tag_attributes($formProperties) . '>' . "\n";
    $html .= '<fieldset>' . "\n\n";
    $html .= __v()->formText('search', $searchQuery, array('name'=>'textinput','class'=>'textinput','placeholder'=>'search','data-type'=>'search'));
    $html .= __v()->formHidden('submit_search', $buttonText);
    $html .= '</fieldset>' . "\n\n";
    
    // add hidden fields for the get parameters passed in uri
    $parsedUri = parse_url($uri);
    if (array_key_exists('query', $parsedUri)) {
        parse_str($parsedUri['query'], $getParams);
        foreach($getParams as $getParamName => $getParamValue) {    
            $html .= __v()->formHidden($getParamName, $getParamValue); 
        }
    }
    
    $html .= '</form>';
    return $html;
}

function mh_about($text=null){
    if (!$text) {
        // If the 'About Text' option has a value, use it. Otherwise, use default text  
        $text = 
        get_theme_option('about') ?
        get_theme_option('about') : 
        'This site is powered by Omeka + MobileHistorical, a humanities-centered web and mobile framework available for both Android and iOS devices.';
    }
    return $text; 
}

function mh_display_app_links_home(){
	echo '<a href="'.get_theme_option('ios_link').'"><img src="'.img('app-store-badge.gif').'" alt="Available at the iPhone App Store" width="96" height="32" border="0" /></a> <a href="'.get_theme_option('android_link').'"><img src="'.img('btn-android.png').'" width="96" height="32" border="0" /></a>';
}

function mh_display_app_links_mobile(){
	echo '
	<a href="'.get_theme_option('android_link').'"><img src="'.img('btn-android.png').'"/></a>
	<a href="'.get_theme_option('ios_link').'"><img src="'.img('app-store-badge.gif').'"/></a>
	';
}

function mh_app_download_links_home($links=0){
    if (!$links) {
        // If the 'Enable App Links' option is checked, display links. Otherwise, use default text  
        $links = 
        get_theme_option('Enable App Links') ?
        mh_display_app_links_home() : 
        '<p class="h-coming-soon">Available soon as a native smartphone app.</p>';
    }
    return $links; 

}

function mh_app_download_links_mobile($links=0){
    if (!$links) {
        // If the 'Enable App Links' option is checked, display links. Otherwise, use default text  
        $links = 
        get_theme_option('Enable App Links') ?
        mh_display_app_links_mobile() : 
        '<p class="m-coming-soon">Coming soon.</p>';
    }
    return $links; 

}

function mh_google_analytics($webPropertyID=null){
	$webPropertyID= get_theme_option('google_analytics');
	if ($webPropertyID!=null){
	echo "<script type=\"text/javascript\">
	
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '".$webPropertyID."']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>";
	}
}

/* DISQUSS COMMENTS */
function mh_disquss_comments(){
	$shortname=get_theme_option('comments_id');
	if ($shortname){
	?>
    <div id="disqus_thread"></div>
    <script type="text/javascript">
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = '<?php echo $shortname;?>'; // required: replace example with your forum shortname

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
	<?php
	}
}


function link_to_item_edit()
{
$current = Omeka_Context::getInstance()->getCurrentUser();
        if ($current->role == 'super') {
                echo '<a class="edit" href="'. html_escape(uri('admin/items/edit/')).item('ID').'">Edit this item...</a>';
                }
        elseif($current->role == 'admin'){
                echo '<a class="edit" href="'. html_escape(uri('admin/items/edit/')).item('ID').'">Edit this item...</a>';
                }
} 

function mh_poster_url()
{
    $poster = get_theme_option('poster');
	
	$posterimg = $poster ? WEB_ROOT.'/archive/theme_uploads/'.$poster : img('poster.jpg');
	
	return $posterimg;
}

function mh_stealth_logo_url()
{
    $stealth_logo = get_theme_option('stealth_logo');
	
	$logo_img = $stealth_logo ? WEB_ROOT.'/archive/theme_uploads/'.$stealth_logo : img('logo.png');
	
	return $logo_img;
}

function mh_lg_logo_url()
{
    $lg_logo = get_theme_option('lg_logo');
	
	$logo_img = $lg_logo ? WEB_ROOT.'/archive/theme_uploads/'.$lg_logo : img('hm-logo.png');
	
	return $logo_img;
}

function mh_med_logo_url()
{
    $med_logo = get_theme_option('med_logo');
	
	$logo_img = $med_logo ? WEB_ROOT.'/archive/theme_uploads/'.$med_logo : img('lv-logo.png');
	
	return $logo_img;
}

function mh_nav_logo_url()
{
    $nav_logo = get_theme_option('tiny_logo');
	
	$logo_img = $nav_logo ? WEB_ROOT.'/archive/theme_uploads/'.$nav_logo : img('icn-sm.png');
	
	return $logo_img;
}

function mh_tour_logo_url()
{
    $tour_logo = get_theme_option('tour_logo');
	
	$logo_img = $tour_logo ? WEB_ROOT.'/archive/theme_uploads/'.$tour_logo : img('ttl-take-a-tour.png');
	
	return $logo_img;
}

function mh_follow_logo_url()
{
    $follow_logo = get_theme_option('follow_logo');
	
	$logo_img = $follow_logo ? WEB_ROOT.'/archive/theme_uploads/'.$follow_logo : img('btn-conversation.png');
	
	return $logo_img;
}

function mh_map_pin_logo_url()
{
    $map_pin_logo = get_theme_option('map_pin');
	
	$logo_img = $map_pin_logo ? WEB_ROOT.'/archive/theme_uploads/'.$map_pin_logo : img('icn.png');
	
	return $logo_img;
}

function mh_apple_icon_logo_url()
{
    $apple_icon_logo = get_theme_option('apple_icon');
	
	$logo_img = $apple_icon_logo ? WEB_ROOT.'/archive/theme_uploads/'.$apple_icon_logo : img('Icon.png');
	
	return $logo_img;
}

function mh_bg_home_logo_url()
{
    $bg_home_logo = get_theme_option('bg_home');
	
	$logo_img = $bg_home_logo ? WEB_ROOT.'/archive/theme_uploads/'.$bg_home_logo : img('bg-home.jpg');
	
	return $logo_img;
}

function mh_bg_lv_logo_url()
{
    $bg_lv_logo = get_theme_option('bg_lv');
	
	$logo_img = $bg_lv_logo ? WEB_ROOT.'/archive/theme_uploads/'.$bg_lv_logo : img('lv-bg.png');
	
	return $logo_img;
}

function mh_link_color()
{
    $color = get_theme_option('link_color');
	
	if ( ($color) && (preg_match('/^#[a-f0-9]{6}$/i', $color)) ){
		return $color;
	}
}

/*TWITTER*/

function linkmytweet($text){
	$text= preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);
	$text= preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);
	$text= preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $text);
	$text= preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $text);
	echo $text;
}


function twi_get(){

	 $twitte = file_get_contents("http://search.twitter.com/search.json?q=".get_theme_option('twitter_hashtag')."%20OR%20%23".get_theme_option('twitter_hashtag')."&rpp=4");
	 $data = json_decode($twitte);
	 $o_text = "";
	 foreach($data->results as $item)
	 {
	 	$date = date('m/d/o',strtotime($item->created_at));
	 	echo "<li>";
	    $o_text = "<div class='date'><a href='https://twitter.com/#!/".get_theme_option('twitter_hashtag')."/status/". $item->id_str . "' target='_blank'>" .$date."</a> @". $item->from_user .": "."</div>".$item->text;
		linkmytweet($o_text);
	
	   	//echo $o_text;
		   echo "</li>";
		 } 
 }
 
 function mh_follow_the_conversation(){
	 
	 $html .= '<a href="http://twitter.com/#!/search/'.(get_theme_option('twitter_hashtag') ? get_theme_option('twitter_hashtag') : get_theme_option('twitter_username')).'"><img src="'. mh_follow_logo_url().'" alt="Join the Conversation on Twitter" title="Join the Conversation on Twitter" border="0" /></a>';
	 return $html;
 }
 
 
 
/*
** CC License chooser support 
*/ 
function mh_cc_for_item($item, $width = 0) {      
if (function_exists('cc_for_item')){
		$divId = "item-license-{$item->id}";
		$cc = current(get_license_for_item($item->id));
		if ($cc) {
		     
		//begin HTML ?>
				<h3>License</h3>
				
				<div id="<?php echo $divId; ?>" style="display: block;">
					<div id="cc_license"><!-- Creative Commonts License -->
						<a href="<?php echo $cc->cc_uri; ?>" rel="license" class="cc_js_a"><img width="88" height="31" border="0" class="cc_js_cc-button" src="<?php echo $cc->cc_img; ?>" alt="Creative Commons License" style="margin:5px 0"/></a>
					<div class="cc_info">This work is licensed under a <a href="<?php echo $cc->cc_uri; ?>" rel="license"><?php echo $cc->cc_name; ?></a>.
					</div>
				</div>
				</div>
		<?php //end HTML
			
		}
	}
} 
 
 
 
 /*UNWANTED CHARACTER STRIPPING */
 function normalize_special_characters( $str ) 
{ 
    # Quotes cleanup 
    $str = str_replace( chr(ord("`")), "'", $str );        # ` 
    $str = str_replace( chr(ord("´")), "'", $str );        # ´ 
    $str = str_replace( chr(ord("?")), ",", $str );        # ? 
    $str = str_replace( chr(ord("`")), "'", $str );        # ` 
    $str = str_replace( chr(ord("´")), "'", $str );        # ´ 
    $str = str_replace( chr(ord("?")), "\"", $str );        # ? 
    $str = str_replace( chr(ord("?")), "\"", $str );        # ? 
    $str = str_replace( chr(ord("´")), "'", $str );        # ´ 

    $unwanted_array = array(    '?'=>'S', '?'=>'s', '?'=>'Z', '?'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 
                                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 
                                'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 
                                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 
                                'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y','&amp;'=>'&','height'=>'h&#101;ight' ); 
    $str = strtr( $str, $unwanted_array ); 
    
    #For reasons yet unknown, only some servers may require an additional $unwanted_array item: 'height'=>'h&#101;ight'

    # Bullets, dashes, and trademarks 
    $str = str_replace( chr(149), "&#8226;", $str );    # bullet ? 
    $str = str_replace( chr(150), "&ndash;", $str );    # en dash 
    $str = str_replace( chr(151), "&mdash;", $str );    # em dash 
    $str = str_replace( chr(153), "&#8482;", $str );    # trademark 
    $str = str_replace( chr(169), "&copy;", $str );    # copyright mark 
    $str = str_replace( chr(174), "&reg;", $str );        # registration mark 
    $str = str_replace( "&quot;", "\"", $str );        # "
    $str = str_replace( "&apos;", "\'", $str );        # '
    $str = str_replace( "&#039;", "'", $str );        # '

    return $str; 
} 

/**
* Get Related Tours
* not yet functional
* based on Exhibit Builder related exhibits function


function mh_link_to_related_tours() 
{
    $tourTable = get_db()->getTable('Tour');
    $select = $tourTable->getSelect()->where("public = 1")->where("title != '[Untitled]'")->where("title != null");
    $featuredTours = $tourTable->fetchObjects($select);
 
    $html = '<h3>Related Tours</h3>';
    if(count($featuredTours) !== 0) {
    $html .= '<ul>';
	    foreach($featuredTours as $featuredTour) {
		if ($featuredTour) {
		    $html .= '<li>' . link_to_tour($tourTitle, array(), 'show', $featuredTour) . '</li>';
		    }
		}
    } else {
	  $html .= '<p>No related tours are available.</p>';
    }
    $html .= '</ul>';
 
    return $html;
}
*/

?>