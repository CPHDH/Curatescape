<?php

class TourTable extends Omeka_Db_Table
{
	public function findItemsByTourId( $tour_id )
	{
		$db = get_db();
		$prefix=$db->prefix;
		$itemTable = $this->getTable( 'Item' );
		$select = $itemTable->getSelect();
		$iAlias = $itemTable->getTableAlias();
		$select->joinInner( array( 'ti' => $db->TourItem ),
			"ti.item_id = $iAlias.id", array() );
		$select->where( 'ti.tour_id = ?', array( $tour_id ) );
		$select->order( 'ti.ordinal ASC' );

		$items = $itemTable->fetchObjects( "SELECT i.*, ti.ordinal
         FROM ".$prefix."items i LEFT JOIN ".$prefix."tour_items ti
         ON i.id = ti.item_id
         WHERE ti.tour_id = ?
         ORDER BY ti.ordinal ASC",
			array( $tour_id ) );

		//$items = $itemTable->fetchObjects( $select );
		return $items;
	}

	public function findImageByTourId( $tour_id ) {
		$db = get_db();
		$prefix=$db->prefix;
		$itemTable = $this->getTable( 'File' );
		$select = $itemTable->getSelect();
		$iAlias = $itemTable->getTableAlias();
		$select->joinInner( array( 'ti' => $db->TourItem ),
			"ti.item_id = $iAlias.id", array() );
		$select->where( 'ti.tour_id = ?', array( $tour_id ) );
		$select->order( 'ti.ordinal ASC' );

		$items = $itemTable->fetchObjects( "SELECT f.*, ti.ordinal
         FROM ".$prefix."files f LEFT JOIN ".$prefix."tour_items ti
         ON i.id = ti.item_id
         WHERE ti.tour_id = ?
         ORDER BY ti.ordinal ASC",
			array( $tour_id ) );

		//$items = $itemTable->fetchObjects( $select );
		return $items;
	}

	public function getSelect()
	{
		$select = parent::getSelect()->order('tours.id');

		$permissions = new Omeka_Db_Select_PublicPermissions( 'TourBuilder_Tours' );
		$permissions->apply( $select, 'tours', null );
		$acl = Zend_Registry::get('bootstrap')->getResource('Acl');
/*
		if( $acl &&  ! is_allowed( 'TourBuilder_Tours', 'show-unpublished' ) )
		{
			// Determine public level TODO: May be outdated
			$select->where( $this->getTableAlias() . '.public = 1' );
		}
*/

		return $select;
	}

}
