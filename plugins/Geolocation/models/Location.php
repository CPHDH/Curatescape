<?php
require_once 'LocationTable.php';
/**
 * Location
 * @package: Omeka
 */
class Location extends Omeka_Record
{
    public $item_id;
    public $latitude;
    public $longitude;
    public $zoom_level;
    public $map_type;
    public $address;
    
    protected function _validate()
    {
        if (empty($this->item_id)) {
            $this->addError('item_id', 'Location requires an item id.');
        }
    }
}