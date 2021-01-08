<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<kml xmlns="http://earth.google.com/kml/2.0">
    <Document>
        <name>Omeka Items KML</name>
        <?php /* Here is the styling for the balloon that appears on the map */ ?>
        <Style id="item-info-balloon">
            <BalloonStyle>
                <text><![CDATA[
                    <div class="geolocation_balloon">
                        <div class="geolocation_balloon_title">$[namewithlink]</div>
                        <div class="geolocation_balloon_thumbnail">$[description]</div>
                        <p class="geolocation_balloon_description">$[Snippet]</p>
                    </div>
                ]]></text>
            </BalloonStyle>
        </Style>
        <?php
        foreach(loop('item') as $item):
        $location = $locations[$item->id];
        ?>
        <Placemark>
            <name><?php echo xml_escape(metadata('item', 'display_title', array('no_escape' => true))); ?></name>
            <namewithlink><?php echo xml_escape(link_to_item(metadata('item' , array('Dublin Core', 'Title')), array('class' => 'view-item'))); ?></namewithlink>
            <Snippet maxLines="2"><?php echo xml_escape(metadata('item', array('Dublin Core', 'Description'), array('snippet' => 150))); ?></Snippet>
            <description><?php
            if (metadata($item, 'has thumbnail')) {
                echo xml_escape(link_to_item(item_image('thumbnail'), array('class' => 'view-item')));
            }
            ?></description>
            <Point>
                <coordinates><?php echo $location['longitude']; ?>,<?php echo $location['latitude']; ?></coordinates>
            </Point>
            <?php if ($location['address']): ?>
            <address><?php echo xml_escape($location['address']); ?></address>
            <?php endif; ?>
        </Placemark>
        <?php endforeach; ?>
    </Document>
</kml>
