<?php

$request = Zend_Controller_Front::getInstance()->getRequest();

$isMapRequest = $request->getModuleName() == 'geolocation';

// Get the address, latitude, longitude, and the radius from parameters
$address = trim((string) $request->getParam('geolocation-address'));
$currentLat = trim((string) $request->getParam('geolocation-latitude'));
$currentLng = trim((string) $request->getParam('geolocation-longitude'));
$radius = trim((string) $request->getParam('geolocation-radius'));
$mapped = $request->getParam('geolocation-mapped');

if (empty($radius)) {
    $radius = get_option('geolocation_default_radius');
}

if (get_option('geolocation_use_metric_distances')) {
   $distanceLabel =  __('Geographic Radius (kilometers)');
   } else {
   $distanceLabel =  __('Geographic Radius (miles)');
}

?>


<div id="search-by-geolocation-status" class="field">
    <div class="field-meta"><?php echo $this->formLabel('geolocation-mapped', __('Geolocation Status')); ?></div>
    <div class="inputs">
        <?php echo $this->formSelect('geolocation-mapped',  $mapped, array(), array(
            '' => __('Select Below'),
            '1' => __('Only Items with Locations'),
            '0' => __('Only Items without Locations'),
        )); ?>
    </div>
</div>


<div id="search-by-geolocation-address" class="field">
    <div class="field-meta"><?php echo $this->formLabel('geolocation-address-input', __('Geographic Address')); ?></div>
    <div class="inputs">
        <?php echo $this->formText('geolocation-address',  $address, array('size' => '40', 'id' => 'geolocation-address-input')); ?>
        <?php echo $this->formHidden('geolocation-latitude', $currentLat, array('id' => 'geolocation-latitude-input')); ?>
        <?php echo $this->formHidden('geolocation-longitude', $currentLng, array('id' => 'geolocation-longitude-input')); ?>
    </div>
</div>

<div id="search-by-geolocation-radius" class="field">
    <div class="field-meta"><?php echo $this->formLabel('geolocation-radius', $distanceLabel); ?></div>
    <div class="inputs">
        <?php echo $this->formText('geolocation-radius', $radius, array('size' => '40')); ?>
    </div>
</div>

<?php
echo js_tag('geocoder');
$geocoder = json_encode(get_option('geolocation_geocoder'));
?>
<script type="text/javascript">
(function ($) {
    function disableOnUnmapped(mappedInput) {
        var disabled = false;
        if (mappedInput.val() === '0') {
            disabled = true;
        }
        $('#geolocation-address-input, #geolocation-latitude, #geolocation-longitude, #geolocation-radius').prop('disabled', disabled);
    }

    $(document).ready(function() {
        var geocoder = new OmekaGeocoder(<?php echo $geocoder; ?>);
        var pauseForm = true;
        $('#geolocation-address-input').parents('form').submit(function(event) {
            // Find the geolocation for the address
            if (!pauseForm) {
                return;
            }

            var form = this;
            var address = $('#geolocation-address-input').val();
            if ($.trim(address).length > 0) {
                event.preventDefault();
                geocoder.geocode(address).then(function (coords) {
                    $('#geolocation-latitude-input').val(coords[0]);
                    $('#geolocation-longitude-input').val(coords[1]);
                    pauseForm = false;
                    form.submit();
                }, function () {
                    alert('Error: "' + address + '" was not found!');
                });
            }
        });

        var mapped = $('#geolocation-mapped');
        disableOnUnmapped(mapped);
        $(mapped).change(function () {
            disableOnUnmapped($(this));
        });
    });
})(jQuery);
</script>
