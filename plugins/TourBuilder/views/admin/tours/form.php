<?php echo js_tag('vendor/jquery-ui');?>
<?php echo js_tag('jquery.ui.touch-punch.min');?>
<?php $svg_icon='<svg id="drag" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 325.12 663" width="9px" height="16px"><defs><style>.cls-1{fill:#fff;}.cls-2{fill:#bbb;}</style></defs><title>drag_icon</title><g id="shadow"><path class="cls-1" d="M7.12,559c3-9.33,7.88-17.46,17-21.66a40.59,40.59,0,0,1,15.78-3.68c21.13-.41,42.28-.23,63.42-.15,19,.07,32.67,13.68,32.78,32.81.12,20.64,0,41.29,0,61.93,0,18.13-9.12,29.67-26.77,34a4.81,4.81,0,0,0-1.23.75h-73c-14.42-3.19-23.71-11.9-28-26Z"/><path class="cls-1" d="M7.12,384c1.71-3.75,2.94-7.84,5.24-11.18,6.44-9.36,15.41-14.37,27-14.34,21.47.06,42.95,0,64.42,0,18.12.06,32.08,13.64,32.27,31.75q.34,32.71,0,65.42a31.66,31.66,0,0,1-31.78,31.77q-32.71.29-65.42,0c-14.51-.11-26.12-9-30.62-22.8-.3-.91-.75-1.77-1.13-2.66Q7.12,423,7.12,384Z"/><path class="cls-1" d="M7.12,208c1.95-4,3.42-8.31,5.94-11.89,6.48-9.21,15.71-13.58,27-13.55,22,.06,44-.23,65.92.4,17,.49,29.9,14.09,30.11,31.16q.4,33,0,65.94c-.19,17.76-14,31.51-31.91,31.72-21.81.26-43.63.22-65.44,0-14.34-.14-26.21-9.39-30.5-23.06-.29-.93-.72-1.81-1.09-2.71Q7.12,247,7.12,208Z"/><path class="cls-1" d="M111.12,7c2.39,1,4.88,1.88,7.15,3.14C129.69,16.44,136,26,136.09,39.21q.18,32.22,0,64.45C136,122.29,122,135.95,103.28,136q-31.23.07-62.45,0c-16.31,0-27.26-7.88-32.71-23.42a8.13,8.13,0,0,0-1-1.57q0-39.5,0-79c1.68-3.53,3.07-7.22,5.08-10.54C16.78,13.9,24.13,10,32.12,7Z"/><path class="cls-1" d="M223.12,663c-3.64-1.54-7.56-2.64-10.88-4.7-10.41-6.48-15.58-16.12-15.57-28.37,0-22.3-.17-44.6.41-66.88.39-15.28,13.79-28.63,29.08-28.95q34.93-.72,69.88,0c12.84.25,21.56,7.67,27.15,18.95.65,1.31,1.28,2.64,1.92,4v83c-3.13,4.74-5.52,10.3-9.59,14-4.3,3.92-10.2,6.07-15.41,9Z"/><path class="cls-1" d="M325.12,113c-3.72,5.4-6.84,11.35-11.31,16-4.94,5.19-11.82,7.16-19.13,7.14-22.31,0-44.61.07-66.92,0A31.07,31.07,0,0,1,197,105.2c-.11-21.64,0-43.28,0-64.92,0-16.6,9.32-28.45,25.41-32.43A9.32,9.32,0,0,0,224.12,7h75c2.44.9,4.92,1.73,7.32,2.72C315.78,13.6,321.11,21.2,325.12,30Z"/><path class="cls-1" d="M325.12,465c-2.5,4.06-4.58,8.46-7.59,12.1a28.42,28.42,0,0,1-22.27,10.69c-22.66.2-45.33.46-68-.07-17.49-.4-30.28-14.44-30.31-32q-.06-32.5,0-65c0-17.85,13.72-31.72,31.58-31.84,21.33-.15,42.66-.06,64,0,15.23,0,25.87,7.28,31.62,21.45a15.25,15.25,0,0,0,1,1.71Z"/><path class="cls-1" d="M325.12,289c-2.77,4.47-5,9.44-8.44,13.3C310.79,308.9,303,312,294,312c-21.8-.06-43.61,0-65.41,0A31.32,31.32,0,0,1,197,280.29q-.09-32.71,0-65.41c0-18,14-31.81,32.06-31.86,21.14-.06,42.28,0,63.42,0,15.18,0,25.77,7.16,31.68,21.35a12.46,12.46,0,0,0,1,1.65Z"/></g><g id="top"><path class="cls-2" d="M0,552c3-9.33,7.88-17.46,17-21.66a40.59,40.59,0,0,1,15.78-3.68c21.13-.41,42.28-.23,63.42-.15,19,.07,32.67,13.68,32.78,32.81.12,20.64,0,41.29,0,61.93,0,18.13-9.12,29.67-26.77,34A4.81,4.81,0,0,0,101,656H28c-14.42-3.19-23.71-11.9-28-26Z"/><path class="cls-2" d="M0,377c1.71-3.75,2.94-7.84,5.24-11.18,6.44-9.36,15.41-14.37,27-14.34,21.47.06,42.95,0,64.42,0,18.12.06,32.08,13.64,32.27,31.75q.34,32.71,0,65.42a31.66,31.66,0,0,1-31.78,31.77q-32.71.29-65.42,0c-14.51-.11-26.12-9-30.62-22.8C.83,456.75.38,455.89,0,455Q0,416,0,377Z"/><path class="cls-2" d="M0,201c1.95-4,3.42-8.31,5.94-11.89,6.48-9.21,15.71-13.58,27-13.55,22,.06,44-.23,65.92.4,17,.49,29.9,14.09,30.11,31.16q.4,33,0,65.94c-.19,17.76-14,31.51-31.91,31.72-21.81.26-43.63.22-65.44,0-14.34-.14-26.21-9.39-30.5-23.06C.8,280.78.37,279.9,0,279Q0,240,0,201Z"/><path class="cls-2" d="M104,0c2.39,1,4.88,1.88,7.15,3.14C122.57,9.44,128.89,19,129,32.21q.18,32.22,0,64.45c-.13,18.63-14.1,32.29-32.8,32.33q-31.23.07-62.45,0C17.4,129,6.45,121.12,1,105.57A8.13,8.13,0,0,0,0,104Q0,64.5,0,25c1.68-3.53,3.07-7.22,5.08-10.54C9.65,6.9,17,3,25,0Z"/><path class="cls-2" d="M217,656c-3.64-1.54-7.56-2.64-10.88-4.7-10.41-6.48-15.58-16.12-15.57-28.37,0-22.3-.17-44.6.41-66.88.39-15.28,13.79-28.63,29.08-28.95q34.93-.72,69.88,0c12.84.25,21.56,7.67,27.15,18.95.65,1.31,1.28,2.64,1.92,4v83c-3.13,4.74-5.52,10.3-9.59,14-4.3,3.92-10.2,6.07-15.41,9Z"/><path class="cls-2" d="M319,106c-3.72,5.4-6.84,11.35-11.31,16-4.94,5.19-11.82,7.16-19.13,7.14-22.31,0-44.61.07-66.92,0A31.07,31.07,0,0,1,190.86,98.2c-.11-21.64,0-43.28,0-64.92,0-16.6,9.32-28.45,25.41-32.43A9.32,9.32,0,0,0,218,0h75c2.44.9,4.92,1.73,7.32,2.72C309.66,6.6,315,14.2,319,23Z"/><path class="cls-2" d="M319,458c-2.5,4.06-4.58,8.46-7.59,12.1a28.42,28.42,0,0,1-22.27,10.69c-22.66.2-45.33.46-68-.07-17.49-.4-30.28-14.44-30.31-32q-.06-32.5,0-65c0-17.85,13.72-31.72,31.58-31.84,21.33-.15,42.66-.06,64,0,15.23,0,25.87,7.28,31.62,21.45a15.25,15.25,0,0,0,1,1.71Z"/><path class="cls-2" d="M319,282c-2.77,4.47-5,9.44-8.44,13.3-5.89,6.6-13.68,9.74-22.68,9.72-21.8-.06-43.61,0-65.41,0a31.32,31.32,0,0,1-31.61-31.7q-.09-32.71,0-65.41c0-18,14-31.81,32.06-31.86,21.14-.06,42.28,0,63.42,0,15.18,0,25.77,7.16,31.68,21.35a12.46,12.46,0,0,0,1,1.65Z"/></g></svg>';
?>
<section class="seven columns alpha" id="edit-form">

  <div id="tour-metadata">
	<div class="seven columns alpha" id="form-data">
	
		<fieldset>
			<div class="field">
				<div class="two columns alpha">
				  <?php echo $this->formLabel( 'title', __('Title') ); ?>
				</div>
				<div class="five columns omega">
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
				  <p class="explanation"><?php echo __('OPTIONAL: The name of the person(s) or organization responsible for the content of the tour.');?></p>
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
				  <p class="explanation"><?php echo __('OPTIONAL: Add postscript text to the end of the tour, for example, to thank a sponsor or add directional information.');?></p>
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
		  minLength: 2,
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
		  return jQuery( "<li>" )
		    .append( "<div>" + item.label + "</div>" )
		    .appendTo( ul );
		};
				
	});
</script>
