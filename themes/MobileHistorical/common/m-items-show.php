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
		
		<?php echo geolocation_public_show_item_map('100%', '10em');?>	
	
		<div class="files" id="gallery">
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
					echo '<div class="file">';
					echo '<video controls="controls" poster="'.img('poster.jpg').'">
					<source src="'.$uri.'" type="'.$mime.'"/>
					  <object width="320" height="240" type="application/x-shockwave-flash"
					    data="http://releases.flowplayer.org/swf/flowplayer-3.2.7.swf"> 
					    <param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.7.swf" /> 
					    <param name="allowfullscreen" value="true" /> 
					    <param name="flashvars" value=\'config={"clip": {"url": "'.$uri.'", "autoPlay":false, "autoBuffering":true}}\' /> 
					  </object>
					Your browser cannot play this HTML video file.</video>';
	    			echo '<h4>Video: '.$title.'</h4>';
	    			echo '<p>'.$desc.'</p>';
	    			echo '</div>';
				}
				//if audio
				elseif (array_search($mime,$audio) !== false) {
					echo '<div class="file">';
					echo '<audio controls="controls">
					<source src="'.$uri.'" type="'.$mime.'"/>
					Your browser cannot play this HTML audio file.</audio>';
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
				//if other, display error message
				else{
					echo '<div class="file">';
	    			echo '<h4>Orphan file</h4>';
	    			echo '<p>File Error: Incompatible Format or MIME Type for <a href="'.item_file('uri').'">'.$title.'</a> ('.item_file('MIME Type').').</p>';
	    			echo '</div>';
				}

		?>
		<?php endwhile;?>
		</div>
		<!-- video controller scripts for older Android devices-->
		<script>
		  var v = document.getElementById("movie");
		  v.onclick = function() {
		    if (v.paused) {
		      v.play();
		    } else {
		      v.pause();
		    }
		  };
		</script>		

<div style="clear:both"></div>	

<a href="#download-app" data-role="button" data-rel="dialog" data-transition="pop" id="download-app">Download the App</a>	

</div> <!-- end content-->

<?php echo common('footer-nav');?>

</div> <!-- end outer page from header -->	

<?php echo common('dialogues');?>

</body>
</html>
