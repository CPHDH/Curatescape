<?php
/**
 * Simple Pages
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Simple Pages page controller class.
 *
 * @package SimplePages
 */
class SimplePages_PageController extends Omeka_Controller_AbstractActionController
{
    public function showAction()
    {
        // Get the page object from the passed ID.
        $pageId = $this->_getParam('id');
        $page = $this->_helper->db->getTable('SimplePagesPage')->find($pageId);
        
        // Restrict access to the page when it is not published.
        if (!$page->is_published 
            && !$this->_helper->acl->isAllowed('show-unpublished')) {
            throw new Omeka_Controller_Exception_403;
        }

        $route = $this->getFrontController()->getRouter()->getCurrentRouteName();
        $isHomePage = ($route == Omeka_Application_Resource_Router::HOMEPAGE_ROUTE_NAME);

        // Set the page object to the view.
        $this->view->simple_pages_page = $page;
        $this->view->is_home_page = $isHomePage;
    }
}
