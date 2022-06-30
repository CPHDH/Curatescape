<?php
function cah_item_type(){
	$itemTypeMeta = array(
		'name'=> 'Curatescape Story',
		'description' => 'A narrative body of text being sent to Curatescape mobile applications or being displayed using Curatescape themes. Please use relevant Dublin Core fields for Title, Creator, and other key elements as needed.',
	);	
	return $itemTypeMeta;
}

function cah_get_element_id($set=null,$element=null){
	if(element_exists($set,$element)){
		$elementObj= get_record('Element',array('name'=>$element));
		return $elementObj->id;
	}
}

function cah_elements(){
	$elements = array(
		array(
			'name'=>'Subtitle',
			'description'=>'A subtitle or alternate title for the entry.',
			'order'=>1
			),
		array(
			'name'=>'Lede',
			'description'=>'A brief introductory section that is intended to entice the reader to read the full entry.',
			'order'=>2
			),
		array(
			'name'=>'Story',
			'description'=>'The primary full-text for the entry.',
			'order'=>3
			),
		array(
			'name'=>'Sponsor',
			'description'=>'The name of a person or organization that has sponsored the research for this specific entry. Use HTML to create an active link.',
			'order'=>4
			),
		array(
			'name'=>'Factoid',
			'description'=>'One or more facts or pieces of information related to the entry, often presented as a list. Examples include architectural metadata, preservation status, FAQs, pieces of trivia, etc. Use HTML to create bullet lists and headings.',
			'order'=>5
			),
		array(
			'name'=>'Related Resources',
			'description'=>'The name of or link to a related resource, often used for citation information.',
			'order'=>6
			),
		array(
			'name'=>'Official Website',
			'description'=>'An official website related to the entry. Use HTML to create an active link.',
			'order'=>7
			),
		array(
			'name'=>'Street Address',
			'description'=>'A detailed street/mailing address for a physical location.',
			'order'=>8
			),				
		array(
			'name'=>'Access Information',
			'description'=>'Information regarding physical access to a location, including restrictions (e.g. "Private Property"), walking directions (e.g. "To reach the peak, take the trail on the left"), or other useful details (e.g. "Location is approximate").',
			'order'=>9
			),
	);
	return $elements;
}

function cah_item_form_helper_text_array(){
	$it=cah_item_type();
	$it_name=$it['name'];
	$mod = array(
		'tabs'=> 
			array( // tabs that need some helper text
				array(
				'text'=>'<p class="cah-helper">'.__('Use Dublin Core fields to add basic information, including the <strong>Title</strong>, <strong>Creator</strong> (Author), and <strong>Subjects</strong>. <span class="divider"></span><strong>Tip:</strong> Use the green <strong>Add Input</strong> buttons when adding more than one Subject or Creator.').'</p>',
				'insert_point'=>'.items #edit-form #dublin-core-metadata .set h2',
				),
				array(
				'text'=>'<p class="cah-helper">'.__('Choose <strong>%s</strong> from the select menu to reveal Curatescape element fields. <span class="divider"></span><strong>Tip:</strong> Use the green <strong>Add Input</strong> buttons when adding more than one Factoid or Related Resource.',$it_name).'</p>',
				'insert_point'=>'.items #edit-form #item-type-metadata-metadata .set h2',	
				),
				array(
				'text'=>'<p class="cah-helper">'.__('%s items should have <strong>at least one image</strong> file. <span class="divider"></span><strong>File Formats: </strong><br>Upload <strong>image</strong> files in the JPEG or PNG format. <br>Upload <strong>audio</strong> files in the MP3 format. <br>Upload <strong>video</strong> files in the H.264/MP4 format. <br>For more, see: <a target="_blank" rel="noreferrer" href="https://github.com/CPHDH/Curatescape/wiki/Formatting-requirements">formatting requirements</a><span class="divider"></span><strong>Important:</strong> after files have been uploaded the item record has been saved, please return to this page to add additional information for each file, including captions and source information.',$it_name).'</p>',
				'insert_point'=>'.items #edit-form #files-metadata .set h2',	
				),
				array(
				'text'=>'<p class="cah-helper">'.__('Add tags to tie your entries together according to common themes and then click the <strong>Add Tags</strong> button to submit. <span class="divider"></span><strong>Tips:</strong> In contrast to formal Subject terms, tags are often used more informally. It is recommended to use no more than 8-10 tags per item. Avoid applying tags that are unlikely to be re-used.').'</p>',
				'insert_point'=>'.items #edit-form #tags-metadata .set h2',	
				),
				array(
				'text'=>'<p class="cah-helper">'.__('Add a <strong>location</strong> for the item by entering a search term and clicking <strong>Find</strong> or by clicking directly on the map to manually add or move a marker.').'</p>',
				'insert_point'=>'.items #edit-form #map-metadata .set h2',	
				),
				array(
				'text'=>'<p class="cah-helper">'.__('Use Dublin Core fields to add basic information, like the <strong>Title</strong>, <strong>Description</strong>, <strong>Creator</strong> (Author), <strong>Source</strong> and <strong>Date</strong>. If you would like to utilize additional Dublin Core Fields, use the "Reveal" button below. <span class="divider"></span><strong>Note:</strong> This information is used to create file captions.').'</p>',
				'insert_point'=>'.files #edit-form #dublin-core-metadata .set h2',	
				),				
			),
		'item_fields'=> 
			array( // key fields to reorder, etc...
			cah_get_element_id('Dublin Core','Title'),
			cah_get_element_id('Dublin Core','Creator'),
			cah_get_element_id('Dublin Core','Subject')
			),
		'file_fields'=> 
			array( // key fields to reorder, etc...
			cah_get_element_id('Dublin Core','Title'),
			cah_get_element_id('Dublin Core','Description'),
			cah_get_element_id('Dublin Core','Creator'),
			cah_get_element_id('Dublin Core','Source'),
			cah_get_element_id('Dublin Core','Date'),
			cah_get_element_id('Dublin Core','Rights'),				
			),
		'add_input_supported'=>
			array(
			cah_get_element_id('Dublin Core','Subject'),
			cah_get_element_id('Dublin Core','Creator'),
			cah_get_element_id('Item Type Metadata','Factoid'),
			cah_get_element_id('Item Type Metadata','Related Resources'),
			),
		'use_html_supported'=>
		array(
		cah_get_element_id('Dublin Core','Description'),
		cah_get_element_id('Dublin Core','Publisher'),
		cah_get_element_id('Dublin Core','Relation'),
		cah_get_element_id('Dublin Core','Source'),
		cah_get_element_id('Dublin Core','Rights'),
		cah_get_element_id('Item Type Metadata','Lede'),
		cah_get_element_id('Item Type Metadata','Story'),
		cah_get_element_id('Item Type Metadata','Sponsor'),
		cah_get_element_id('Item Type Metadata','Factoid'),
		cah_get_element_id('Item Type Metadata','Related Resources'),
		cah_get_element_id('Item Type Metadata','Official Website'),
		cah_get_element_id('Item Type Metadata','Access Information'),
		),	
		);
	return json_encode($mod);
}

/* Get some info about the files to display on admin dahboard*/
function cah_get_file_info(){
	$file_dir=$_SERVER['DOCUMENT_ROOT'].'/files/original/';
	$files = 0;
	$images = 0;
	$audio = 0;
	$video = 0;
	$uncounted = 0;
	if(is_dir($file_dir)){
		$dir = opendir($file_dir);
		while ($file = readdir($dir)) {
			$ext=strtolower(pathinfo($file, PATHINFO_EXTENSION));
		    if ($file == '.' || $file == '..' || in_array($ext, array(null,'','html','htaccess'))) {
		    	continue;
		    }
			elseif(in_array($ext, array('jpg','jpeg','png','gif','tif','tiff'))){
				$images++;
			}
			elseif(in_array($ext, array('mp4','m4v','mov'))){
				$video++;
			}
			elseif(in_array($ext, array('mp3','wav','ogg'))){
				$audio++;
			}
			else{
				$uncounted++;
			}
		    $files++;
		}
		return 'Total Files: '.$files.' ('.$images.' images, '.$audio.' audio, '.$video.' video, '.$uncounted.' other)';
	}else{
		return null;
	}
}

function cah_resources_guide(){
	$html  = null;
	$html .= '<section class="five columns alpha"><div class="panel">';
	$html .= '<h2>'.__('Curatescape Resources').'</h2><br>';
	$html .= '<h4>'.__('Documentation').'</h4>';
	$html .= '<p>'.__('For detailed information on setup, deployment, and usage, please visit <a href="https://curatescape.org/docs/">curatescape.org/docs</a> or contact your project manager').'</p>';
	$html .= '<h4>'.__('User Community').'</h4>';
	$html .= '<p>'.__('Curatescape users are invited to join the <a href="https://forum.curatescape.org/">Curatescape Forum</a>. Curatescape is also on <a href="https://www.facebook.com/curatescape">Facebook</a> and <a href="https://twitter.com/curatescape">Twitter</a>').' .</p>';
	$html .= '</div></section>';
	return $html;
}

function cah_components_guide(){
	// Theme
	$theme = (strpos(Theme::getTheme(Theme::getCurrentThemeName('public'))->title, 'Curatescape') === 0);
	$text_theme = $theme ? '<li>'.__('The Curatescape theme is currently active.').' '.__('<a class="config" href="%s">Configure theme settings</a>',WEB_ROOT.'/admin/themes').'</li>' : '<li>'.__('The Curatescape theme is not activated. Activate theme in <a href="%s">Appearance settings</a>.',WEB_ROOT.'/admin/themes/').'</li>';
	$icon_ok = '<i class="fa fa-check-circle"></i>';
	$icon_warning = '<i class="fa fa-exclamation-triangle"></i>';
	$html  = null;
	$html .= '<section class="five columns omega"><div class="panel">';
	$html .= '<h2>'.__('Curatescape Components').'</h2><br>';
	$html .= '<h4>'.__('Theme Settings').' '.($theme ? $icon_ok : $icon_warning).'</h4>';
	$html .= '<ul>';
	$html .= $text_theme;
	$html .= '</ul>';
	// Plugins
	$required_plugins=array('Geolocation'=>true, 'SimplePages'=>false, 'TourBuilder'=>false, 'CuratescapeJSON'=>false);
	$active=0;
	$text_plugin=null;
	foreach($required_plugins as $name=>$config){
		if(plugin_is_active($name)){
			$active++;
			$config_link = $config ? '<a class="config" href="'.WEB_ROOT.'/admin/plugins/config?name='.$name.'">'.__('Configure plugin settings').'</a>.' : null;
			$text_plugin.='<li>'.__('The %s plugin is currently active.',$name).' '.$config_link.'</li>';
		}else{
			$text_plugin.='<li>'.__('The %s plugin is <strong>not activated</strong>. Activate plugin in <a href="'.WEB_ROOT.'/admin/plugins/">Plugins settings</a>.',$name).'</li>';
		}
	}
	$html .= '<h4>'.__('Required Plugins').' '.( (count($required_plugins) == $active ) ? $icon_ok : $icon_warning ).'</h4>';
	$html .= '<ul>';
	$html .= $text_plugin;
	$html .= '</ul>';
	// Item Type 
	$it_info=cah_item_type();
	$it_name=$it_info['name'];	
	$it=get_record('ItemType',array('name'=>$it_name));
	$type_exists = (bool)$it;
	$text_type = ($type_exists) ? '<li>'.__('The "%s" item type exists.',$it_name).'</li>' : '<li>'.__('Please create the "%s" item type.',$it_name).'</li>';
	$missing_elements=0;
	$text_elements=null;
	if($type_exists){
		// array of currently assigned elements
		$elementsForType=$it->Elements;
		$a=array();
		foreach($elementsForType as $e) {
			$a[]=$e['name']; 
		}
		// array of required elements
		$reqElementsForType = cah_elements();
		// check to see if the required elements exist AND are assigned to the item type
		foreach($reqElementsForType as $element){
			if(element_exists('Item Type Metadata',$element['name'])){
				$assigned=(bool)in_array($element['name'], $a);
				$text_elements .='<li>The "'.$element['name'].'" element exists'.($assigned ? ' and is properly assigned' : ' but <strong>MAY NOT BE PROPERLY ASSIGNED</strong> to the item type').'. </li>';
			}else{
				$missing_elements++;
				$text_elements .='<li>'.__('The "%s" element <strong>does not exist</strong>.',$element['name']).' '.__('Please update the item type record to include the element "%s".',$element['name']).'</li>';	
			}
		}
	}
	$html .= '<h4>'.__('Required Item Type and Elements').' '.( ( $type_exists && ($missing_elements==0) ) ? $icon_ok : $icon_warning ).'</h4>';
	$html .= '<ul>';
	$html .= $text_type;
	$html .= '<ul>'.$text_elements.'</ul>';	
	$html .= '</ul>';
	echo $html;
}