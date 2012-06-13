<?php
require_once 'Tour.php';
require_once 'TourItem.php';

require_once 'Omeka/Controller/Action.php';

class TourBuilder_ToursController extends Omeka_Controller_Action
{
   public function init()
   {
      $this->_modelClass = 'Tour';
   }

   public function removeitemAction()
   {
      # Get the tour and item id from the request
      $tour = $this->findById();
      $item_id = $this->getRequest()->getParam( 'item' );

      # Remove the item (id) from the tour
      $tour->removeItem( $item_id );

      # Go back to editing the tour.
      $this->redirect->goto( 'edit', null, null, array( 'id' => $tour->id ) );
   }

   public function browseforitemAction()
   {
      $db = get_db();
      $tour_id = $this->getRequest()->getParam( 'id' );
      $tour = $this->findById();

      # Get all items which are not already in this tour.
      $itemTable = $db->getTable( 'Item' );
      /* This did not work, much as I preferred
      $iAlias = $itemTable->getTableAlias();
      $select = $itemTable->getSelect();
      $select->joinLeft( array( 'ti' => $db->TourItem ),
         "ti.item_id = $iAlias.id AND ti.tour_id = ?" );
      $select->where( 'ti.id IS NULL' );
       */

      # Attach the items to the view
      #$items = $itemTable->fetchObjects( $select, array( $tour_id ) );
      $items = $itemTable->fetchObjects( "SELECT i.*
         FROM omeka_items i LEFT OUTER JOIN omeka_tour_items ti
         ON i.id = ti.item_id AND ti.tour_id = ?
         WHERE ti.id IS NULL",
         array( $tour->id ) );
      $this->view->assign( compact( 'items' ) );
   }

   public function additemAction()
   {
      # Get the tour and item ids
      $tour = $this->findById();
      $item_id = $this->getRequest()->getParam( 'item' );

      $tour->addItem( $item_id );

      $this->redirect->goto( 'edit', null, null, array( 'id' => $tour->id ) );
   }

   public function hoistitemAction()
   {
      $tour = $this->findById();
      $item_id = $this->getRequest()->getParam( 'item' );

      $tour->hoistItem( $item_id );
      $this->redirect->goto( 'edit', null, null, array( 'id' => $tour->id ) );
   }

   public function loweritemAction()
   {
      $tour = $this->findById();
      $item_id = $this->getRequest()->getParam( 'item' );

      $tour->lowerItem( $item_id );
      $this->redirect->goto( 'edit', null, null, array( 'id' => $tour->id ) );
   }
}
