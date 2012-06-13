<?php

/**
 * Tour Item.
 * @package: Omeka
 */
class TourItem extends Omeka_Record
{
   public $tour_id;
   public $item_id;
   public $ordinal;

   protected $_related = array(
      'Tour' => 'getTour',
      'Item' => 'getItem',
   );

   protected function getItem()
   {
      return $this->getTable( 'Item' )->find( $this->item_id );
   }

   protected function getTour()
   {
      return $this->getTable( 'Tour' )->find( $this->tour_id );
   }

   protected function _validate()
   {
      if( empty( $this->item_id ) ) {
         $this->addError( 'item_id', 'Tour item requires an item ID#' );
      }

      if( ! is_numeric( $this->item_id ) ) {
         $this->addError( 'item_id', 'Item ID must be numeric' );
      }

      if( empty( $this->tour_id ) ) {
         $this->addError( 'tour_id', 'Tour item requires a tour ID#' );
      }

      if( ! is_numeric( $this->tour_id ) ) {
         $this->addError( 'tour_id', 'Tour ID must be numeric' );
      }

      if( ! is_numeric( $this->ordinal ) ) {
         $this->addError( 'ordinal', 'Order must be numeric' );
      }
   }
}
