<?php

class Geolocation_MapController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Item');
    }
    
    public function browseAction()
    {
        $table = $this->_helper->db->getTable();
        $locationTable = $this->_helper->db->getTable('Location');
        
        $params = $this->getAllParams();
        $params['only_map_items'] = true;
        $limit = (int) get_option('geolocation_per_page');
        $currentPage = $this->getParam('page', 1);

        // Only get pagination data for the "normal" page, only get
        // item/location data for the KML output.
        if ($this->_helper->contextSwitch->getCurrentContext() == 'kml') {
            $items = $table->findBy($params, $limit, $currentPage);
            $this->view->items = $items;
            $this->view->locations = $locationTable->findLocationByItem($items);
        } else {
            $this->view->totalItems = $table->count($params);
            $this->view->params = $params;
        
            $pagination = array(
                'page'          => $currentPage,
                'per_page'      => $limit,
                'total_results' => $this->view->totalItems
            );
            Zend_Registry::set('pagination', $pagination);
        }
    }
}
