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

		return $select;
	}
	
	public function applySearchFilters($select, $params)
	{
		$db = $this->getDb();
		parent::applySearchFilters($select, $params);
	
		foreach($params as $paramName => $paramValue) {
			switch($paramName) {
				case 'tag':
				case 'tags':
					$tags = explode(',', $paramValue);
					$select->joinInner(array('tg'=>$db->RecordsTags), 'tg.record_id = tours.id', array());
					$select->joinInner(array('t'=>$db->Tag), "t.id = tg.tag_id", array());
					foreach ($tags as $k => $tag) {
						$select->where('t.name = ?', trim($tag));
					}
					$select->where("tg.record_type = ? ", array('Tour'));
					break;
				case 'public':
					$this->filterByPublic($select, $params['public']);
					break;
				case 'featured':
					$this->filterByFeatured($select, $params['featured']);
					break;
			}
		}
		return $select;
	}

}
