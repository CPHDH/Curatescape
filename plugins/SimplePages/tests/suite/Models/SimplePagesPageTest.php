<?php
/**
 * Simple Pages
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Tests for the SimplePagesPage model.
 */
class Models_SimplePagesPageTest extends SimplePages_Test_AppTestCase
{   
    public function testGetCreatedByUser()
    {
        $testPage1 = $this->_addTestPage('Test Title 1', 'testslug1', 'testtext1');
        $this->assertEquals($this->user->id, $testPage1->getCreatedByUser()->id);
    }

    public function testGetModifiedByUser()
    {
        $testPage1 = $this->_addTestPage('Test Title 1', 'testslug1', 'testtext1');
        $this->assertEquals($this->user->id, $testPage1->getModifiedByUser()->id);
    }
        
    public function testGetAncestors()
    {
        $testPage1 = $this->_addTestPage('Test Title 1', 'testslug1', 'testtext1');

        $testPage2 = $this->_addTestPage('Test Title 2', 'testslug2', 'testtext2');
        $testPage2->parent_id = $testPage1->id;
        $testPage2->save();
        
        $testPage3 = $this->_addTestPage('Test Title 3', 'testslug3', 'testtext3');
        $testPage3->parent_id = $testPage1->id;
        $testPage3->save();

        $testPage4 = $this->_addTestPage('Test Title 4', 'testslug4', 'testtext4');
        $testPage4->parent_id = $testPage3->id;
        $testPage4->save();
    
        $testPage5 = $this->_addTestPage('Test Title 5', 'testslug5', 'testtext5');
        $testPage5->parent_id = $testPage3->id;
        $testPage5->save();

        $testPage6 = $this->_addTestPage('Test Title 6', 'testslug6', 'testtext6');
        $testPage6->parent_id = $testPage5->id;
        $testPage6->save();
        
        $testPage7 = $this->_addTestPage('Test Title 7', 'testslug7', 'testtext7');
        $testPage7->parent_id = $testPage5->id;
        $testPage7->save();

        $testPage8 = $this->_addTestPage('Test Title 8', 'testslug8', 'testtext8');
        $testPage8->parent_id = $testPage7->id;
        $testPage8->save();
        
        $ancestors = $testPage8->getAncestors();        
        $ancestorIds = array($testPage7->id, $testPage5->id, $testPage3->id, $testPage1->id);
        
        $this->assertEquals(4, count($ancestors));
        foreach($ancestors as $ancestor) {
            $this->assertTrue(in_array($ancestor->id, $ancestorIds));
        }
    }
    
    public function testGetChildren()
    {
        $testPage1 = $this->_addTestPage('Test Title 1', 'testslug1', 'testtext1');

        $testPage2 = $this->_addTestPage('Test Title 2', 'testslug2', 'testtext2');
        $testPage2->parent_id = $testPage1->id;
        $testPage2->save();
        
        $testPage3 = $this->_addTestPage('Test Title 3', 'testslug3', 'testtext3');
        $testPage3->parent_id = $testPage1->id;
        $testPage3->save();

        $testPage4 = $this->_addTestPage('Test Title 4', 'testslug4', 'testtext4');
        $testPage4->parent_id = $testPage3->id;
        $testPage4->save();
    
        $testPage5 = $this->_addTestPage('Test Title 5', 'testslug5', 'testtext5');
        $testPage5->parent_id = $testPage3->id;
        $testPage5->save();

        $testPage6 = $this->_addTestPage('Test Title 6', 'testslug6', 'testtext6');
        $testPage6->parent_id = $testPage5->id;
        $testPage6->save();

        $testPage7 = $this->_addTestPage('Test Title 7', 'testslug7', 'testtext7');
        $testPage7->parent_id = $testPage5->id;
        $testPage7->save();

        $testPage8 = $this->_addTestPage('Test Title 8', 'testslug8', 'testtext8');
        $testPage8->parent_id = $testPage7->id;
        $testPage8->save();
        
        $childrenPages = $testPage5->getChildren();
        $childrenPageIds = array($testPage6->id, $testPage7->id);
        
        $this->assertEquals(2, count($childrenPages));
        foreach($childrenPages as $childPage) {
            $this->assertTrue(in_array($childPage->id, $childrenPageIds));
        }
    }
}
