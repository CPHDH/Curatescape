<?php
/**
 * Simple Vocab
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A simple_vocab_terms row.
 * 
 * @package Omeka\Plugins\SimpleVocab
 */
class SimpleVocabTerm extends Omeka_Record_AbstractRecord
{
    public $id;
    public $element_id;
    public $terms;
}
