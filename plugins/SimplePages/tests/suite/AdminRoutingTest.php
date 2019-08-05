<?php
/**
 * Simple Pages
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Test that the routes are correctly not applied on the admin interface.
 */
class SimplePages_AdminRoutingTest extends SimplePages_Test_AppTestCase
{
    public function testRoutesDoNotApply()
    {
        $page = $this->_addTestPage('Test', 'items');
        $this->_reloadRoutes();

        $this->dispatch('/items');
        $this->assertNotModule('simple-pages');
    }
}
