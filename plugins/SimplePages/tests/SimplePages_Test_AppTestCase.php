<?php
/**
 * Simple Pages
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Base class for SimplePages tests.
 */
class SimplePages_Test_AppTestCase extends Omeka_Test_AppTestCase
{
    const PLUGIN_NAME = 'SimplePages';
    
    public function setUp()
    {
        parent::setUp();
        
        // Authenticate and set the current user 
        $this->user = $this->db->getTable('User')->find(1);
        $this->_authenticateUser($this->user);
        
        $pluginHelper = new Omeka_Test_Helper_Plugin;
        $pluginHelper->setUp(self::PLUGIN_NAME);
        Omeka_Test_Resource_Db::$runInstaller = true;
        $this->_reloadRoutes();
    }
    
    public function assertPreConditions()
    {
        $pages = $this->db->getTable('SimplePagesPage')->findAll();
        $this->assertEquals(1, count($pages), 'There should be one page.');
        
        $aboutPage = $pages[0];
        $this->assertEquals('About', $aboutPage->title, 'The about page has the wrong title.');
        $this->assertEquals('about', $aboutPage->slug, 'The about page has the wrong slug.');
    }
    
    protected function _deleteAllPages()
    {
        $pages = $this->db->getTable('SimplePagesPage')->findAll();
        foreach($pages as $page) {
            $page->delete();
        }
        $pages = $this->db->getTable('SimplePagesPage')->findAll();
        $this->assertEquals(0, count($pages), 'There should be no pages.');
    }
    
    protected function _addTestPage($title='Test Page', $slug='testpage', $text='whatever', $isPublished = true) 
    {
        $page = new SimplePagesPage;
        $page->title = $title;
        $page->slug = $slug;
        $page->text = $text;
        $page->is_published = $isPublished ? '1' : '0';
        $page->created_by_user_id = $this->user->id;
        $page->save();
        
        return $page;
    }
    
    protected function _addTestPages($minIndex=0, $maxIndex=0, $titlePrefix = 'Test Page ', $slugPrefix = 'testpage', $textPrefix = 'whatever')
    {
        $pages = array();
        for($i = $minIndex; $i <= $maxIndex; $i++) {
            $pages[] = $this->_addTestPage( $titlePrefix . $i, $slugPrefix . $i, $textPrefix . $i);
        }
        return $pages;
    }

    protected function _reloadRoutes()
    {
        $plugin = new SimplePagesPlugin;
        $plugin->hookDefineRoutes(array('router' => Zend_Controller_Front::getInstance()->getRouter()));
    }
}
