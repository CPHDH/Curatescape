<?php
/**
 * Tests for geolocation_get_center function
 */
class GeolocationGetCenterTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $helper = new Geolocation_IntegrationHelper();
        $helper->setUpPlugin();
    }

    /**
     * Tests whether geolocation_get_center correctly returns the default latitude, longitude, and zoom level
     */
    public function testGeolocationGetCenter()
    {
        $this->_checkValidCenter();
        set_option('geolocation_default_latitude', '4');
        set_option('geolocation_default_longitude', '5');
        set_option('geolocation_default_zoom_level', '6');
        $this->_checkValidCenter();
    }

    protected function _checkValidCenter()
    {
        $center = geolocation_get_center();

        $centerLat = $center['latitude'];
        $centerLng = $center['longitude'];
        $centerZoomLevel = $center['zoomLevel'];

        $this->assertTrue(is_double($centerLat));
        $this->assertTrue(is_double($centerLng));
        $this->assertTrue(is_double($centerZoomLevel));

        $this->assertEquals(get_option('geolocation_default_latitude'), $centerLat);
        $this->assertEquals(get_option('geolocation_default_longitude'), $centerLng);
        $this->assertEquals(get_option('geolocation_default_zoom_level'), $centerZoomLevel);
    }
}