<?php $svg_icon=file_get_contents(TOURBUILDER_PLUGIN_DIR."/views/admin/images/drag_icon.svg");?>
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
			
			<h2>Tour Items</h2>
			<p>Search for items by title to add to tour.</p>
			
			<div class="input-container">
				<input type="search" id="tour-item-search" placeholder="Search by title..." onkeydown="if (event.keyCode == 13) return false"/>
			</div>
			
			<ul id="sortable">
				<?php if($tour->id){
					$tourItems = $tour->getItems();
					foreach($tourItems as $ti){
						$html  = '<li data-id="'.$ti->id.'" class="ui-state-default">';
						$html .= '<span>'.$svg_icon.metadata($ti,array('Dublin Core','Title')).'</span>';
						$html .= '<span class="remove">Remove</span></li>';
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
	var allItems=<?php echo availableItemsJSON();?>;
	var svg_icon='<?php echo $svg_icon;?>';
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
				jQuery('.remove').on('click',function(){
					jQuery(this).parent().fadeOut(400,function(){
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
				alert('The item "' +label+ '" has already been added to the tour.');
			}else{
				jQuery( '<li data-id="' + id + '" class="ui-state-highlight">' ).html( '<span>'+svg_icon+ label + '</span> <span class="remove">Remove</span>' ).prependTo( "#sortable" );
				jQuery( "#sortable" ).scrollTop( 0 );
				// update list on add
				jQuery(document).trigger('tourItemsUpdated');
			}
		}
		jQuery( "#tour-item-search" ).autocomplete({
			minLength: 3,
			source: allItems,
			select: function( event, ui ) {
				addItem( ui.item.label,ui.item.id);
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