<?php

class TourTable extends Omeka_Db_Table
{
   public function findItemsByTourId( $tour_id )
   {
      $db = get_db();

      $itemTable = $this->getTable( 'Item' );
      $select = $itemTable->getSelect();
      $iAlias = $itemTable->getTableAlias();
      $select->joinInner( array( 'ti' => $db->TourItem ),
         "ti.item_id = $iAlias.id", array() );
      $select->where( 'ti.tour_id = ?', array( $tour_id ) );
      $select->order( 'ti.ordinal ASC' );

      $items = $itemTable->fetchObjects( $select );
      return $items;
   }
}
