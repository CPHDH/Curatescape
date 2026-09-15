<?php 
$tour = isset($tour) ? $tour : null; // unset on add, assigned on edit
$tourOrdinal = $tour ? $tour->ordinal : 0;
$tourPublic = $tour ? $tour->public : 0;
$tourFeatured = $tour ? $tour->featured : 0;
$tourId = $tour ? $tour->id : null;
$tourTitle = $tour ? $tour->title : null;
$tourCredits = $tour ? $tour->credits : null;
$tourDescription = $tour ? $tour->description : null;
$tourPostscript = $tour ? $tour->postscript_text : null;
$tourTags = $tour ? join(', ', pluck('name', $tour->Tags)) : null;
$tourItems = $tour ? $tour->getItems() : array(); // ordered by ti.ordinal
$tourItemIds = join(',', pluck('id', $tourItems));
// translatable strings used in html attributes and js strings
$strings = array_map('html_escape', array(
	'label_subtitle' =>__('Custom Subtitle (optional)'),
	'placeholder_subtitle'	=>__('Leave blank to use default subtitle'),
	'label_text' =>__('Custom Text (optional)'),
	'placeholder_text' =>__('Leave blank to use default text'),
	'title_remove' =>__('Remove'),
	'title_edit' =>__('Edit'),
));
function availableTourItemsJSON()
{
	$db = get_db();
	$itemTypeId = itemTypeID();
	if(!$itemTypeId) return json_encode(array());
	$items = $db->getTable( 'Item' )->fetchObjects(
		<<<SQL
		SELECT i.* FROM {$db->prefix}items i
		WHERE i.item_type_id = {$itemTypeId}
		AND EXISTS (SELECT 1 FROM {$db->prefix}locations l WHERE l.item_id = i.id)
		ORDER BY i.modified DESC
		SQL
	);
	$available = array();
	foreach($items as $item) {
		if(!hasLocation($item)) continue; // geolocation v4: key location must be a Point
		$available[] = array(
			'id' => intval($item->id),
			'label' => dc( $item,'Title'),
		);
	}
	return json_encode($available);
}
?>

<?php if (isset($this->csrf)): // add: core's form object ?>
<?php echo $this->csrf; ?>
<?php elseif (isset($this->csrfToken)): // edit: token issued in editAction() ?>
<input type="hidden" name="csrf_token" value="<?php echo html_escape($this->csrfToken); ?>">
<?php endif; ?>
<section class="seven columns alpha" id="edit-form">
	<div id="tour-metadata">
		<div class="columns alpha" id="form-data">
			<fieldset>
				<div class="field">
					<div class="two columns alpha">
						<?php echo $this->formLabel( 'title', __('Title') ); ?>
					</div>
					<div class="five columns omega inputs">
						<?php echo $this->formText( 'title', $tourTitle); ?>
						<p class="explanation"><?php echo __('A title for the tour.');?></p>
					</div>
				</div>

				<div class="field">
					<div class="two columns alpha">
						<?php echo $this->formLabel( 'credits', __('Credits') ); ?>
					</div>
					<div class="five columns omega inputs">
						<?php echo $this->formText( 'credits', $tourCredits ); ?>
						<p class="explanation"><?php echo __('The name of the person(s) or organization responsible for the content of the tour.');?></p>
					</div>
				</div>

				<div class="field">
					<div class="two columns alpha">
						<?php echo $this->formLabel( 'description', __('Description') ); ?>
					</div>
					<div class="five columns omega inputs">
						<?php echo $this->formTextarea( 'description', $tourDescription,
						array( 'rows' => 12, 'cols' => '40' ) ); ?>
						<p class="explanation"><?php echo __('The main text of the tour.');?></p>
					</div>
				</div>

				<div class="field">
					<div class="two columns alpha">
						<?php echo $this->formLabel( 'postscript_text', __('Postscript Text') ); ?>
					</div>
					<div class="five columns omega inputs">
						<?php echo $this->formTextarea( 'postscript_text', $tourPostscript,
						array( 'rows' => 3, 'cols' => '40' ) ); ?>
						<p class="explanation"><?php echo __('Additional text to be displayed after the main tour content.');?></p>
					</div>
				</div>

				<div class="field">
					<div class="two columns alpha">
						<?php echo $this->formLabel('tags', __('Tags')); ?>
					</div>
					<div class="five columns omega inputs">
						<?php echo $this->formText('tags', $tourTags); ?>
						<p class="explanation"><?php echo __('Separate tags with ,');?></p>
					</div>
				</div>
			</fieldset>

			<fieldset id="tour-items-picker">
				<div class="field">
					<div class="tour_item_ids hidden">
						<?php echo $this->formText( 'tour_item_ids', $tourItemIds ); ?>
					</div>
				</div>

				<h2><?php echo __('Tour Items');?></h2>
				<p><?php echo __('Search for items to add to tour (item geolocation is required). Drag and drop to change order.');?></p>

				<div class="input-container">
					<label for="tour-item-search" class="sr-only"><?php echo __('Search for items to add to the tour');?></label>
					<input type="search" id="tour-item-search" placeholder="<?php echo html_escape(__('Search by title...'));?>" onkeydown="if (event.keyCode == 13) return false"/>
				</div>

				<ul id="sortable">
					<?php if($tourId){
						foreach($tourItems as $ti){
							$custom=$tour->getTourItem($ti->id);
							$html = '<li data-id="'.$ti->id.'" class="ui-state-highlight"><div class="item-primary"><div class="drag">'.svg('drag').'</div><span class="title"><a href="'.url('items/show/'.$ti->id).'" target="_blank">'.metadata($ti,array('Dublin Core','Title')).'</a></span><button type="button" class="edit" onclick="editTourItem(this)" aria-label="'.$strings['title_edit'].'" title="'.$strings['title_edit'].'">'.svg('edit').'</button><button type="button" class="remove" aria-label="'.$strings['title_remove'].'" title="'.$strings['title_remove'].'">'.svg('trash').'</button></div><div class="item-secondary" hidden><div class="editable"><label for="ti_sub_'.$ti->id.'">'.$strings['label_subtitle'].'</label><input id="ti_sub_'.$ti->id.'" name="ti_sub_'.$ti->id.'" type="text" placeholder="'.$strings['placeholder_subtitle'].'" value="'.html_escape($custom->subtitle).'"><label for="ti_text_'.$ti->id.'">'.$strings['label_text'].'</label><textarea id="ti_text_'.$ti->id.'" name="ti_text_'.$ti->id.'" rows="5" placeholder="'.$strings['placeholder_text'].'">'.html_escape($custom->text).'</textarea></div></div></li>';
							echo $html;
						}
					} ?>
				</ul>
			</fieldset>
		</div>
	</div>
</section>

<section class="three columns omega">
	<div id="save" class="panel">
		<?php if(!$tourId):?>
			<!-- add -->
			<?php echo $this->formSubmit('submit',__('Add Tour'), 
			array('id' => 'save-changes', 'class' => 'submit big green button')); ?>
		<?php else:?>
			<!-- edit -->
			<?php echo $this->formSubmit( 'submit', __('Save Changes'), 
			array( 'id' => 'save-changes', 'class' => 'submit big green button' )); ?>
			<!-- view -->
			<a href="<?php echo html_escape( public_url( 'tours/show/' . $tour->id ) ); ?>"
			class="big blue button" target="_blank">
			<?php echo __('View Public Page'); ?> <span class="sr-only"><?php echo __('(opens in new tab)'); ?></span>
			</a>
			<!-- delete -->
			<?php if(is_allowed('Curatescape_CuratescapeTours', 'delete')): ?>
				<a href="<?php echo url(array( 'action' => 'delete-confirm','id' => $tour->id), 'tourAction' );?>"
				class="delete-confirm big red button"><?php echo __('Delete'); ?></a>
			<?php endif; ?>
		<?php endif;?>
	</div>
	<?php if (is_allowed('Curatescape_CuratescapeTours','makePublic')): ?>
		<div class="field panel ordinal">
			<?php echo $this->formLabel('ordinal',__('Custom Order')); ?>
			<?php echo $this->formText('ordinal', $tourOrdinal); ?>
			<p class="explanation"><?php echo __('Optional: Enter a number greater than 0 to customize the order of this tour. Enter 0 to use the default order.');?></p>
		</div>
	<?php endif; ?>
	<div id="public-featured">
		<?php if(is_allowed('Curatescape_CuratescapeTours','makePublic')): ?>
			<div class="checkbox">
				<label for="public">
					<?php echo __('Public'); ?>:
				</label>
				<div class="checkbox">
					<?php echo $this->formCheckbox('public',$tourPublic,
					array(),array('1','0'));?>
				</div>
			</div>
		<?php endif; ?>
		<?php if(is_allowed('Curatescape_CuratescapeTours','makeFeatured')): ?>
			<div class="checkbox">
				<label for="featured">
					<?php echo __('Featured'); ?>:
				</label>
				<div class="checkbox">
					<?php echo $this->formCheckbox('featured',$tourFeatured,
					array(),array('1','0'));?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- Items Selection -->
<script>
	const editTourItem = (t)=>{
		t.parentElement.nextElementSibling.toggleAttribute('hidden')
	}
	// labels are html-escaped so they are safe to concatenate into markup below;
	// jquery ui puts item.value in the search input and matches against it, so
	// each item also carries a decoded plain-text copy
	const decodeEntities = (str)=>{
		const el = document.createElement('textarea');
		el.innerHTML = str;
		return el.value;
	}
	var allItems=<?php echo availableTourItemsJSON();?>;
	var svg_icon='<?php echo svg('drag');?>';
	jQuery('#tour-item-search').on('focus', function() {
		// give user some vertical space for autosuggest dropdown
		jQuery("html, body").animate({ scrollTop: jQuery('#tour-items-picker').position().top }, 'slow');
	});
	jQuery( function() {
		jQuery.formCanSubmit = false;
		var tourItems=_itemsInTour();
		jQuery('#tour_item_ids').val(tourItems);
		allItems.forEach((item)=>{ item.value = decodeEntities(item.label); });
		// UI BUTTONS
		(function () {
			var _UIButtons;
			(_UIButtons = function (){
				jQuery('#sortable .remove').on('click',function(){
					jQuery(this).parent().parent().fadeOut(400,function(){
						jQuery(this).remove();
						// update list on remove
						jQuery(document).trigger('tourItemsUpdated');
					});
				});
			})();
			//When list is updated, re-evaluate the list
			jQuery(document).on('tourItemsUpdated',function(e){
				_UIButtons();
				tourItems=_itemsInTour();
				jQuery('#tour_item_ids').val(tourItems);
			});	
		})();
		function _itemsInTour(){
			var inTour = new Array();
			jQuery('#sortable li').each(function(){
				inTour.push(parseInt(jQuery(this).attr('data-id')));
			});
			return inTour;
		}
		// SORTABLE
		jQuery( "#sortable" ).sortable({
			placeholder: "ui-state-highlight",
			stop: function(event,ui){ 
				// update list on drag-end
				jQuery(document).trigger('tourItemsUpdated');
			}
		});
		jQuery( "#sortable" ).disableSelection();
		// AUTOCOMPLETE
		function addItem( label, id ) {
			if(jQuery.inArray(id, _itemsInTour(),0) >= 0){
				alert('The item "' +decodeEntities(label)+ '" has already been added to the tour.');
			}else{
				jQuery( '<li data-id="' + id + '" class="ui-state-highlight">' ).html( '<div class="item-primary">'+'<div class="drag">'+'<?php echo svg('drag');?>'+'</div><span class="title"><a href="<?php echo url('items/show');?>/'+id+'" target="_blank">'+label + '</a></span><button type="button" class="edit" onclick="editTourItem(this)" aria-label="<?php echo $strings['title_edit'];?>" title="<?php echo $strings['title_edit'];?>">'+'<?php echo svg('edit');?>'+'</button><button type="button" class="remove" aria-label="<?php echo $strings['title_remove'];?>" title="<?php echo $strings['title_remove'];?>">'+'<?php echo svg('trash');?>'+'</button></div><div class="item-secondary" hidden><div class="editable"><label for="ti_sub_' + id + '"><?php echo $strings['label_subtitle'];?></label><input id="ti_sub_' + id + '" name="ti_sub_' + id + '" type="text" placeholder="<?php echo $strings['placeholder_subtitle'];?>" value=""><label for="ti_text_' + id + '"><?php echo $strings['label_text'];?></label><textarea id="ti_text_' + id + '" name="ti_text_' + id + '" rows="5" placeholder="<?php echo $strings['placeholder_text'];?>"></textarea></div></div>' ).prependTo( "#sortable" );
				jQuery( "#sortable" ).scrollTop( 0 );
				// update list on add
				jQuery(document).trigger('tourItemsUpdated');
			}
		}
		jQuery( "#tour-item-search" ).autocomplete({
			minLength: 3,
			source: function( request, response ) {
				// skip items that have been added
				const inTour = _itemsInTour();
				// match the decoded text, not the escaped label
				const matcher = new RegExp(jQuery.ui.autocomplete.escapeRegex(request.term), 'i');
				response(allItems.filter((item)=> !inTour.includes(item.id) && matcher.test(item.value)));
			},
			select: function( event, ui ) {
				addItem( ui.item.label, ui.item.id );
				// update list on select
				jQuery(document).trigger('tourItemsUpdated');
				// clear the form
				jQuery( "#tour-item-search" ).val('');
				return false;
			},
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			ul.css('background', '#fafafa');
			return jQuery( '<li>' )
				.append( "<span>" + item.label + "</span>" )
				.appendTo( ul );
		};
	});
</script>

<!-- Tags Auto-complete -->
<?php echo js_tag('items'); ?>
<script>
	jQuery(document).ready(function(){
		Omeka.Items.tagDelimiter = <?php echo js_escape(get_option('tag_delimiter')); ?>;
		Omeka.Items.tagChoices('#tags', <?php echo js_escape(url(array('controller'=>'tags', 'action'=>'autocomplete'), 'default', array(), true)); ?>);
	});
</script>
