<?php

require_once 'TourTable.php';

/**
 * Tour
 * @package: Omeka
 */
class Tour extends Omeka_Record_AbstractRecord
{
	public $title;
	public $description;
	public $credits;
	public $featured = 0;
	public $public = 0;
	public $postscript_text;

	protected $_related = array( 'Items' => 'getItems','Image' => 'getImage' );

    public function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Search($this);
    }

	public function getItems()
	{
		return $this->getTable()->findItemsByTourId( $this->id );
	}


	public function removeAllItems( ) {
		$db = get_db();
		$tiTable = $db->getTable( 'TourItem' );
		$select = $tiTable->getSelect();
		$select->where( 'tour_id = ?', array( $this->id ) );

		# Get the tour item
		$tourItems = $tiTable->fetchObjects( $select );

		# Iterate through all the tour items
		# and remove them
		for($i = 0; $i < count($tourItems); $i++) {
			$tourItems[$i]->delete();
		}
	}

	public function addItem( $item_id, $ordinal = null )
	{
		if( !is_numeric( $item_id ) ) {
			$item_id = $item_id->id;
		}

		# Get the next ordinal
		$db = get_db();
		$tiTable = $db->getTable( 'TourItem' );
		$select = $tiTable->getSelectForCount();
		$select->where( 'tour_id = ?', array( $this->id ) );
		if($ordinal === null) {
			$ordinal = $tiTable->fetchOne( $select );
		}

		# Create, assign, and save the new tour item connection
		$tourItem = new TourItem;
		$tourItem->tour_id = $this->id;
		$tourItem->item_id = $item_id;
		$tourItem->ordinal = $ordinal;
		$tourItem->save();
	}


	protected function _validate()
	{
		if( empty( $this->title ) ) {
			$this->addError( 'title', 'Tour must be given a title.' );
		}

		if( strlen( $this->title > 255 ) ) {
			$this->addError( 'title', 'Title for a tour must be 255 characters or fewer.' );
		}
		if (!$this->fieldIsUnique('title')) {
			$this->addError('title', 'The Title is already in use by another tour. Please choose another.');
		}

	}

	protected function beforeDelete(){
		$this->removeAllItems();
	}
	
    protected function afterSave($args)
    {        
	    $post=$args['post'];
        if($post && !$args['insert']){ 
	        $this->removeAllItems();
        }
        
		// Get item IDs from $_POST and save to tour items table
		$tour_item_ids=trim( $post['tour_item_ids'] );
		$item_ids=explode( ',', $tour_item_ids );
		$i=0;
		
			
		foreach($item_ids as $item_id){
			$item_id=intval($item_id);
			if($item_id){
				$this->addItem( $item_id, $i);
				$i++;
			}
		}
		
		// Add tour to search index
        if (!$this->public) {
            $this->setSearchTextPrivate();
        }
        $this->setSearchTextTitle($this->title);
        $this->addSearchText($this->title);
        $this->addSearchText($this->description);        
    }		
}
