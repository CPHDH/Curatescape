<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Geolocation
 */
class Geolocation_IntegrationHelper
{
    const PLUGIN_NAME = 'Geolocation';
    
    public function setUpPlugin()
    {        
        $pluginHelper = new Omeka_Test_Helper_Plugin;
        $this->_addPluginHooksAndFilters($pluginHelper->pluginBroker, self::PLUGIN_NAME);
        $pluginHelper->setUp(self::PLUGIN_NAME);
    }
        
    public function _addPluginHooksAndFilters($pluginBroker, $pluginName)
    {
        // Set the current plugin so the add_plugin_hook function works
        $pluginBroker->setCurrentPluginDirName($pluginName);

        // Add plugin hooks
        add_plugin_hook('install', 'geolocation_install');
        add_plugin_hook('uninstall', 'geolocation_uninstall');
        add_plugin_hook('config_form', 'geolocation_config_form');
        add_plugin_hook('config', 'geolocation_config');
        add_plugin_hook('define_acl', 'geolocation_define_acl');
        add_plugin_hook('define_routes', 'geolocation_add_routes');
        add_plugin_hook('after_save_form_item', 'geolocation_save_location');
        add_plugin_hook('admin_append_to_items_show_secondary', 'geolocation_admin_show_item_map');
        add_plugin_hook('admin_append_to_advanced_search', 'geolocation_admin_append_to_advanced_search');
        add_plugin_hook('public_append_to_advanced_search', 'geolocation_public_append_to_advanced_search');
        add_plugin_hook('item_browse_sql', 'geolocation_item_browse_sql');
        add_plugin_hook('contribution_append_to_type_form', 'geolocation_append_contribution_form');
        add_plugin_hook('contribution_save_form', 'geolocation_save_contribution_form');

        // Add plugin filters
        add_filter('admin_navigation_main', 'geolocation_admin_nav');
        add_filter('define_response_contexts', 'geolocation_kml_response_context');
        add_filter('define_action_contexts', 'geolocation_kml_action_context');
        add_filter('admin_items_form_tabs', 'geolocation_item_form_tabs');
    }
}
