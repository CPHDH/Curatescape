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
		'Geolocation' => '3.4', // @todo: change before release, awaiting PR
	);
	public function HookAdminHead($args){
		$this->adminCss();
		$this->adminJs();
		$this->compatibilityCheck();
		$this->itemFormCss();
		$this->itemTypeFormJs();
	}
	private function modifiedFormElements()
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
	private function cssSelectorsForModification($modification, $append = null)
	{
		if(!$modification) return array();
		$elements = array_filter(
			$this->modifiedFormElements(), 
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
					return '#label_element_'.$id.$append;
				}, array_column($elements, 'id'));
		}
		return array();
	}
	private function adminCss()
	{
		queue_css_file('dashboard', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
		if(is_current_url('/admin/tours/')){
			queue_css_file('tours', 'all', false, 'css', get_plugin_ini(_PLUGIN_NAME_, 'version'));
		}
	}
	private function adminJs()
	{
		if(is_current_url('/admin/tours/')){
			queue_js_file('browse', 'javascripts');
		}
	}
	private function compatibilityCheck($warnings = array())
	{
		if(
			(is_current_url('/admin/plugins/') && !is_current_url('/admin/plugins/uninstall/')) ||
			is_current_url('/admin/themes/') ||
			is_current_url('/admin/settings/') ||
			is_current_url('/admin/tours/')
		){
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
	private function itemFormCss()
	{
		if(
			!str_contains(current_url(), 'admin/items/edit') &&
			!str_contains(current_url(), 'admin/items/add')
		) return null;

		if(!option('curatescape_form_enforcement')) return null;
	?>

	<style>
		/* add Curatescape-recommended to select labels (title attr added via js below) */
		<?php echo implode(',', $this->cssSelectorsForModification('highlight','::after'));?>{
			content:'<?php echo __('Curatescape');?>';
			display: block;
			font-weight: normal;
			width: max-content;
			background-color: #445a66;
			color: #F5f5f5;
			padding: 0 5px;
			text-transform: uppercase;
			font-size: 0.9rem;
		}
		@media (min-width: 768px) and (max-width: 991px) {
			<?php echo implode(',', $this->cssSelectorsForModification('highlight','::after'));?>{
				margin-bottom: 5px;
			}
		}
		/* bigger text area for Story */
		<?php echo implode(',', $this->cssSelectorsForModification('largertextarea'));?>{
			min-height: 60vh;
		}
		/* disable add input for select elements */
		<?php echo implode(',',  $this->cssSelectorsForModification('noaddinput'));?>{
			opacity: 0;
			cursor:not-allowed;
			pointer-events: none;
		}
		/* disable html for select elements */
		<?php echo implode(',', $this->cssSelectorsForModification('nohtml'));?>{
			opacity: 0;
			cursor: not-allowed;
			pointer-events: none;
		}
	</style>

	<script defer>
		const addTitleAttribute = ()=>{
			document.querySelectorAll('<?php echo implode(',', $this->cssSelectorsForModification('highlight'));?>').forEach(el=>{
				if(typeof el.attributes.title == 'undefined'){
					el.setAttribute('title','<?php echo __('This element is recommended for %1s content.', _PLUGIN_NAME_);?>');
				}
			});
		} 
		const itemTypeFormCallback = (mutationList, observer) => {
			for (const mutation of mutationList) {
				if (mutation.type === "childList") {
					addTitleAttribute();
				}
			}
		};
		addEventListener("DOMContentLoaded", (e) => {
			addTitleAttribute(); // repeat with each change of the item type form
			const observer = new MutationObserver(itemTypeFormCallback);
			observer.observe(document.getElementById("item-type-metadata-metadata"),{
				childList: true,
				subtree: true,
			});
		});
		console.info('<?php echo __('The "Add Input" and "Use HTML" buttons for select elements have been disabled by the %1s plugin. This option can be disabled at %2s',_PLUGIN_NAME_, WEB_ROOT.'/admin/plugins/config?name='._PLUGIN_NAME_);?>');
	</script>

	<?php
	}
	private function itemTypeFormJs()
	{
		if(!str_contains(current_url(), 'admin/item-types/edit')) return null;
	?>

	<script defer>
		document.addEventListener('DOMContentLoaded', ()=>{
			const requiredItemTypeName = '<?php echo _CURATESCAPE_ITEM_TYPE_NAME_;?>';
			let input = document.querySelector("#itemtypes_name");
			if(requiredItemTypeName == input.value){
				input.style.pointerEvents = 'none';
				if(deleteItemTypeBtn = document.querySelector("#save .delete-confirm")){
					deleteItemTypeBtn.style.pointerEvents = 'none';
				}
				if(AddElementBtn = document.querySelector('#add-element')){
					AddElementBtn.style.pointerEvents = 'none';
				}
				if(deleteElementBtns = document.querySelectorAll('.delete-element')){
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
			$elementObj= get_record('Element',array('name'=>$element));
			return $elementObj->id;
		}
	}
}