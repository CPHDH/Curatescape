<?php
	$it_info=cah_item_type();
	$it_name=$it_info['name'];
	$default_text_item=__("Reveal unused Dublin Core fields");
	$default_text_item_hide=__("Hide unused Dublin Core fields");
	$default_text_file=__("Reveal additional Dublin Core fields");
	$default_text_file_hide=__("Hide additional Dublin Core fields");	
	$edit_file=__('Edit File');
	$html_warning=__('HTML is not recommended in this field when using the Curatescape theme. <a title="View the Curatescape Admin Helper plugin settings" href="/admin/plugins/config?name=CuratescapeAdminHelper">Disable this warning.</a>');
	$howto=__('How-to');
?>
<script>
	
	// Plugin Options
	var cah_enable_item_file_tab_notes = <?php echo get_option('cah_enable_item_file_tab_notes');?>;
	var cah_enable_item_file_toggle_dc = <?php echo get_option('cah_enable_item_file_toggle_dc');?>;
	var cah_enable_dashboard_components = <?php echo get_option('cah_enable_dashboard_components');?>;
	var cah_enable_dashboard_resources = <?php echo get_option('cah_enable_dashboard_resources');?>;
	var cah_enable_dashboard_stats = <?php echo get_option('cah_enable_dashboard_stats');?>;
	var cah_enable_file_edit_links= <?php echo get_option('cah_enable_file_edit_links');?>;
	var cah_theme_options_accordion= <?php echo get_option('cah_theme_options_accordion');?>;
	var cah_hide_add_input_where_unsupported = <?php echo get_option('cah_hide_add_input_where_unsupported');?>;
	var cah_hide_html_checkbox_where_unsupported = <?php echo get_option('cah_hide_html_checkbox_where_unsupported');?>;
		
	// Dashboard
	var stats = jQuery('body.index #stats');
	if(cah_enable_dashboard_stats==1) stats.append('<div id="file_stats"><p>'+'<?php echo cah_get_file_info();?>'+'</p><div>');
	if(cah_enable_dashboard_components==1) stats.after('<?php echo cah_components_guide();?>');
	if(cah_enable_dashboard_resources==1) stats.after('<?php echo cah_resources_guide();?>');
	
	// Tab text on item and file forms
	if(cah_enable_item_file_tab_notes==1){
		var form_mod_array=<?php echo cah_item_form_helper_text_array();?>;
		jQuery.each(form_mod_array.tabs, function(i,data){
			jQuery(data.insert_point).after('<span class="tab-info"><span class="fa fa-question-circle"></span> <?php echo __('How-to');?></span>'+data.text);
		});
	}

	// Use jQueryUI to create theme options accordion
	function getParameterByName(name) {
	    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
	        results = regex.exec(location.search);
	    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	if( (cah_theme_options_accordion==1) && (getParameterByName('name').includes('curatescape')) ){
			jQuery(".theme-configuration [id^='fieldset-']").each(function(i){
				jQuery(this).children('.field').wrapAll('<div />');
			 });
			jQuery('fieldset').accordion({
				header:'legend',
				collapsible: true,
				active: false,
				heightStyleType: 'content',
				});
			jQuery('fieldset').css('border-bottom','1px solid #ccc');
			jQuery('fieldset').css('width','100%');
			jQuery('.ui-accordion .ui-accordion-content').css('height','auto');
	}
	
	// Re-order and re-style elements for items and files
	if(cah_enable_item_file_toggle_dc==1){
		jQuery('.items #dublin-core-metadata .field,.files #dublin-core-metadata .field').addClass('toggle-me').hide();
		
		jQuery('.items #dublin-core-metadata .element-set-description').after('<div id="dc-reveal" class="items"><?php echo $default_text_item;?></div>');
		jQuery('.files #dublin-core-metadata .element-set-description').after('<div id="dc-reveal" class="files"><?php echo $default_text_file;?></div>');

		
	    jQuery('#dc-reveal.items').click(function(){
	        jQuery('#dublin-core-metadata .field.toggle-me').slideToggle();
	        jQuery(this).html(function(i,text){
		        var default_txt="<?php echo $default_text_item;?>";
		        if( text === default_txt ){
			        return "<?php echo $default_text_item_hide;?>"
			    }else{
				    return default_txt;
			    }
	        });
	    });

	    jQuery('#dc-reveal.files').click(function(){
	        jQuery('#dublin-core-metadata .field.toggle-me').slideToggle();
	        jQuery(this).html(function(i,text){
		        var default_txt="<?php echo $default_text_file;?>";
		        if( text === default_txt ){
			        return "<?php echo $default_text_file_hide;?>"
			    }else{
				    return default_txt;
			    }
	        });
	    });
	    
	    // items
		jQuery.each(form_mod_array.item_fields, function(i,id){
			jQuery('.items #edit-form #element-'+id).addClass('curatescape-recommended');
			jQuery('.items #edit-form #element-'+id).removeClass('toggle-me').insertBefore('#dc-reveal').show();
		});
	
	    // fields
		jQuery.each(form_mod_array.file_fields, function(i,id){
			jQuery('.files #edit-form #element-'+id).addClass('curatescape-recommended');
			jQuery('.files #edit-form #element-'+id).removeClass('toggle-me').insertBefore('#dc-reveal').show();
		});
		
	}
	
	if(cah_hide_add_input_where_unsupported==1){
		// add input
		jQuery('.field button.add-element').hide();
		jQuery.each(form_mod_array.add_input_supported, function(i,id){
			jQuery('.items #edit-form #element-'+id+' button.add-element').addClass('add_input_supported').show();
		});			
	}

	if(cah_hide_html_checkbox_where_unsupported==1){
		var htmlWhitelist = function(){
			// hide all
			jQuery('label.use-html').hide();
			// show supported
			jQuery.each(form_mod_array.use_html_supported, function(i,id){
				jQuery('.items #edit-form #element-'+id+' label.use-html').addClass('use_html_supported').show();
				jQuery('.files #edit-form #element-'+id+' label.use-html').addClass('use_html_supported').show();
			});
			// show warning (and checkbox) if using HTML in unsupported field 
			var conflicts = jQuery("label.use-html").not(".use_html_supported").children('.use-html-checkbox:checked');
			jQuery.each(conflicts, function(i,el){
				jQuery(el).after('<span class="cah-warning"><?php echo $html_warning;?></span>');
				jQuery(el).parent('label').show();
			});
		}
		// on item type form select
		jQuery("select#item-type").on("click",function(){
			setTimeout(function(){
				htmlWhitelist();
			},500);
			
		});
		// on load
		htmlWhitelist();
	}
	
	if(cah_enable_file_edit_links){
		// file edit links
		jQuery('.admin-thumb.panel a').each(function(){
			var href = jQuery(this).attr('href').replace('show','edit');
			jQuery(this).parentsUntil('#item-images').append('<a target="_blank" class="cah-file-edit" href="'+href+'"><?php echo $edit_file;?></a>');
		});		
	}

	// Highlight the Curatescape item type in the dropdown
	jQuery('select#item-type option:contains("<?php echo $it_name;?>")').css("background-color","yellow");
	
</script>
