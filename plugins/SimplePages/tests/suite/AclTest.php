<?php
/**
 * Simple Pages
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Test the ACL for SimplePages.
 *
 * @package SimplePages
 */
class SimplePages_AclTest extends SimplePages_Test_AppTestCase
{
    const PAGE_RESOURCE = 'SimplePages_Page';
    const ADMIN_RESOURCE = 'SimplePages_Index';

    public function assertPreConditions()
    {
        $this->assertTrue($this->acl->has('SimplePages_Index'),
            "SimplePages ACL resources have not been defined.");
    }
    
    public function testNonauthenticatedUsersCanViewPublishedPages()
    {
        $this->assertTrue($this->acl->isAllowed(null, self::PAGE_RESOURCE, 'show'));
    }
    
    public function testNonauthenticatedUsersCannotViewUnpublishedPages()
    {
        $this->assertFalse($this->acl->isAllowed(null, self::PAGE_RESOURCE, 'show-unpublished'));
    }
    
    public function testNonauthenticatedUsersCannotEditPages()
    {
        $this->assertFalse($this->acl->isAllowed(null, self::ADMIN_RESOURCE, 'edit'));
    }
    
    public function testContributorsCannotEditPages()
    {
        $this->assertFalse($this->acl->isAllowed('contributor', self::ADMIN_RESOURCE, 'edit'));
    }
    
    public function testAdminsCanEditPages()
    {
        $this->assertTrue($this->acl->isAllowed('admin', self::ADMIN_RESOURCE, 'edit'));
    }
    
    public function testSuperUsersCanEditPages()
    {
        $this->assertTrue($this->acl->isAllowed('super', self::ADMIN_RESOURCE, 'edit'));        
    }
}
