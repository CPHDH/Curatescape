<?php 
$icons = array(
	'drag'	=>file_get_contents(TOURBUILDER_PLUGIN_DIR."/views/admin/images/drag.svg"),
	'remove'=>file_get_contents(TOURBUILDER_PLUGIN_DIR."/views/admin/images/trash.svg"),
	'edit'=>file_get_contents(TOURBUILDER_PLUGIN_DIR."/views/admin/images/edit.svg"),
);
$strings = array(
	'label_subtitle'		=>__('Custom Subtitle (optional)'),
	'placeholder_subtitle'	=>__('Leave blank to use default subtitle'),
	'label_text'			=>__('Custom Text (optional)'),
	'placeholder_text'		=>__('Leave blank to use default text'),
	'title_remove'			=>__('Remove'),
	'title_edit'			=>__('Edit'),
);
?>

<section class="seven columns alpha" id="edit-form">
	<div id="tour-metadata">
		<div class="columns alpha" id="form-data">
			<fieldset>
				<div class="field">
					<div class="two columns alpha">
				  	<?php echo $this->formLabel( 'title', __('Title') ); ?>
					</div>
					<div class="five columns omega inputs">
						<?php echo $this->formText( 'title', $tour->title ); ?>
						<p class="explanation"><?php echo __('A title for the tour.');?></p>
					</div>
				</div>
				
				<div class="field">
					<div class="two columns alpha">
						<?php echo $this->formLabel( 'credits', __('Credits') ); ?>
					</div>
					<div class="five columns omega inputs">
						<?php echo $this->formText( 'credits', $tour->credits ); ?>
						<p class="explanation"><?php echo __('Optional: The name of the person(s) or organization responsible for the content of the tour.');?></p>
					</div>
				</div>
	
				<div class="field">
					<div class="two columns alpha">
				  	<?php echo $this->formLabel( 'description', __('Description') ); ?>
					</div>
					<div class="five columns omega inputs">
						<?php echo $this->formTextarea( 'description', $tour->description,array( 'rows' => 12, 'cols' => '40' ) ); ?>
						<p class="explanation"><?php echo __('The main text of the tour.');?></p>
					</div>
				</div>
	
				<div class="field">
					<div class="two columns alpha">
				  	<?php echo $this->formLabel( 'postscript_text', __('Postscript Text') ); ?>
					</div>
					<div class="five columns omega inputs">
						<?php echo $this->formTextarea( 'postscript_text', $tour->postscript_text,array( 'rows' => 3, 'cols' => '40' )  ); ?>
						<p class="explanation"><?php echo __('Optional: Add postscript text to the end of the tour, for example, to thank a sponsor or add directional information.');?></p>
					</div>
				</div>
				
				<div class="field">
					<div class="two columns alpha">
						<?php echo $this->formLabel('tags', __('Tags')); ?>
					</div>
					<div class="five columns omega inputs">
						<?php $tourTagList = join(', ', pluck('name', $tour->Tags)); ?>
						<?php echo $this->formText('tags', $tourTagList); ?>
						<p class="explanation"><?php echo __('Optional: Add tags, separated by a comma.');?></p>
					</div>
				</div>
			</fieldset>
	
			<fieldset id="tour-items-picker">
				<div class="field">
					<div class="tour_item_ids hidden">
						<?php echo $this->formText( 'tour_item_ids', null ); ?>
					</div>
				</div>
	
				<h2><?php echo __('Tour Items');?></h2>
				<p><?php echo __('Search for items by title to add to tour. Drag and drop to change order.');?></p>
	
				<div class="input-container">
					<input type="search" id="tour-item-search" placeholder="Search by title..." onkeydown="if (event.keyCode == 13) return false"/>
				</div>
	
				<ul id="sortable">
					<?php if($tour->id){
						$tourItems = $tour->getItems();
						foreach($tourItems as $ti){
							$custom=$tour->getTourItem($ti->id);
							$html  = '<li data-id="'.$ti->id.'" class="ui-state-highlight"><div class="item-primary"><div class="drag">'.$icons['drag'].'</div><span class="title"><a href="/items/show/'.$ti->id.'" target="_blank">'.metadata($ti,array('Dublin Core','Title')).'</a></span><div class="edit" onclick="editTourItem(this)" tabindex="0" aria-role="button" title="'.$strings['title_edit'].'">'.$icons['edit'].'</div><div class="remove" tabindex="0" aria-role="button" title="'.$strings['title_remove'].'">'.$icons['remove'].'</div></div><div class="item-secondary" hidden><div class="editable"><label for="ti_sub_'.$ti->id.'">'.$strings['label_subtitle'].'</label><input id="ti_sub_'.$ti->id.'" name="ti_sub_'.$ti->id.'" type="text" placeholder="'.$strings['placeholder_subtitle'].'" value="'.$custom->subtitle.'"><label for="ti_text_'.$ti->id.'">'.$strings['label_text'].'</label><textarea id="ti_text_'.$ti->id.'" name="ti_text_'.$ti->id.'" rows="5" placeholder="'.$strings['placeholder_text'].'">'.$custom->text.'</textarea></div></div></li>';
							echo $html;
						}
					} ?>
				</ul>
			</fieldset>
	</div>
</div>

</section>

<!-- Items Selection -->
<script>
	const editTourItem = (t)=>{
		t.parentElement.nextSibling.toggleAttribute('hidden')
	}

	var allItems=<?php echo availableItemsJSON();?>;
	var svg_icon='<?php echo $icons['drag'];?>';
	jQuery('#tour-item-search').on('focus', function() {
		// give user some vertical space for autosuggest dropdown
		jQuery("html, body").animate({ scrollTop: jQuery('#tour-items-picker').position().top }, 'slow');
	});
	jQuery( function() {
		jQuery.formCanSubmit = false;
		var tourItems=_itemsInTour();
		jQuery('#tour_item_ids').val(tourItems);
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
		function addItem( label, id, subtitle, text ) {
			if(jQuery.inArray(id, _itemsInTour(),0) >= 0){
				alert('The item "' +label+ '" has already been added to the tour.');
			}else{
				jQuery( '<li data-id="' + id + '" class="ui-state-highlight">' ).html( '<div class="item-primary">'+'<div class="drag">'+'<?php echo $icons['drag'];?>'+'</div><span class="title"><a href="/items/show/'+id+'" target="_blank">'+label + '</a></span><div class="edit" onclick="editTourItem(this)" tabindex="0" aria-role="button" title="<?php echo $strings['title_edit'];?>">'+'<?php echo $icons['edit'];?>'+'</div><div class="remove" tabindex="0" aria-role="button" title="<?php echo $strings['title_remove'];?>">'+'<?php echo $icons['remove'];?>'+'</div></div><div class="item-secondary" hidden><div class="editable"><label for="ti_sub_' + id + '"><?php echo $strings['label_subtitle'];?></label><input id="ti_sub_' + id + '" name="ti_sub_' + id + '" type="text" placeholder="<?php echo $strings['placeholder_subtitle'];?>" value=""><label for="ti_text_' + id + '"><?php echo $strings['label_text'];?></label><textarea id="ti_text_' + id + '" name="ti_text_' + id + '" rows="5" placeholder="<?php echo $strings['placeholder_text'];?>"></textarea></div></div>' ).prependTo( "#sortable" );
				jQuery( "#sortable" ).scrollTop( 0 );
				// update list on add
				jQuery(document).trigger('tourItemsUpdated');
			}
		}
		jQuery( "#tour-item-search" ).autocomplete({
			minLength: 3,
			source: allItems,
			select: function( event, ui ) {
				addItem( ui.item.label,ui.item.id, ui.item.subtitle, ui.item.text);
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