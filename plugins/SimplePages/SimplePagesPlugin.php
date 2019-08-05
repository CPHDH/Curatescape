<?php
/**
 * Simple Pages
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

require_once dirname(__FILE__) . '/helpers/SimplePageFunctions.php';

/**
 * Simple Pages plugin.
 */
class SimplePagesPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array('install', 'uninstall', 'upgrade', 'initialize',
        'define_acl', 'define_routes', 'html_purifier_form_submission');

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array('admin_navigation_main',
        'public_navigation_main', 'search_record_types', 'page_caching_whitelist',
        'page_caching_blacklist_for_record',
        'api_resources', 'api_import_omeka_adapters');

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        // Create the table.
        $db = $this->_db;
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SimplePagesPage` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `modified_by_user_id` int(10) unsigned NOT NULL,
          `created_by_user_id` int(10) unsigned NOT NULL,
          `is_published` tinyint(1) NOT NULL,
          `title` tinytext COLLATE utf8_unicode_ci NOT NULL,
          `slug` tinytext COLLATE utf8_unicode_ci NOT NULL,
          `text` mediumtext COLLATE utf8_unicode_ci,
          `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `inserted` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
          `order` int(10) unsigned NOT NULL,
          `parent_id` int(10) unsigned NOT NULL,
          `template` tinytext COLLATE utf8_unicode_ci NOT NULL,
          `use_tiny_mce` tinyint(1) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `is_published` (`is_published`),
          KEY `inserted` (`inserted`),
          KEY `updated` (`updated`),
          KEY `created_by_user_id` (`created_by_user_id`),
          KEY `modified_by_user_id` (`modified_by_user_id`),
          KEY `order` (`order`),
          KEY `parent_id` (`parent_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);
        
        // Save an example page.
        $page = new SimplePagesPage;
        $page->modified_by_user_id = current_user()->id;
        $page->created_by_user_id = current_user()->id;
        $page->is_published = 1;
        $page->parent_id = 0;
        $page->title = 'About';
        $page->slug = 'about';
        $page->text = '<p>This is an example page. Feel free to replace this content, or delete the page and start from scratch.</p>';
        $page->save();
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {        
        // Drop the table.
        $db = $this->_db;
        $sql = "DROP TABLE IF EXISTS `$db->SimplePagesPage`";
        $db->query($sql);
    }

    /**
     * Upgrade the plugin.
     *
     * @param array $args contains: 'old_version' and 'new_version'
     */
    public function hookUpgrade($args)
    {
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];
        $db = $this->_db;

        // MySQL 5.7+ fix; must do first or else MySQL complains about any other ALTER
        if ($oldVersion < '3.0.7') {
            $db->query("ALTER TABLE `$db->SimplePagesPage` ALTER `inserted` SET DEFAULT '2000-01-01 00:00:00'");
        }

        if ($oldVersion < '1.0') {
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD INDEX ( `is_published` )";
            $db->query($sql);    
            
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD INDEX ( `inserted` ) ";
            $db->query($sql);    
            
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD INDEX ( `updated` ) ";
            $db->query($sql);    
            
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD INDEX ( `add_to_public_nav` ) ";
            $db->query($sql);    
            
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD INDEX ( `created_by_user_id` ) ";
            $db->query($sql);    
            
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD INDEX ( `modified_by_user_id` ) ";
            $db->query($sql);    
            
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD `order` INT UNSIGNED NOT NULL ";
            $db->query($sql);
            
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD INDEX ( `order` ) ";
            $db->query($sql);
            
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD `parent_id` INT UNSIGNED NOT NULL ";
            $db->query($sql);
            
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD INDEX ( `parent_id` ) ";
            $db->query($sql);
            
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD `template` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ";
            $db->query($sql);
        }

        if ($oldVersion < '1.3') {
            $sql = "ALTER TABLE `$db->SimplePagesPage` ADD `use_tiny_mce` TINYINT(1) NOT NULL";
            $db->query($sql);
        }

        if ($oldVersion < '2.0') {
            $db->query("ALTER TABLE `$db->SimplePagesPage` DROP `add_to_public_nav`");
            delete_option('simple_pages_home_page_id');
        }

        if ($oldVersion < '3.0.2') {
            $db->query("ALTER TABLE `$db->SimplePagesPage` MODIFY `text` MEDIUMTEXT COLLATE utf8_unicode_ci");
        }

        if ($oldVersion < '3.1.1') {
            delete_option('simple_pages_filter_page_content');
        }
    }

    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
    }

    /**
     * Define the ACL.
     * 
     * @param Omeka_Acl
     */
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        
        $indexResource = new Zend_Acl_Resource('SimplePages_Index');
        $pageResource = new Zend_Acl_Resource('SimplePages_Page');
        $acl->add($indexResource);
        $acl->add($pageResource);

        $acl->allow(array('super', 'admin'), array('SimplePages_Index', 'SimplePages_Page'));
        $acl->allow(null, 'SimplePages_Page', 'show');
        $acl->deny(null, 'SimplePages_Page', 'show-unpublished');
    }

    /**
     * Add the routes for accessing simple pages by slug.
     * 
     * @param Zend_Controller_Router_Rewrite $router
     */
    public function hookDefineRoutes($args)
    {
        // Don't add these routes on the admin side to avoid conflicts.
        if (is_admin_theme()) {
            return;
        }

        $router = $args['router'];

        // Add custom routes based on the page slug.
        $pages = get_db()->getTable('SimplePagesPage')->findAll();
        foreach ($pages as $page) {
            $router->addRoute(
                'simple_pages_show_page_' . $page->id, 
                new Zend_Controller_Router_Route(
                    $page->slug, 
                    array(
                        'module'       => 'simple-pages', 
                        'controller'   => 'page', 
                        'action'       => 'show', 
                        'id'           => $page->id
                    )
                )
            );
        }
    }

    /**
     * Filter the 'text' field of the simple-pages form
     * 
     * @param array $args Hook args, contains:
     *  'request': Zend_Controller_Request_Http
     *  'purifier': HTMLPurifier
     */
    public function hookHtmlPurifierFormSubmission($args)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $purifier = $args['purifier'];

        // If we aren't editing or adding a page in SimplePages, don't do anything.
        if ($request->getModuleName() != 'simple-pages' or !in_array($request->getActionName(), array('edit', 'add'))) {
            return;
        }
        
        $post = $request->getPost();
        $post['text'] = $purifier->purify($post['text']); 
        $request->setPost($post);
    }

    /**
     * Add the Simple Pages link to the admin main navigation.
     * 
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Simple Pages'),
            'uri' => url('simple-pages'),
            'resource' => 'SimplePages_Index',
            'privilege' => 'browse'
        );
        return $nav;
    }

    /**
     * Add the pages to the public main navigation options.
     * 
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterPublicNavigationMain($nav)
    {
        $navLinks = simple_pages_get_links_for_children_pages(0, 'order', true);
        $nav = array_merge($nav, $navLinks);
        return $nav;
    }

    /**
     * Add SimplePagesPage as a searchable type.
     */
    public function filterSearchRecordTypes($recordTypes)
    {
        $recordTypes['SimplePagesPage'] = __('Simple Page');
        return $recordTypes;
    }

    /**
     * Specify the default list of urls to whitelist
     * 
     * @param $whitelist array An associative array urls to whitelist, 
     * where the key is a regular expression of relative urls to whitelist 
     * and the value is an array of Zend_Cache front end settings
     * @return array The whitelist
     */
    public function filterPageCachingWhitelist($whitelist)
    {
        // Add custom routes based on the page slug.
        $pages = get_db()->getTable('SimplePagesPage')->findAll();
        foreach($pages as $page) {
            $whitelist['/' . trim($page->slug, '/')] = array('cache'=>true);
        }
            
        return $whitelist;
    }

    /**
     * Add pages to the blacklist
     * 
     * @param $blacklist array An associative array urls to blacklist, 
     * where the key is a regular expression of relative urls to blacklist 
     * and the value is an array of Zend_Cache front end settings
     * @param $record
     * @param $args Filter arguments. contains:
     * - record: the record
     * - action: the action
     * @return array The blacklist
     */
    public function filterPageCachingBlacklistForRecord($blacklist, $args)
    {
        $record = $args['record'];
        $action = $args['action'];

        if ($record instanceof SimplePagesPage) {
            $page = $record;
            if ($action == 'update' || $action == 'delete') {
                $blacklist['/' . trim($page->slug, '/')] = array('cache'=>false);
            }
        }
            
        return $blacklist;
    }
    public function filterApiResources($apiResources)
    {
	$apiResources['simple_pages'] = array(
		'record_type' => 'SimplePagesPage',
		'actions'   => array('get','index'),
	);	
       return $apiResources;
    }
    
    public function filterApiImportOmekaAdapters($adapters, $args)
    {
        $simplePagesAdapter = new ApiImport_ResponseAdapter_Omeka_GenericAdapter(null, $args['endpointUri'], 'SimplePagesPage');
        $simplePagesAdapter->setService($args['omeka_service']);
        $simplePagesAdapter->setUserProperties(array('modified_by_user', 'created_by_user'));
        $adapters['simple_pages'] = $simplePagesAdapter;
        return $adapters;
    }
}
