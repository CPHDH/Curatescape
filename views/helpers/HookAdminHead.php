<?php
class Curatescape_View_Helper_HookAdminHead extends Zend_View_Helper_Abstract{
	public function HookAdminHead($args){
		$this->itemTypeFormJs();
		$this->itemFormCss();
		$this->adminCss();
		$this->deprecatedCheck();
	}
	private function deprecatedCheck($warnings=array()){
		if(
			(is_current_url('/admin/plugins/') && !is_current_url('/admin/plugins/uninstall/')) ||
			is_current_url('/admin/themes/') ||
			is_current_url('/admin/settings/') ||
			is_current_url('/admin/tours/')
		){
			// @todo: theme deprecation check?
			$deprecatedPlugins = array(
				'CuratescapeJSON',
				'CuratescapeAdminHelper',
				'TourBuilder',
				'SuperRSS',
				'SendToAdminHeader',
				'MobileJSON',
			);
			foreach($deprecatedPlugins as $plugin){
				if(plugin_is_active($plugin)){
					array_push($warnings, __('The %1s plugin is deprecated and replaced by the %2s plugin. Please deactivate and remove the %3s plugin to avoid conflicts.', $plugin, _PLUGIN_NAME_, $plugin));
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
	private function elementId($set=null,$element=null){
		if(element_exists($set,$element)){
			$elementObj= get_record('Element',array('name'=>$element));
			return $elementObj->id;
		}
	}
	private function adminCss(){
		?>
		
		<style>
		:root{
			--notice-bg: #fafad2;
			--notice-text: #9F6D00;
			--notice-text-darker: #390700;
			--notice-decorative: #b8860b;
		}
		.curatescape-panel h3{
			padding-top:12px;
			padding-bottom: 6px;
			border-bottom: 1px solid #e7e7e7;
		}
		.curatescape-panel span.highlight{
			background: var(--notice-bg);
			display: inline-block;
			padding: 0 1lh;
			margin-bottom: 1lh;
		}
		.curatescape-panel svg{
			height: 0.95lh;
			width: 0.95lh;
			vertical-align: text-bottom;
			padding-right: .5lh;
			fill: var(--notice-decorative);
		}
		span.highlight h3{
			color:var(--notice-text);
			border-bottom-color: var(--notice-decorative);
		}
		#content #flash .curatescape-warning{
			background-color: var(--notice-bg);
			color:var(--notice-text-darker);
		}
		</style>
		
		<?php
	}
	private function itemFormCss($largerTextArea = array(), $noHtml=array(), $noAddInput=array(), $labelHighlight=array())
	{
		if(
			!str_contains(current_url(), 'admin/items/edit') &&
			!str_contains(current_url(), 'admin/items/add')
		) return null;
		
		if(!option('curatescape_form_enforcement')) return null;

		$titleId=$this->elementId('Dublin Core','Title');
		$creatorId=$this->elementId('Dublin Core','Creator');
		$subjectId=$this->elementId('Dublin Core','Subject');
		$storyId=$this->elementId('Item Type Metadata','Story');
		$ledeId=$this->elementId('Item Type Metadata','Lede');
		$factoidId=$this->elementId('Item Type Metadata','Factoid');
		$relatedResources=$this->elementId('Item Type Metadata','Related Resources');
		$sponsorId=$this->elementId('Item Type Metadata','Sponsor');
		$streetAddressId=$this->elementId('Item Type Metadata','Street Address');
		$subtitleId=$this->elementId('Item Type Metadata','Subtitle');
		$officialWebsiteId=$this->elementId('Item Type Metadata','Official Website');
		$accessInformationId=$this->elementId('Item Type Metadata','Access Information');
		$largerTextArea = array(
			'div#element-'.$storyId.' textarea',
		);
		$labelHighlight = array(
			'#label_element_'.$titleId,
			'#label_element_'.$creatorId,
			'#label_element_'.$subjectId,
			'#label_element_'.$subtitleId,
			'#label_element_'.$ledeId,
			'#label_element_'.$storyId,
			'#label_element_'.$sponsorId,
			'#label_element_'.$factoidId,
			'#label_element_'.$relatedResources,
			'#label_element_'.$officialWebsiteId,
			'#label_element_'.$streetAddressId,
			'#label_element_'.$accessInformationId,
		);
		$noAddInput = array(
			'div#element-'.$streetAddressId.' .add-element',
			'div#element-'.$titleId.' .add-element',
			'div#element-'.$officialWebsiteId.' .add-element',
			'div#element-'.$subtitleId.' .add-element',
			'div#element-'.$ledeId.' .add-element',
			'div#element-'.$storyId.' .add-element',
			'div#element-'.$sponsorId.' .add-element',
			'div#element-'.$accessInformationId.' .add-element',
		);
		$noHtml = array(
			'div#element-'.$titleId.' .use-html',
			'div#element-'.$creatorId.' .use-html',
			'div#element-'.$subjectId.' .use-html',
			'div#element-'.$streetAddressId.' .use-html',
			'div#element-'.$subtitleId.' .use-html',
		);
	?>

	<style>
		/* add Curatescape-recommended to select labels (title attr added via js below) */
		<?php if(count($labelHighlight)):?>
			<?php echo implode('::after,', $labelHighlight);?>::after{
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
				<?php echo implode('::after,', $labelHighlight);?>::after{
					margin-bottom: 5px;
				}
			}
		<?php endif;?>
		/* bigger text area for Story */
		<?php if(count($largerTextArea)):?>
			<?php echo implode(',', $largerTextArea);?>{
				min-height: 60vh;
			}
		<?php endif;?>
		/* disable add input for select elements */
		<?php if(count($noAddInput)):?>
			<?php echo implode(',', $noAddInput);?>{
				opacity: 0;
				cursor:not-allowed;
				pointer-events: none;
			}
		<?php endif;?>
		/* disable html for select elements */
		<?php if(count($noHtml)):?>
			<?php echo implode(',', $noHtml);?>{
				opacity: 0;
				cursor: not-allowed;
				pointer-events: none;
			}
		<?php endif;?>
	</style>
	
	<script defer>
		<?php if(count($labelHighlight)):?>
			const addTitleAttribute = ()=>{
				document.querySelectorAll('<?php echo implode(',', $labelHighlight);?>').forEach(el=>{
					if(typeof el.attributes.title == 'undefined'){
						el.setAttribute('title','<?php echo __('This element is recommended for %1s content. The form has been modified to enforce the recommended formatting rules for this element. You may turn off this feature in %2s plugin settings.', _PLUGIN_NAME_, _PLUGIN_NAME_);?>');
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
		<?php endif;?>
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
			let value = input.value;
			if(requiredItemTypeName == value){
				input.setAttribute('disabled',true);
				let deleteItemTypeBtn = document.querySelector("#save .delete-confirm");
				if(deleteItemTypeBtn){
					deleteItemTypeBtn.style.pointerEvents = 'none';
				}
				let AddElementBtn = document.querySelector('#add-element');
				if(AddElementBtn){
					AddElementBtn.style.pointerEvents = 'none';
				}
				let deleteElementBtns = document.querySelectorAll('.delete-element');
				if(deleteElementBtns){
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
}