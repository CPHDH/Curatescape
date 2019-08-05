<?php

$request = Zend_Controller_Front::getInstance()->getRequest();

// Get the address, latitude, longitude, and the radius from parameters
$address = trim($request->getParam('geolocation-address'));
$currentLat = trim($request->getParam('geolocation-latitude'));
$currentLng = trim($request->getParam('geolocation-longitude'));
$radius = trim($request->getParam('geolocation-radius'));

if (empty($radius)) {
    $radius = get_option('geolocation_default_radius');
}

if (get_option('geolocation_use_metric_distances')) {
   $distanceLabel =  __('Geographic Radius (kilometers)');
   } else {
   $distanceLabel =  __('Geographic Radius (miles)');
}

?>

<div class="field">
    <div class="two columns alpha">
        <?php echo $this->formLabel('geolocation-address', __('Geographic Address')); ?>
    </div>
    <div class="five columns omega inputs">
        <?php echo $this->formText('geolocation-address',  $address, array('size' => '40', 'id' => 'geolocation-address-input')); ?>
        <?php echo $this->formHidden('geolocation-latitude', $currentLat, array('id' => 'geolocation-latitude-input')); ?>
        <?php echo $this->formHidden('geolocation-longitude', $currentLng, array('id' => 'geolocation-longitude-input')); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <?php echo $this->formLabel('geolocation-radius', $distanceLabel); ?>
    </div>
    <div class="five columns omega inputs">
        <?php echo $this->formText('geolocation-radius', $radius, array('size' => '40')); ?>
    </div>
</div>

<?php echo js_tag('geocoder'); ?>
<script type="text/javascript">
(function ($) {
    $(document).ready(function() {
        var geocoder = new OmekaGeocoder('photon');
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
    });
})(jQuery);
</script>
