<?php
class Curatescape_View_Helper_HookAdminHead extends Zend_View_Helper_Abstract{
	const DEPRECATEDPLUGINS = array(
		'CuratescapeJSON',
		'CuratescapeAdminHelper',
		'TourBuilder',
		'SuperRSS',
		'SendToAdminHeader',
		'MobileJSON',
	);
	const INCOMPATIBLEPLUGINS = array(
		'WalkingTour',
	);
	const MINVERSIONTHEMES = array(
		'curatescape' => '4.0',
		'curatescape-echo' => '2.0'
	);
	const MINVERSIONPLUGINS = array(
		'Geolocation' => '3.3',
	);
	public function HookAdminHead($args){
		$this->adminCss();
		$this->adminJs();
		$this->compatibilityCheck();
		$this->itemFormMods();
		$this->fileFormMods();
		$this->lockItemTypeForm();
	}
	private function itemTypeId(){
		$itemType=get_record('ItemType', array('name'=>_CURATESCAPE_ITEM_TYPE_NAME_));
		if(!$itemType) return null;
		return $itemType->id;
	}
	private function adminCss()
	{
		queue_css_file('curatescape-dashboard', 'all', false, 'css', get_plugin_ini('Curatescape', 'version'));
		if(is_current_url('/admin/tours/')){
			queue_css_file('curatescape-tours', 'all', false, 'css', get_plugin_ini('Curatescape', 'version'));
		}
		if(
			is_current_url('/admin/plugins/config?name=Curatescape') ||
			is_current_url('/admin/plugins/config/name/Curatescape')
		){
			queue_css_file('curatescape-config', 'all', false, 'css', get_plugin_ini('Curatescape', 'version'));
		}
	}
	private function adminJs()
	{
		if(is_current_url('/admin/tours/')){
			queue_js_file('curatescape-tours', 'javascripts');
		}
	}
	private function compatibilityCheck()
	{
		if(
			(is_current_url('/admin/plugins/') && !is_current_url('/admin/plugins/uninstall/')) ||
			is_current_url('/admin/themes/') ||
			is_current_url('/admin/settings/') ||
			is_current_url('/admin/tours/')
		){
			$warnings = array();
			foreach(self::DEPRECATEDPLUGINS as $plugin){
				if(plugin_is_active($plugin)){
					array_push($warnings, __('The %1s plugin is deprecated and replaced by the %2s plugin. Please deactivate, uninstall, and remove the %3s plugin to avoid conflicts.', $plugin, _PLUGIN_NAME_, $plugin));
				}
			}
			foreach(self::INCOMPATIBLEPLUGINS as $plugin){
				if(plugin_is_active($plugin)){
					array_push($warnings, __('The %1s plugin is incompatible with the %2s plugin. Please deactivate and remove the %3s plugin to avoid conflicts.', $plugin, _PLUGIN_NAME_, $plugin));
				}
			}
			foreach(self::MINVERSIONPLUGINS as $plugin=>$minversion){
				if(plugin_is_active($plugin)){
					$currentPluginVersion = get_plugin_ini($plugin, 'version');
					if(version_compare($currentPluginVersion, $minversion, '<')){
						array_push($warnings, __('The %s plugin needs to be updated. Version %s or higher is required for use with the %s plugin. Your current version is %s.', $plugin, $minversion, _PLUGIN_NAME_, $currentPluginVersion));
					}
				}
			}
			$currentThemeName = Theme::getCurrentThemeName('public');
			foreach(self::MINVERSIONTHEMES as $theme=>$minversion){
				if($theme == $currentThemeName){
					$currentTheme = Theme::getTheme($currentThemeName);
					$currentThemeTitle = $currentTheme->title;
					$currentThemeVersion = $currentTheme->version;
					if(version_compare($currentThemeVersion, $minversion, '<')){
						array_push($warnings, __('The %s theme needs to be updated. Version %s or higher is required for the %s plugin. Your current version is %s.', $currentThemeTitle, $minversion, _PLUGIN_NAME_, $currentThemeVersion));
					}
				}
			}
			if(count($warnings)){
				$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
				foreach($warnings as $message){
					$flash->addMessage($message, 'error');
				}
			}
		}
	}
	private function itemFormElements()
	{
		return array(
			array(
				'id' => $this->elementId('Dublin Core','Title'),
				'modifications'=>array('highlight','nohtml','noaddinput'),
			),
			array(
				'id' => $this->elementId('Dublin Core','Creator'),
				'modifications'=>array('highlight','nohtml'),
			),
			array(
				'id' => $this->elementId('Dublin Core','Subject'),
				'modifications'=>array('highlight','nohtml'),
			),
			array(
				'id' => $this->elementId('Item Type Metadata','Story'),
				'modifications'=>array('highlight','noaddinput','largertextarea'),
			),
			array(
				'id' => $this->elementId('Item Type Metadata','Lede'),
				'modifications'=>array('highlight','noaddinput'),
			),
			array(
				'id' => $this->elementId('Item Type Metadata','Factoid'),
				'modifications'=>array('highlight'),
			),
			array(
				'id' => $this->elementId('Item Type Metadata','Related Resources'),
				'modifications'=>array('highlight'),
			),
			array(
				'id' => $this->elementId('Item Type Metadata','Sponsor'),
				'modifications'=>array('highlight','nohtml','noaddinput'),
			),
			array(
				'id' => $this->elementId('Item Type Metadata','Street Address'),
				'modifications'=>array('highlight','nohtml','noaddinput'),
			),
			array(
				'id' => $this->elementId('Item Type Metadata','Subtitle'),
				'modifications'=>array('highlight','nohtml','noaddinput'),
			),
			array(
				'id' => $this->elementId('Item Type Metadata','Official Website'),
				'modifications'=>array('highlight','noaddinput'),
			),
			array(
				'id' => $this->elementId('Item Type Metadata','Access Information'),
				'modifications'=>array('highlight','noaddinput'),
			),
		);
	}
	private function fileFormElements()
	{
		return array(
			array(
				'id' => $this->elementId('Dublin Core','Title'),
				'modifications'=>array('highlight','nohtml','noaddinput'),
			),
			array(
				'id' => $this->elementId('Dublin Core','Description'),
				'modifications'=>array('highlight','noaddinput','largertextarea'),
			),
			array(
				'id' => $this->elementId('Dublin Core','Creator'),
				'modifications'=>array('highlight','nohtml'),
			),
			array(
				'id' => $this->elementId('Dublin Core','Source'),
				'modifications'=>array('highlight','noaddinput'),
			),
			array(
				'id' => $this->elementId('Dublin Core','Date'),
				'modifications'=>array('highlight','noaddinput','nohtml'),
			),
			array(
				'id' => $this->elementId('Dublin Core','Rights'),
				'modifications'=>array('highlight','noaddinput'),
			),
		);
	}
	private function cssSelectorsForModification($formElements, $modification, $append = null)
	{
		if(!$modification) return array();
		$elements = array_filter(
			$formElements, 
			function($element) use ($modification){
				return in_array($modification, $element['modifications']);
			});
		if(!$elements) return array();
		if($modification == 'noaddinput'){
			return array_map(
				function($id) use($append){
					return 'div#element-'.$id.' .add-element'.$append;
				}, array_column($elements, 'id'));
		}
		if($modification == 'nohtml'){
			return array_map(
				function($id) use($append){
					return 'div#element-'.$id.' .use-html'.$append;
				}, array_column($elements, 'id'));
		}
		if($modification == 'largertextarea'){
			return array_map(
				function($id) use($append){
					return 'div#element-'.$id.' textarea'.$append;
				}, array_column($elements, 'id'));
		}
		if($modification == 'highlight'){
			return array_map(
				function($id) use($append){
					return '#element-'.$id.$append;
				}, array_column($elements, 'id'));
		}
		return array();
	}
	private function cssDCHelperText(){
		echo <<<CSS
		#dublin-core-description{
			visibility: hidden;
			height: 0;
			width: 0;
			padding: 0;
			margin: 0;
			font-size: 0;
			line-height: 0;
			color: transparent;
			position: absolute;
			pointer-events: none;
		}
		CSS;
	}
	private function cssRecommendedOnly($formElements, $itemTypeId = null){
		$selectors = implode(',', $this->cssSelectorsForModification($formElements, 'highlight', '.field'));
		if($itemTypeId) {
			echo <<<CSS
			/* hide other item types */
			#item-type option:not([value=""],[value="{$itemTypeId}"]){
				display: none;
			}
			CSS;
		}
		echo <<<CSS
		/* hide all but recommended elements */
		#item-type-metadata-metadata #type-metadata-form > div,
		#dublin-core-metadata > .set > .field{
			display: none;
		}
		{$selectors}{
			display: revert !important;
		}
		CSS;
	}
	private function cssHighlight($formElements){
		$selectors = implode(',', $this->cssSelectorsForModification($formElements, 'highlight',' label[id^=label_element]::after'));
		$label = __('Curatescape');
		echo <<<CSS
		/* add Curatescape-recommended to select labels (title attr added via js below) */
		{$selectors}{
			content:'{$label}';
			display: block;
			font-weight: normal;
			width: max-content;
			background-color: #445a66;
			color: #F5f5f5;
			padding: 0 5px;
			margin-bottom: 5px;
			text-transform: uppercase;
			font-size: 0.9rem;
		}
		@media (min-width: 768px) and (max-width: 991px) {
			{$selectors}{
				margin-bottom: 5px;
			}
		}
		CSS;
	}
	private function cssNoHTML($formElements){
		$selectors = implode(',', $this->cssSelectorsForModification($formElements, 'nohtml'));
		echo <<<CSS
		/* disable html for select elements */
		{$selectors}{
			opacity: 0;
			cursor: not-allowed;
			pointer-events: none;
			position: absolute;
		}
		CSS;
	}
	private function cssNoAddInput($formElements){
		$selectors = implode(',',  $this->cssSelectorsForModification($formElements, 'noaddinput'));
		echo <<<CSS
		/* disable add input button for select elements */
		{$selectors}{
			opacity: 0;
			cursor:not-allowed;
			pointer-events: none;
			position: absolute;
		}
		CSS;
	}
	private function cssLargerTextArea($formElements){
		$selectors = implode(',', $this->cssSelectorsForModification($formElements, 'largertextarea'));
		echo <<<CSS
		/* bigger text area for primary text */
		{$selectors}{
			min-height: 40vh;
		}
		CSS;
	}
	private function itemFormMods()
	{
		if(
			!str_contains(current_url(), 'admin/items/edit') &&
			!str_contains(current_url(), 'admin/items/add')
		) return null;

		if(!option('curatescape_form_enforcement')) return null;

		$itemFormElements = $this->itemFormElements();
	?>

	<style>
		<?php 
		echo $this->cssDCHelperText();
		if(option('curatescape_form_recommended_only')){
			echo $this->cssRecommendedOnly($itemFormElements, $this->itemTypeId());
		} else {
			echo $this->cssHighlight($itemFormElements);
		}
		echo $this->cssLargerTextArea($itemFormElements);
		echo $this->cssNoAddInput($itemFormElements);
		echo $this->cssNoHTML($itemFormElements);
		?>
	</style>

	<script defer>
		const message = '<?php echo __('This form has been modified by the %s plugin. Only recommended elements and features are available. Default functionality may be restored in plugin settings by an administator.',_PLUGIN_NAME_);?>';
	
		const addTitleAttribute = ()=>{
			document.querySelectorAll('<?php echo implode(',', $this->cssSelectorsForModification($itemFormElements, 'highlight',' label[id^=label_element]'));?>').forEach(el=>{
				if(typeof el.attributes.title == 'undefined'){
					el.setAttribute('title','<?php echo __('This element is recommended for %1s content.', _PLUGIN_NAME_);?>');
				}
			});
		} 
		const selectItemType = (value)=>{
			let typeSelect = document.querySelector('select#item-type');
			let changeEvent = new Event('change', { bubbles: true });
			setTimeout(()=>{
				if(typeSelect.value !== value){
					typeSelect.value = value;
					typeSelect.dispatchEvent(changeEvent)
				}
			},300)
		}
		const itemTypeFormCallback = (mutationList, observeItemType) => {
			for (const mutation of mutationList) {
				<?php if(!option('curatescape_form_recommended_only')):?>
				if (mutation.type === "childList") {
					addTitleAttribute();
				}
				<?php endif;?>
				<?php if(option('curatescape_form_recommended_only')):?>
				if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
					if(mutation.target.style.display !== 'none' && mutation.target.style.visibility !== 'hidden'){
						selectItemType(<?php echo $this->itemTypeId();?>);
						observeItemType.disconnect();
					}
				}
				<?php endif;?>
			}
		}
		const modifySetDescription = ()=>{
			let description = document.querySelector('#dublin-core-description');
			description.innerHTML = message; // visually hidden via css
		}
		addEventListener("DOMContentLoaded", (e) => {
			const observeItemType = new MutationObserver(itemTypeFormCallback);
			<?php if(!option('curatescape_form_recommended_only')):?>
			addTitleAttribute(); // repeat with each change of the item type form
			<?php endif;?>
			<?php if(option('curatescape_form_recommended_only')):?>
			modifySetDescription();
			<?php endif;?>
			observeItemType.observe(document.getElementById("item-type-metadata-metadata"),{
				childList: true,
				subtree: true,
				attributes: true, 
				attributeFilter: ['style'],
			});
		});
		console.info(message);
	</script>

	<?php
	}
	
	private function fileFormMods()
	{
		if( !str_contains(current_url(), 'admin/files/edit') ) return null;
		if(!option('curatescape_form_enforcement')) return null;

		$fileFormElements = $this->fileFormElements();
	?>
	
	<style>
		<?php 
		echo $this->cssDCHelperText();
		if(option('curatescape_form_recommended_only')){
			echo $this->cssRecommendedOnly($fileFormElements);
		} else {
			echo $this->cssHighlight($fileFormElements);
		}
		echo $this->cssLargerTextArea($fileFormElements);
		echo $this->cssNoAddInput($fileFormElements);
		echo $this->cssNoHTML($fileFormElements);
		?>
	</style>
	
	<script defer>
		const message = '<?php echo __('This form has been modified by the %s plugin. Only recommended elements and features are available. Default functionality may be restored in plugin settings by an administator.',_PLUGIN_NAME_);?>';
	
		const addTitleAttribute = ()=>{
			document.querySelectorAll('<?php echo implode(',', $this->cssSelectorsForModification($fileFormElements, 'highlight',' label[id^=label_element]'));?>').forEach(el=>{
				if(typeof el.attributes.title == 'undefined'){
					el.setAttribute('title','<?php echo __('This element is recommended for %1s content.', _PLUGIN_NAME_);?>');
				}
			});
		} 
		const modifySetDescription = ()=>{
			let description = document.querySelector('#dublin-core-description');
			description.innerHTML = message; // visually hidden via css
		} 
		addEventListener("DOMContentLoaded", (e) => {
			modifySetDescription();
			<?php if(!option('curatescape_form_recommended_only')):?>
			addTitleAttribute();
			<?php endif;?>
			console.info(message);
		})
		
	</script>
	
	<?php
	}
	private function lockItemTypeForm()
	{
		if(!str_contains(current_url(), 'admin/item-types/edit')) return null;
	?>

	<script defer>
		document.addEventListener('DOMContentLoaded', ()=>{
			const requiredItemTypeName = '<?php echo _CURATESCAPE_ITEM_TYPE_NAME_;?>';
			let input = document.querySelector("#itemtypes_name");
			if(requiredItemTypeName == input.value){
				input.style.pointerEvents = 'none';
				const deleteItemTypeBtn = document.querySelector("#save .delete-confirm");
				if(deleteItemTypeBtn){
					deleteItemTypeBtn.style.pointerEvents = 'none';
				}
				const AddElementBtn = document.querySelector('#add-element');
				if(AddElementBtn){
					AddElementBtn.style.pointerEvents = 'none';
				}
				const deleteElementBtns = document.querySelectorAll('.delete-element, .delete-drawer');
				if(deleteElementBtns.length){
					deleteElementBtns.forEach((btn)=>{
						btn.style.pointerEvents = 'none';
					})
				}
				console.info('<?php echo __('Editing or deleting the contents of the "%1s" Item Type is disabled while the %2s plugin is active. You may still use this page to edit the Item Type description and change the order of Elements. To change the display name for the Item Type, use the plugin settings at %3s.', _CURATESCAPE_ITEM_TYPE_NAME_, _PLUGIN_NAME_, WEB_ROOT.'/admin/plugins/config?name='._PLUGIN_NAME_);?>');
			}
		})
	</script>

	<?php
	}
	private function elementId($set = null, $element = null)
	{
		if(element_exists($set,$element)){
			$elementObj= get_record('Element',array('element_set_name' => $set, 'name'=>$element));
			return $elementObj->id;
		}
	}
}