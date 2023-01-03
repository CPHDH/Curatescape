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
	
	$copyLink=__('Copy Activation Link');
	$copied=__('Copied!');
	$inactive_users_helper = get_option('cah_inactive_users_helper'); 
	if(is_current_url('/admin/users') && $inactive_users_helper){
		$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		$host = $_SERVER['HTTP_HOST'];
		$user_activations = array();
		$u = get_records('User',array(
			'active'=>'0'),false);
		foreach($u as $user){
			$ua = get_record('UsersActivations',array(
				'user_id'=>$user['id'],
				'sort_field'=>'added',
				'sort_dir'=>'d'));
			if($ua){
				$user_activations[] = array('id'=>$user['id'],'url'=>$protocol.'://'.$host.'/admin/users/activate?u='.$ua['url']);
			}
			
		}
		$inactive_users = json_encode($user_activations);
	}

?>
<script>
	// Plugin Options
	var cah_inactive_users = <?php echo isset($inactive_users) ? $inactive_users : json_encode([]);?>;
	var cah_enable_item_file_tab_notes = <?php echo get_option('cah_enable_item_file_tab_notes');?>;
	var cah_enable_item_file_toggle_dc = <?php echo get_option('cah_enable_item_file_toggle_dc');?>;
	var cah_enable_dashboard_components = <?php echo get_option('cah_enable_dashboard_components');?>;
	var cah_enable_dashboard_resources = <?php echo get_option('cah_enable_dashboard_resources');?>;
	var cah_enable_dashboard_stats = <?php echo get_option('cah_enable_dashboard_stats');?>;
	var cah_enable_file_edit_links= <?php echo get_option('cah_enable_file_edit_links');?>;
	var cah_theme_options_accordion= <?php echo get_option('cah_theme_options_accordion');?>;
	// Dashboard
	if(cah_enable_dashboard_stats==1) jQuery('body.index #stats').append('<div id="file_stats"><p>'+'<?php echo cah_get_file_info();?>'+'</p><div>');
	if(cah_enable_dashboard_components==1) jQuery('body.index .panels').prepend('<?php echo cah_components_guide();?>');
	if(cah_enable_dashboard_resources==1) jQuery('body.index .panels').prepend('<?php echo cah_resources_guide();?>');
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
	if(cah_enable_file_edit_links){
		// file edit links
		jQuery('.admin-thumb.panel a').each(function(){
			var href = jQuery(this).attr('href').replace('show','edit');
			jQuery(this).parentsUntil('#item-images').append('<a target="_blank" class="cah-file-edit" href="'+href+'"><?php echo $edit_file;?></a>');
		});		
	}
	
	// add links to copy user activation links for inactive users
	if(cah_inactive_users.length){
		cah_inactive_users.forEach((user)=>{
			let user_delete_button = jQuery('.inactive li a[href="/admin/users/delete-confirm/'+user['id']+'"]');
			if(user_delete_button){
				user_delete_button = user_delete_button.parent();
				let li = document.createElement('li');
				li.setAttribute('class','activation-link');
				let confirmation = document.createElement('i');
				confirmation.setAttribute('class','fa fa-check-circle');
				confirmation.setAttribute('title','<?php echo $copied;?>');
				confirmation.style.opacity = 0;
				confirmation.style.paddingLeft = '.25em';
				let link = document.createElement('a');
				link.setAttribute('href',user['url']);
				link.setAttribute('data-href',user['url']);
				link.innerText = '<?php echo $copyLink;?>';
				link.addEventListener('click',(e)=>{
					e.preventDefault();
					if(navigator && navigator.clipboard && navigator.clipboard.writeText){
						navigator.clipboard.writeText(e.target.dataset.href);
						confirmation.style.transition = 'none';
						confirmation.style.opacity = 1;
						setTimeout(()=>{
							confirmation.style.transition = 'opacity .15s linear'
							confirmation.style.opacity = 0;
						},1500)
					}else{
						alert(e.target.dataset.href);
					}
					
				})
				li.appendChild(link)
				li.appendChild(confirmation)
				jQuery(user_delete_button).after(li)
			}
		});
	}
	// Highlight the Curatescape item type in the dropdown
	jQuery('select#item-type option:contains("<?php echo $it_name;?>")').css("background-color","yellow");
</script>
