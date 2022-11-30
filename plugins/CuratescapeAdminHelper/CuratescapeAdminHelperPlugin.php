<?php

include dirname(__FILE__) . '/helpers.php';

class CuratescapeAdminHelperPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'admin_head',
        'admin_footer',
        'config',
        'config_form',
        'uninstall',
        'upgrade',
    );

    protected $_options = array(
        'cah_enable_dashboard_stats' => 1,
        'cah_enable_dashboard_resources' => 1,
        'cah_enable_dashboard_components' => 1,
        'cah_enable_item_file_tab_notes' => 1,
        'cah_enable_item_file_toggle_dc' => 1,
        'cah_enable_file_edit_links' => 1,
        'cah_theme_options_accordion'=>1,
        'cah_hide_add_input_where_unsupported'=>1,
        'cah_hide_html_checkbox_where_unsupported'=>1,
        'cah_inactive_users_helper'=>1
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
        set_option('cah_hide_add_input_where_unsupported', (int)(boolean)$_POST['cah_hide_add_input_where_unsupported']);
        set_option('cah_hide_html_checkbox_where_unsupported', (int)(boolean)$_POST['cah_hide_html_checkbox_where_unsupported']);
        set_option('cah_inactive_users_helper', (int)(boolean)$_POST['cah_inactive_users_helper']);
    }

    public function hookConfigForm()
    {
        // config form
        require dirname(__FILE__) . '/config_form.php';
    }

        
    public function hookAdminHead()
    {
        // header scripts: admin styles
        require dirname(__FILE__) . '/functions/admin_head.php';
    }

    public function hookAdminFooter()
    {
        // footer scripts: admin dashboard enhancements
        require dirname(__FILE__) . '/functions/admin_footer.php';
    }

    public function hookInstall()
    {
        // install scripts: plugin options
        $this->_installOptions();
        
        // install scripts: create custom item type and elements
        require dirname(__FILE__) . '/functions/install.php';
    }
    
    public function hookUpgrade($args)
    {
        if (version_compare($args['old_version'], '1.1', '<')) {
            set_option('cah_hide_add_input_where_unsupported', 1);
        }
        if (version_compare($args['old_version'], '1.2', '<')) {
            set_option('cah_hide_html_checkbox_where_unsupported', 1);
        }
        if (version_compare($args['old_version'], '1.6', '<')) {
            set_option('cah_inactive_users_helper', 1);
        }
    }
    
    public function hookUninstall()
    {
        $this->_uninstallOptions();
    }
}
