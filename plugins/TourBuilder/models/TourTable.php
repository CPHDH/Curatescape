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

   public function getSelect()
   {
      $select = parent::getSelect();

      // Determine public level
      $acl = Omeka_Context::getInstance()->acl;
      if( $acl && $acl->has( 'TourBuilder_Tours' ) )
      {
         $has_permission = $acl->isAllowed( current_user(), 'TourBuilder_Tours',
                                            'showNotPublic' );
         if( ! $has_permission )
         {
            $select->where( 't.public = 1' );
         }
      }

      return $select;
   }

}
