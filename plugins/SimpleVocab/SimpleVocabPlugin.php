<?php
/**
 * Simple Vocab
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Simple Vocab plugin.
 * 
 * @package Omeka\Plugins\SimpleVocab
 */
class SimpleVocabPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array This plugin's hooks.
     */
    protected $_hooks = array(
        'install', 'uninstall', 'initialize', 'upgrade',
        'define_acl', 'config_form', 'config',
    );
    
    /**
     * @var array This plugin's filters.
     */
    protected $_filters = array('admin_navigation_main');
    
    protected $_options = array(
        'simple_vocab_files' => 0,
    );

    /**
     * Install this plugin.
     */
    public function hookInstall()
    {
        $db = get_db();
        $sql = "
        CREATE TABLE `{$db->SimpleVocabTerm}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `element_id` int(10) unsigned NOT NULL,
            `terms` text COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `element_id` (`element_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $db->query($sql);
        $this->_installOptions();
    }
    
    /**
     * Uninstall this plugin.
     */
    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `{$db->SimpleVocabTerm}`;";
        $db->query($sql);
        $this->_uninstallOptions();
    }

    public function hookUpgrade($args)
    {
        if (version_compare($args['old_version'], '2.0.1', '<=')) {
            set_option('simple_vocab_files', $this->_options['simple_vocab_files']);
        }
    }
    
    /**
     * Initialize this plugin.
     */
    public function hookInitialize()
    {
        // Register the select filter controller plugin.
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new SimpleVocab_Controller_Plugin_SelectFilter);
        
        // Add translation.
        add_translation_source(dirname(__FILE__) . '/languages');
    }
    
    public function hookConfigForm()
    {
        $view = get_view();
        include 'config_form.php';
    }

    public function hookConfig($args)
    {
        $files = 0;
        if (isset($args['post']['simple_vocab_files'])) {
            $files = (int) $args['post']['simple_vocab_files'];
        }
        set_option('simple_vocab_files', $files);
    }

    /**
     * Define this plugin's ACL.
     */
    public function hookDefineAcl($args)
    {
        // Restrict access to super and admin users.
        $args['acl']->addResource('SimpleVocab_Index');
    }
    
    /**
     * Add the Simple Vocab navigation link.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Simple Vocab'),
            'uri' => url('simple-vocab'),
            'resource' => 'SimpleVocab_Index'
        );
        return $nav;
    }
}
