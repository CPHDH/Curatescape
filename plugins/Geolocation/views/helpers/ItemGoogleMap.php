<?php

class Geolocation_View_Helper_ItemGoogleMap extends Zend_View_Helper_Abstract
{    
    public function itemGoogleMap($item = null, $width = '200px', $height = '200px', $hasBalloonForMarker = false, $markerHtmlClassName = 'geolocation_balloon')
    {
        $divId = "item-map-{$item->id}";
        $location = get_db()->getTable('Location')->findLocationByItem($item, true);
        // Only set the center of the map if this item actually has a location
        // associated with it
        if ($location) {
            $center['latitude']     = $location->latitude;
            $center['longitude']    = $location->longitude;
            $center['zoomLevel']    = $location->zoom_level;
            $center['show']         = true;
            if ($hasBalloonForMarker) {
                $titleLink = link_to_item(metadata($item, array('Dublin Core', 'Title'), array(), $item), array(), 'show', $item);
                $thumbnailLink = !(item_image('thumbnail')) ? '' : link_to_item(item_image('thumbnail',array(), 0, $item), array(), 'show', $item);
                $description = metadata($item, array('Dublin Core', 'Description'), array('snippet'=>150), $item);
                $center['markerHtml'] = '<div class="' . $markerHtmlClassName . '">'
                                      . '<div class="geolocation_balloon_title">' . $titleLink . '</div>'
                                      . '<div class="geolocation_balloon_thumbnail">' . $thumbnailLink . '</div>'
                                      . '<p class="geolocation_balloon_description">' . $description . '</p></div>';
            }
            $options = array();
            $options['mapType'] = get_option('geolocation_map_type');
            $center = js_escape($center);
            $options = js_escape($options);
            $style = "width: $width; height: $height";
            $html = '<div id="' . $divId . '" class="map geolocation-map" style="' . $style . '"></div>';
            
            $js = "var " . Inflector::variablize($divId) . ";";
            $js .= "OmekaMapSingle = new OmekaMapSingle(" . js_escape($divId) . ", $center, $options); ";
            $html .= "<script type='text/javascript'>$js</script>";
        } else {
            $html = '<p class="map-notification">'.__('This item has no location info associated with it.').'</p>';
        }
         return $html;   
    }    
}
