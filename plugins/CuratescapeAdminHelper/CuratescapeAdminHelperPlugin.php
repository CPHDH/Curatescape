<?php

include dirname(__FILE__) . '/helpers.php';

class CuratescapeAdminHelperPlugin extends Omeka_Plugin_AbstractPlugin
{	
	
    protected $_hooks = array(
    	'admin_items_batch_edit_form', 
    	'items_batch_edit_custom',
    	'install',
    	'admin_head',
    	'admin_footer',
    	'config',
    	'config_form',
    	'uninstall',
    	);

    protected $_options = array(
        'cah_enable_dashboard_stats' => 1,
        'cah_enable_dashboard_resources' => 1,
        'cah_enable_dashboard_components' => 1,
        'cah_enable_item_file_tab_notes' => 1,
        'cah_enable_item_file_toggle_dc' => 1,
        'cah_enable_file_edit_links' => 1,
        'cah_theme_options_accordion'=>1,
        'cah_theme_options_batch_convert'=>0,
    );
       
        
    public function hookConfig()
    {
		// config form save
        set_option('cah_enable_dashboard_stats', (int)(boolean)$_POST['cah_enable_dashboard_stats']);
        set_option('cah_enable_dashboard_resources', (int)(boolean)$_POST['cah_enable_dashboard_resources']); 
        set_option('cah_enable_dashboard_components', (int)(boolean)$_POST['cah_enable_dashboard_components']);
        set_option('cah_enable_item_file_tab_notes', (int)(boolean)$_POST['cah_enable_item_file_tab_notes']);
        set_option('cah_enable_item_file_toggle_dc', (int)(boolean)$_POST['cah_enable_item_file_toggle_dc']);
        set_option('cah_enable_file_edit_links', (int)(boolean)$_POST['cah_enable_file_edit_links']);  
        set_option('cah_theme_options_accordion', (int)(boolean)$_POST['cah_theme_options_accordion']);
        set_option('cah_theme_options_batch_convert', (int)(boolean)$_POST['cah_theme_options_batch_convert']);    
    }

    public function hookConfigForm()
    {
        // config form
        require dirname(__FILE__) . '/config_form.php';
        
    }	

        
	public function hookAdminHead(){
		
		// header scripts: admin styles
		require dirname(__FILE__) . '/functions/admin_head.php';
		
	}

	public function hookAdminFooter(){
		
		// footer scripts: admin dashboard enhancements
		require dirname(__FILE__) . '/functions/admin_footer.php';
		
	}

    public function hookInstall(){  
		
		// install scripts: plugin options 
		$this->_installOptions();
		
		// install scripts: create custom item type and elements
		require dirname(__FILE__) . '/functions/install.php';
		
	}
    
    public function hookAdminItemsBatchEditForm(){
    	
    	if(get_option('cah_theme_options_batch_convert')==1){
	    	// extend batch edit form: add option to convert to custom item type
	    	require dirname(__FILE__) . '/functions/admin_items_batch_edit_form.php';
    	}
    
    }
    
    public function hookItemsBatchEditCustom($args){
		
		if(get_option('cah_theme_options_batch_convert')==1){
			// save batch edit form: convert item to custom item type and cross-walk elements
	    	require dirname(__FILE__) . '/functions/items_batch_edit_custom.php';
    	}
    }
    
    public function hookUninstall(){
	    
	    $this->_uninstallOptions();	
    
    }
}