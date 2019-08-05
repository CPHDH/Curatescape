<?php
class Table_Location extends Omeka_Db_Table
{
    /**
     * Returns a location (or array of locations) for an item (or array of items)
     * @param array|Item|int $item An item or item id, or an array of items or item ids
     * @param boolean $findOnlyOne Whether or not to return only one location if it exists for the item
     * @return array|Location A location or an array of locations
     **/
    public function findLocationByItem($item, $findOnlyOne = false)
    {
        $db = get_db();
        
        if (($item instanceof Item) && !$item->exists()) {
            return array();
        } else if (is_array($item) && !count($item)) {
            return array();
        }
        $alias = $this->getTableAlias();
        // Create a SELECT statement for the Location table
        $select = $db->select()->from(array($alias => $db->Location), "$alias.*");
        
        // Create a WHERE condition that will pull down all the location info
        if (is_array($item)) {
            $itemIds = array();
            foreach ($item as $it) {
                $itemIds[] = (int)(($it instanceof Item) ? $it->id : $it);
            }
            $select->where("$alias.item_id IN (?)", $itemIds);
        } else {
            $itemId = (int)(($item instanceof Item) ? $item->id : $item);
            $select->where("$alias.item_id = ?", $itemId);
        }

        // If only a single location is request, return the first one found.
        if ($findOnlyOne) {
            $location = $this->fetchObject($select);
            return $location;
        }

        // Get the locations.
        $locations = $this->fetchObjects($select);

        // Return an associative array of locations where the key is the item_id of the location
        // Note: Since each item can only have one location, this makes sense to associate a single location with a single item_id.
        // However, if in the future, an item can have multiple locations, then we cannot just associate a single location with a single item_id;
        // Instead, in the future, we would have to associate an array of locations with a single item_id.         
        $indexedLocations = array();
        foreach ($locations as $k => $loc) {
            $indexedLocations[$loc['item_id']] = $loc;
        }
        return $indexedLocations;
    }

    /**
     * Add permission check to location queries.
     * 
     * Since all locations belong to an item we can override this method to join 
     * the items table and add a permission check to the select object.
     * 
     * @return Omeka_Db_Select
     */
    public function getSelect()
    {
        $select = parent::getSelect();
        $select->join(array('items' => $this->_db->Item), 'items.id = locations.item_id', array());
        $permissions = new Omeka_Db_Select_PublicPermissions('Items');
        $permissions->apply($select, 'items');
        return $select;
    }
}
