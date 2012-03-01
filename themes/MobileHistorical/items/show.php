<?php
if (mobile_device_detect()==true){
//begin mobile items/show
?>
<?php 
$location=item('Dublin Core', 'Title');
head( array( 'title' => $location) );?>
<!-- Start of page content: #show -->

<div data-role="content" id="show">	
<?php echo mobile_simple_search();?>

	    <div class="title">
		<h2><?php echo item('Dublin Core', 'Title');?></h2>
		</div>
		
		<div class="description">
		<?php// echo item_square_thumbnail(array('class'=>'inline-item-image'));?>
		<p><?php echo item('Dublin Core', 'Description');?></p>
		</div>

		<div class="tags">
		<?php if (item_tags_as_string()!=null):?>
		<h4>Tags</h4>
		<p><?php echo item_tags_as_cloud(); ?></p>
		<?php endif;?>
		</div>
		
		<?php echo geolocation_public_show_item_map('100%', '10em');
		    $location = geolocation_get_location_for_item($item, true);
			$lng  = (double) $location['longitude'];
            $lat  = (double) $location['latitude'];
            ?>
		<div data-role="footer" style="margin-top:0.5em;">
			<div data-role="navbar">
				<ul>
				<li><?php echo '<a href="http://maps.google.com/maps?q='.$lat.','.$lng.'">View in Google Maps</a></li>';?>
				</ul>
			</div><!-- /navbar -->
		</div><!-- /footer -->

	
		<div class="files" id="gallery">
		<!-- FLOWPLAYER -->
		<?php echo js('flowplayer/flowplayer-3.2.6.min');?>
		<?php echo js('flowplayer/flowplayer.playlist-3.0.8');?>
		<link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT;?>/themes/MobileHistorical/javascripts/flowplayer/style.css"/> 
		<!-- END FLOWPLAYER -->
		<?php while ($file = loop_files_for_item()):$file = get_current_file();?>

		<?php
	    	$title = item_file('Dublin Core', 'Title');	
	    	$desc = item_file('Dublin Core', 'Description');	
	    	$imguri=item_file('fullsize uri');	
	    	$uri=item_file('uri');
			$mime = item_file('MIME Type');
			$video = array('video/mp4','video/mpeg','video/ogg','video/quicktime','video/webm','video/avi','video/msvideo','video/x-msvideo','video/x-ms-wmv');
			$audio = array('application/ogg','audio/aac','audio/aiff','audio/midi','audio/mp3','audio/mp4','audio/mpeg','audio/mpeg3','audio/mpegaudio','audio/mpg','audio/ogg','audio/wav','audio/x-mp3','audio/x-mp4','audio/x-mpeg','audio/x-mpeg3','audio/x-midi','audio/x-mpegaudio','audio/x-mpg','audio/x-ogg','audio/x-wav','audio/x-wave','audio/x-aac','audio/x-aiff','audio/x-midi','audio/x-mp3','audio/x-mp4','audio/x-mpeg','audio/x-mpeg3','audio/x-mpegaudio','audio/x-mpg,audio/wma','audio/x-ms-wma');
				//if video
				if (array_search($mime,$video) !== false) {
					echo '<div class="file" id="movie">';
					echo '<video controls="controls" poster="'.mh_poster_url().'">
					<source src="'.$uri.'" type="video/mp4"/>
					Download <a href="'.$uri.'">video file</a> ('.$mime.')</video>';
	    			echo '<h4>Video: '.$title.'</h4>';
	    			echo '<p>'.$desc.'</p>';
	    			echo '</div>';
				}
				//if audio
				elseif (array_search($mime,$audio) !== false) {
					echo '<div class="file">';
					echo '<audio controls="controls">';
					echo '<source src="'.$uri.'" type="'.$mime.'"/>';
					echo 'Download <a href="'.$uri.'">audio file</a> ('.$mime.')</audio>';
	    			echo '<h4>Audio: '.$title.'</h4>';
	    			echo '<p>'.$desc.'</p>';
	    			echo '</div>';
				}
				//if image
	    		elseif ($file->hasThumbnail()) 
	    		{
	    			echo '<div class="file gallery-item">';
	    			echo '<a href="'.$imguri.'" target="_self"><img src="'.$imguri.'"/></a>';
	    			echo '<h4>'.$title.'</h4>';
	    			echo '<p>'.$desc.'</p>';
	    			echo '</div>';
				}
				//if other, allow download only
				else{
					echo '<div class="file">';
	    			echo '<h4>Downloadable file: '.$title.'</h4>';
	    			echo '<p>Download: <a href="'.$uri.'">'.$title.'</a> ('.$mime.').</p>';
	    			echo '</div>';
				}

		?>
		<?php endwhile;?>
		</div>
		<!-- video controller scripts for older Android devices-->
		<script>
		  var v = document.getElementById("movie");
		  if (v != null){
		  v.onclick = function() {
		    if (v.paused) {
		      v.play();
		    } else {
		      v.pause();
		    }
		  };
		  }
		</script>		

<div style="clear:both"></div>	

<div id="item-np-nav">
	<?php
	echo link_to_previous_item('PREV', array('target'=>'_self', 'data-role'=>'button','data-icon'=>'arrow-l','data-iconpos'=>'left','class'=>'left')); 
	echo link_to_next_item('NEXT', array('target'=>'_self', 'data-role'=>'button','data-icon'=>'arrow-r','data-iconpos'=>'right','class'=>'right')); 
	?>
</div>	
<div style="clear:both"></div>	
<a href="#download-app" data-role="button" data-rel="dialog" data-transition="pop" id="download-app">Download the App</a>	

</div> <!-- end content-->

<?php echo common('m-footer-nav');?>

</div> <!-- end outer page from header -->	

<?php echo common('m-dialogues');?>

</body>
</html>

<? 
// end mobile items/show
	}
else{	
// begin non-mobile items/show
?>
<?php head(array('title' => item('Dublin Core', 'Title'))); ?>
	<div id="content">
			
		    <div id="header">
			<div id="primary-nav">
    			<ul class="navigation">
    			    <?php echo public_nav_main(array('Home' => uri('/'), 'Tours' => uri('/tour-builder/tours/browse/'), 'Browse Locations' => uri('items'))); ?>
    			</ul>
    		</div>
    		<div id="search-wrap">
				    <?php echo simple_search(); ?>
    			</div>
    			<div style="clear:both;"></div>
   		</div>
	

<?php 
function normalize_special_characters( $str ) 
{ 
    # Quotes cleanup 
    $str = str_replace( chr(ord("`")), "'", $str );        # ` 
    $str = str_replace( chr(ord("´")), "'", $str );        # ´ 
    $str = str_replace( chr(ord("„")), ",", $str );        # „ 
    $str = str_replace( chr(ord("`")), "'", $str );        # ` 
    $str = str_replace( chr(ord("´")), "'", $str );        # ´ 
    $str = str_replace( chr(ord("“")), "\"", $str );        # “ 
    $str = str_replace( chr(ord("”")), "\"", $str );        # ” 
    $str = str_replace( chr(ord("´")), "'", $str );        # ´ 

    $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 
                                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 
                                'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 
                                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 
                                'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' ); 
    $str = strtr( $str, $unwanted_array ); 
    
    #For reasons yet unknown, some servers may require an additional $unwanted_array item: 'eight'=>'&#101;ight'

    # Bullets, dashes, and trademarks 
    $str = str_replace( chr(149), "&#8226;", $str );    # bullet • 
    $str = str_replace( chr(150), "&ndash;", $str );    # en dash 
    $str = str_replace( chr(151), "&mdash;", $str );    # em dash 
    $str = str_replace( chr(153), "&#8482;", $str );    # trademark 
    $str = str_replace( chr(169), "&copy;", $str );    # copyright mark 
    $str = str_replace( chr(174), "&reg;", $str );        # registration mark 
    $str = str_replace( "&quot;", "\"", $str );        # "
    $str = str_replace( "&apos;", "\'", $str );        # '

    return $str; 
} 
?>


<!-- -->
<div id="page-col-left">

<div id="lv-logo"><a href="<?php echo WEB_ROOT;?>/"><img src="<?php echo mh_med_logo_url(); ?>" border="0" alt="Cleveland Historical" title="Cleveland Historical" /></a></div>

<div id="lv-map">
<?php 
if (function_exists('geolocation_get_location_for_item')){ 
		    $location = geolocation_get_location_for_item($item, true);
			$lng  = (double) $location['longitude'];
            $lat  = (double) $location['latitude'];
            echo geolocation_public_show_item_map();
            echo '<a target="_blank" style="margin-top:3px;padding-top:3px;display:block; color:#999" href="http://maps.google.com/maps?q='.$lat.','.$lng.'">View in Google Maps</a></li>'; }           ?>

</div>


<div id="audio-list">
<h3>Audio Files</h3>
<?php $audioTypes = array('audio/mpeg'); ?>
	<?php $myaudio = array(); ?>
	<?php while ($file = loop_files_for_item()):
				$mime = item_file('MIME Type');
				
				//echo $mime;
				
				
				if ( array_search($mime, $audioTypes) !== false ) {
					// echo code here
					array_push($myaudio, $file);
				?>
				<h4><?php echo item_file('Dublin Core', 'Title') ?></h4>
			
				<?php 
				$msie = strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') ? true : false; 
				$firefox = strpos($_SERVER["HTTP_USER_AGENT"], 'Firefox') ? true : false;
				$safari = strpos($_SERVER["HTTP_USER_AGENT"], 'Safari') ? true : false;
				$chrome = strpos($_SERVER["HTTP_USER_AGENT"], 'Chrome') ? true : false;
				$opera = strpos($_SERVER["HTTP_USER_AGENT"], 'Opera') ? true : false;
				?>

<?php
if (($firefox == 'true')||($msie == 'true')||($opera == 'true')){
echo '<object type="application/x-shockwave-flash" data="'.WEB_ROOT.'/themes/MobileHistorical/javascripts/dewplayer/dewplayer.swf" width="200" height="20" id="audioplayer" name="audioplayer">';
echo '<param name="movie" value="'.WEB_ROOT.'/themes/MobileHistorical/javascripts/dewplayer/dewplayer.swf" />';
echo '<param name="flashvars" value="mp3='.file_download_uri($file).'" />';
echo '<param name="wmode" value="transparent" />';
echo '</object>';
//echo '<p style="font-size:1em">'.item_file('Dublin Core', 'Description').'</p>';

} else {
echo '<audio controls="controls"><source src="'.file_download_uri($file).'" type="audio/mpeg" /><h5 class="no-audio"><strong>Download Audio:</strong><a href="'.file_download_uri($file).'">MP3</a></h5></audio>';
//echo '<p style="font-size:1em">'.item_file('Dublin Core', 'Description').'</p>';
?>


				
			
				<?php
				}
				}
				?>
	<?php endwhile; ?>
	<?php
		if ( count($myaudio) == null ) {
					// echo code here
					
					echo '<li>No audio available.</li>';
				}
	?>
</div>
 
	
	
  	
    	<?php
$subjects = item('Dublin Core', 'Subject', 'all');

if (count($subjects) > 1):

?>


    	<h3>Subject</h3>
    	<div>

<?php foreach ($subjects as $subject): ?>
<li>
<a href="<?php echo WEB_ROOT;?>/items/browse?search=&advanced[0][element_id]=49&advanced[0][type]=contains&advanced[0][terms]=<?php echo $subject; ?>&submit_search=Search"><?php echo $subject; ?></a>
</li>
<?php endforeach; ?>
</div>
<?php endif; ?>
    	
	
	
	
	
	
	
	<div>
	 <!-- The following prints a list of all tags associated with the item -->
	<?php if (item_has_tags()): ?>
	
		<h3>Tags:</h3>
	    <?php echo item_tags_as_string(
    		$delimiter = ' ', 
    		$order = 'alpha', 
   			$tagsAreLinked = true, 
   	 		$item=null, 
    		$limit=null
		);?>
	
	<?php endif;?>
	</div>

</div>
<!-- -->





<div id="primary" class="show">

	<h1 class="item-title"><?php echo item('Dublin Core', 'Title'); ?></h1>
					
  <div class="video-js-box" style="z-index:-100;">
  
  <?php function loadPlaylist() {
  	$myvideo = array();
  	$videoItems = 0;
  	$videoTypes = array('video/mp4','video/mpeg','video/quicktime');
	while(loop_files_for_item()): 
        $file = get_current_file();
        $mime = item_file("MIME Type");


				if ($mime == 'video/quicktime' or $mime == 'video/mpeg' or $mime == 'video/mp4' or $mime == ''){
					$videoItems++;
					//array_push($myvideo, "test");
					//echo "hey";
					if($videoItems > 1) { echo ","; }
					echo '{';
					echo 'url:"'.file_download_uri($file).'"';
					echo ', ';
					echo 'title:"'.item_file('Dublin Core', 'Title').'"';
					echo ', ';
					echo 'mime:"'.$mime.'"';
					echo '}';
					//echo $myvideo;
				}
		endwhile;
	
} 

?>
  	
  	
  <script> 
// wait for the DOM to load using jQuery
$(function() {
	
	// setup player normally
	$f("player1", {src:"http://releases.flowplayer.org/swf/flowplayer-3.2.7.swf", wmode:"transparent" }, {
	
		// clip properties common to all playlist entries
		clip: {
			baseUrl: 'http://clevelandhistorical.org',
			subTitle: 'from ClevelandHistorical.org',
			autoPlay: false,
			autoBuffering: true
		},
		
		/**/
		playlist: [
		
		<?php loadPlaylist(); ?>
		
		],
		/**/
		
		// show playlist buttons in controlbar
		plugins: {
			controls: {
				autoHide: 'always',
				playlist: true,
				all: false,   
            	play: true,  
            	scrubber: true,  
            	fullscreen:true 
			}
		}
	});
	
	/*
		here comes the magic plugin. It uses first div.clips element as the 
		root for as playlist entries. loop parameter makes clips play
		from the beginning to the end.
	*/
	$f("player1").playlist("div.clips:first", {loop:true});
	
});
</script> 
	</div>
	
	
	
	  <?php function loadPlaylistB() {
  	$myvideo = array();
  	$videoItems = 0;
	while(loop_files_for_item()): 
        $file = get_current_file();
        $mime = item_file("MIME Type");
				if ($mime == 'video/quicktime'){
					$videoItems++;
				}
		endwhile;
		//echo $videoItems;
	if($videoItems >0) {
		echo '<a class="player plain" id="player1" style="float:left"><img src="'.mh_poster_url().'" /></a>'; 
		echo '<div class="clips" style="float:left">';
		echo '<a href="${url}">';
		echo '${title}';
		echo '</a></div>';
	}
	}
?>
 <?php loadPlaylistB(); ?> 	
	

<!-- let rest of the page float normally --> 
<br clear="all"/> 




  <!-- -->

<div class="item-description">
	

	<?php echo item('Dublin Core', 'Description');?>
	<br/><?php echo link_to_item_edit();?>

</div>
	
	<!-- uncomment the following line to allow plugins to automatically insert content into the primary container -->
	<?php //echo plugin_append_to_items_show(); ?> 
</div><!-- end primary -->


<!-- -->
<div id="page-col-right">


<!-- The following returns all of the images associated with an item. -->
	<div id="itemfiles" class="element">
		<div class="element-text">
		    <!-- -->
		    
		<h3>Photos</h3>

<!-- -->

		    <?php 	    
		    while ($file = loop_files_for_item()):
	    		if ($file->hasThumbnail()) {
	    			//
	    			$query = item_file('Dublin Core', 'Description');
	    			$photoDesc = normalize_special_characters($query);
	    			//
	    			$query = item_file('Dublin Core', 'Title');
	    			$photoTitle = normalize_special_characters($query); 
	    			//
	    			echo display_file($file, array('linkAttributes'=>array('rel'=>'clearbox[gallery=Photo Gallery,,comment='.$photoDesc.',,title='.$photoTitle.']')));
				} else {
				// echo display_files_for_item();
				}
			?>

			

			<?php endwhile; ?>

			<div id="cite-this">
			<h3 style="padding-top:15px;clear:both">Cite this Page</h3>
			<?php echo item_citation(); ?>
			</div>
			
			<div id="share-this">
			<h3 style="margin-top:10px;clear:both">Share this Page</h3>
			<!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
			<a class="addthis_button_preferred_1"></a>
			<a class="addthis_button_preferred_2"></a>
			<a class="addthis_button_preferred_3"></a>
			<a class="addthis_button_preferred_4"></a>
			</div>
			<?php $addthis = (get_theme_option('Add This')) ? (get_theme_option('Add This')) : 'ra-4e89c646711b8856';?>
			<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=<?php echo $addthis ;?>"></script>
				<!-- replace #pubid= value with your ADDTHIS user profile to enable analytics (see settings >> profiles) -->
			<!-- AddThis Button END -->
			</div>

		</div>
		
	</div>	
</div>
<!-- -->


<?php foot(); ?>
<?php 
//end non-mobile items/show
}?>