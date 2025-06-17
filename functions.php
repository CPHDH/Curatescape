<?php
function dc($record, $elementName, $options = array())
{
	if(!$record || !$elementName) return null;
	return metadata($record, array('Dublin Core', $elementName), $options);
}

function itm($record, $elementName, $options = array())
{
	if(!$record || !$elementName) return null;
	if(!element_exists('Item Type Metadata', $elementName)) return null;
	return metadata($record, array('Item Type Metadata', $elementName), $options);
}

function svg($name){
	// @todo: cacheable "sprite sheet"
	if(!$name) return null;
	$path = _PLUGIN_DIR_."/views/shared/images/svg/$name.svg";
	if(!file_exists($path)) return null;
	return file_get_contents($path);
}

function isCuratescapeStory($record)
{
	if(!$record) return false;
	if($record->getRecordUrl()['controller'] !== 'items') return false;
	if($record->getProperty('item_type_name') !== _CURATESCAPE_ITEM_TYPE_NAME_) return false;
	return true;
}

function normalizeTextBlocks($text, $output=null)
{
	if(!$text) return null;
	// breaks to paragraphs
	$text = str_replace(array('<p>','</p>'),'', $text);
	foreach(preg_split("#(<br */?>\s*)+#i", $text) as $p){
		$output .= '<p>'.$p.'</p>';
	}
	// remove empty tags
	$output =  preg_replace('/<[^\/>]*>([\s]?)*<\/[^>]*>/', '', $output); 
	return $output;
}

function plainText($text){
	return $text ? trim( html_entity_decode( strip_formatting($text))) : null;
}

function oxfordAmp($names=array(), $html = null)
{
	if(!$names) return array();
	for($index = 1; $index <= count($names); $index++){
		switch ($index) {
			case (count($names)):
			$delim ='';
			break;
			case (count($names)-1):
			$delim =(count($names) > 2 ? ',' : '').'&#32;<span class="curatescape-amp">&amp;</span>&#32;';
			break;
			default:
			$delim =', ';
			break;
		}
		$html .= $names[$index-1].$delim;
	}
	return $html;
}

function preferredItemImageUrl($item, $size = 'fullsize', $nullresult = null)
{
	if(!$item) return $nullresult;
	if(!$item->hasThumbnail()) return $nullresult;
	foreach($item->getFiles() as $file){
		if($file->has_derivative_image){
			return record_image_url($file, $size);
		}
	}
	return $nullresult;
}

function preferredFileImageUrl($file, $size = 'fullsize', $nullresult = null, $omitVideo = false)
{
	if(!$file) return $nullresult;
	if($file->has_derivative_image){
		if($omitVideo && fileTypeString($file) == 'video') return $nullresult;
		return record_image_url($file, $size);
	}
	return $nullresult;
}

function fileTypeString($file, $nullresult = 'Unknown')
{
	if(!$file) return $nullresult;
	$mimetype = metadata($file, 'mime_type');
	$filetype = explode('/', $mimetype);
	return isset($filetype[0]) ? $filetype[0] : $nullresult;
}

function fileSubTypeString($file, $nullresult = 'Unknown')
{
	if(!$file) return $nullresult;
	$mimetype = metadata($file, 'mime_type');
	$filetype = explode('/', $mimetype);
	return isset($filetype[1]) ? $filetype[1] : $nullresult;
}

function comboTitle($title, $subtitle, $pre = ': ', $post = null)
{
	if(!$subtitle) return $title;
	return $title.$pre.html_entity_decode(strip_formatting($subtitle)).$post;
}

function storiesURL()
{
	$itemType=get_record('ItemType', array('name'=>_CURATESCAPE_ITEM_TYPE_NAME_));
	if(!$itemType) return url();
	$url = 'items/browse?type='.$itemType->id;
	return url($url);
}

function itemTypeURL()
{
	$itemType=get_record('ItemType', array('name'=>_CURATESCAPE_ITEM_TYPE_NAME_));
	if(!$itemType) return url();
	$url = 'item-types/show/'.$itemType->id;
	return admin_url($url);
}

function sortByOrdinal($a, $b){
	// 0/default value will always follow ASC custom values: 1,2,3,0,0
	if ($a['ordinal'] == $b['ordinal']) return 0;
	if ($a['ordinal'] == 0) return 1;
	if ($b['ordinal'] == 0) return -1;
	return $a['ordinal'] > $b['ordinal'] ? 1 : -1;
}

function metaImage($url = ''){
	if(option('curatescape_meta_image') && strlen(option('curatescape_meta_image')) > 6){
		// plugin option (string)
		$url = trim(option('curatescape_meta_image'));
	}elseif(get_theme_option('curatescape_meta_image') && strlen(get_theme_option('curatescape_meta_image')) > 5){
		// theme upload (available for theme developers)
		$url = WEB_ROOT.'/files/theme_uploads/'.trim(get_theme_option('curatescape_meta_image')); 
	}elseif(get_theme_option('custom_meta_img') && strlen(get_theme_option('custom_meta_img')) > 5){
		// theme upload (legacy)
		$url = WEB_ROOT.'/files/theme_uploads/'.trim(get_theme_option('custom_meta_img')); 
	}
	if(!$url) return '';
	// validate/sanitize
	$url = html_escape(filter_var($url, FILTER_SANITIZE_URL));
	if(substr($url,0,4) !== "http" || !allowedExtensionImg($url)){
		return '';
	}
	if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === FALSE) {
		return '';
	}
	return $url;
}

function allowedExtensionImg($filepath, $allowed = array('jpg','jpeg','png', 'webp')){
	$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
	return in_array($ext, $allowed);
}

}

}

}

}

	}
	}
}

		}
	}
}

		}
	}
}

// used only for PDF display
function browserCategory(){
	if($user_agent = $_SERVER['HTTP_USER_AGENT']){
		if (strpos($user_agent, 'Chrome')) {
			return 'chromium';
		}
		if (strpos($user_agent, 'Firefox')) {
			return 'firefox';
		};
	}	
	return 'other';
}

function configFormCheckBox($optionName, $labelName, $helperText){
	if(!$optionName || !$labelName || !$helperText) return null;
	?>
	<div class="field">
		<div class="two columns alpha">
			<label for="<?php echo $optionName;?>"><?php echo __($labelName); ?></label>
		</div>
		<div class="inputs five columns omega">
			<p class="explanation"><?php echo __($helperText); ?></p>
			<?php echo get_view()->formCheckbox($optionName, true,
			array('checked'=>(boolean)get_option($optionName))); ?>
		</div>
	</div>
	<?php
}

function configFormSelect($optionName, $labelName, $helperText, $options=array()){
	if(!$optionName || !$labelName || !$helperText || !count($options)) return null;
	?>
	<div class="field">
		<div class="two columns alpha">
			<label for="<?php echo $optionName;?>"><?php echo __($labelName); ?></label>
		</div>
		<div class="inputs five columns omega">
			<p class="explanation"><?php echo __($helperText); ?></p>
			<?php echo get_view()->formSelect($optionName, get_option($optionName), null, $options); ?>
		</div>
	</div>
	<?php
}

function configFormText($optionName, $labelName, $helperText, $placeholder=null){
	if(!$optionName || !$labelName || !$helperText) return null;
	?>
	<div class="field">
		<div class="two columns alpha">
			<label for="<?php echo $optionName;?>"><?php echo __($labelName); ?></label>
		</div>
		<div class="inputs five columns omega">
			<p class="explanation"><?php echo __($helperText); ?></p>
			<?php echo get_view()->formText($optionName, get_option($optionName), array('placeholder' => __($placeholder))); ?>
		</div>
	</div>
	<?php
}