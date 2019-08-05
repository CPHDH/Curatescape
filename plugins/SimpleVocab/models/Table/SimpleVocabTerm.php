<?php
/**
 * Simple Vocab
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The simple_vocab_terms table.
 * 
 * @package Omeka\Plugins\SimpleVocab
 */
class Table_SimpleVocabTerm extends Omeka_Db_Table
{
    /**
     * Find a row by element ID.
     * 
     * @param int $elementId
     * @return SimpleVocabTerm
     */
    public function findByElementId($elementId)
    {
        $select = $this->getSelect()->where('element_id = ?', $elementId);
        return $this->fetchObject($select);
    }
    
    /**
     * Find all elements for use by select form element.
     * 
     * @return array
     */
    public function findElementsForSelect()
    {
        $db = $this->getDb();
        $select = $db->select()
                     ->from(array('element_sets' => $db->ElementSet), 
                            array('element_set_name' => 'element_sets.name'))
                     ->join(array('elements' => $db->Element), 
                            'element_sets.id = elements.element_set_id', 
                            array('element_id' =>'elements.id', 
                                  'element_name' => 'elements.name'))
                     ->joinLeft(array('item_types_elements' => $db->ItemTypesElements), 
                                'elements.id = item_types_elements.element_id',
                                array())
                     ->joinLeft(array('item_types' => $db->ItemType), 
                                'item_types_elements.item_type_id = item_types.id', 
                                array('item_type_name' => 'item_types.name'))
                     ->joinLeft(array('simple_vocab_terms' => $db->SimpleVocabTerm), 
                                'elements.id = simple_vocab_terms.element_id', 
                                array('simple_vocab_term_id' => 'simple_vocab_terms.id'))
                     ->where('element_sets.record_type IS NULL OR element_sets.record_type = "Item"')
                     ->order(array('element_sets.name', 'item_types.name', 'elements.name'));
        return $db->fetchAll($select);
    }
    
    /**
     * Find distinct element texts for a specific element.
     * 
     * @param int $elementId
     * @return array
     */
    public function findElementTexts($elementId)
    {
        $db = $this->getDb();
        $select = $db->select()
                     ->from($db->ElementText, array('text', 'COUNT(*) AS count'))
                     ->group('text')
                     ->where('element_id = ?', $elementId)
                     ->where('record_type = ?', 'Item')
                     ->order('count DESC');
        return $db->getTable('ElementText')->fetchObjects($select);
    }
}
