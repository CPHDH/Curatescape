<?php
class Curatescape_View_Helper_CuratescapeMap extends Zend_View_Helper_Abstract{
	public function CuratescapeMap()
	{
		return $this;
	}
	public function Single($figcaption = null, $class = "single", $jsonSource = null)
	{
		if(!$jsonSource){
			$jsonSource = $this->defaultJSONSource();
		}
		if(!$jsonSource) return null;
		if(!$figcaption){
			$figcaption = $this->defaultFigcaption();
		}
		$basemap = (get_option('geolocation_basemap')) ? get_option('geolocation_basemap') : 'CartoDB.Voyager';
		$height = (get_option('geolocation_item_map_height')) ? get_option('geolocation_item_map_height') : 0;
		$mapboxMapId = (get_option('geolocation_mapbox_map_id')) ? get_option('geolocation_mapbox_map_id') : '';
		$mapboxAccessToken = (get_option('geolocation_mapbox_access_token')) ? get_option('geolocation_mapbox_access_token') : '';
		$color = (get_option('curatescape_map_marker_color')) ? get_option('curatescape_map_marker_color') : '#222';
		?>
		<figure id="curatescape-map-figure" class="<?php echo $class;?>"
			data-json-source="<?php echo $jsonSource;?>"
			data-primary-layer="<?php echo $basemap;?>"
			data-secondary-layer=""
			data-mapbox-id="<?php echo $mapboxMapId;?>"
			data-mapbox-token="<?php echo $mapboxAccessToken;?>"
			data-root-url="<?php echo WEB_ROOT;?>"
			data-color="<?php echo $color;?>"
			data-height="<?php echo $height;?>"
		>
			<div class="curatescape-map">
				<div id="curatescape-map-canvas"></div>
			</div>
			<figcaption class="curatescape-map-caption"><?php echo $figcaption;?></figcaption>
		</figure>
		<?php
	}
	public function Multi($figcaption = null, $isGlobal = false, $class = "multi", $jsonSource = null)
	{
		if(!$jsonSource){
			$jsonSource = $this->defaultJSONSource();
		}
		if(!$jsonSource) return null;
		if(!$figcaption){
			$figcaption = $this->defaultFigcaption();
		}
		$fixedCenter = (get_option('geolocation_default_latitude')) ? get_option('geolocation_default_latitude') : '';
		$pluginlat = (get_option('geolocation_default_latitude')) ? get_option('geolocation_default_latitude') : '';
		$pluginlon = (get_option('geolocation_default_longitude')) ? get_option('geolocation_default_longitude') : '';
		$zoom = (get_option('geolocation_default_zoom_level')) ? get_option('geolocation_default_zoom_level') : 12;
		$primary = (get_option('geolocation_basemap')) ? get_option('geolocation_basemap') : 'CartoDB.Voyager';
		$cluster = (get_option('geolocation_cluster')) ? get_option('geolocation_cluster') : false; 
		$height = (get_option('geolocation_item_map_height')) ? get_option('geolocation_item_map_height') : 0;
		$mapboxMapId = (get_option('geolocation_mapbox_map_id')) ? get_option('geolocation_mapbox_map_id') : '';
		$mapboxAccessToken = (get_option('geolocation_mapbox_access_token')) ? get_option('geolocation_mapbox_access_token') : '';
		$color = (get_option('curatescape_map_marker_color')) ? get_option('curatescape_map_marker_color') : '#222';
		$colorFeatured = (get_option('curatescape_map_marker_featured_color')) ? get_option('curatescape_map_marker_featured_color') : '#222';
		$star = (get_option('curatescape_map_marker_featured_star')) ? get_option('curatescape_map_marker_featured_star') : 0;
		?>
		<figure id="curatescape-map-figure" class="<?php echo $class;?>"
			data-json-source="<?php echo $jsonSource;?>"
			data-primary-layer="<?php echo $primary;?>"
			data-secondary-layer=""
			data-lat="<?php echo $pluginlat;?>"
			data-lon="<?php echo $pluginlon;?>"
			data-zoom="<?php echo $zoom;?>"
			data-mapbox-id="<?php echo $mapboxMapId;?>"
			data-mapbox-token="<?php echo $mapboxAccessToken;?>"
			data-cluster="<?php echo $cluster;?>"
			data-root-url="<?php echo WEB_ROOT;?>"
			data-fitbounds-label="<?php echo __('Zoom to fit all');?>" 
			data-color="<?php echo $color;?>"
			data-featured-color="<?php echo $colorFeatured;?>"
			data-featured-star="<?php echo $star;?>"
			data-fixed-center="<?php echo $isGlobal ? $fixedCenter : 0;?>"
			data-height="<?php echo $height;?>"
		>
			<?php echo $isGlobal ? $this->subjectSelect() : null;?>
			<div class="curatescape-map">
				<div id="curatescape-map-canvas"></div>
			</div>
			<figcaption class="curatescape-map-caption"><?php echo $figcaption;?></figcaption>
		</figure>
		<?php
	}
	public function GeolocationShortcode($range = null, $tour = null, $figcaption = null, $class="items-map", $html = null)
	{
		// DO NOT USE UNTIL/UNLESS GEOLOCATION PLUGIN SUPPORTS RANGE IN SHORTCODE
		// https://github.com/omeka/plugin-Geolocation/pull/61
		$height = option('geolocation_item_map_height') ? 'height='.option('geolocation_item_map_height') : null;
		if(!$range && !$tour){
			$range = $this->defaultRange();
		}
		if($tour){
			$range = array_column($tour->getItems(), 'id');
		}
		$map = get_view()->shortcodes('[geolocation range='.implode(',',$range).' '.$height.']');
		$html .= '<figure id="curatescape-map-figure" class="'.$class.'">';
			$html .=  $map;
			$html .= '<figcaption class="curatescape-map-caption">';
				$html .= $figcaption;
			$html .= '</figcaption>';
		$html .= '</figure>';
		return $html;
	}
	private function subjectSelect()
	{
		// @todo...
		return null;
	}
	private function defaultFigcaption()
	{
		$view = get_view();
		if($view && isset($view->tour)){
			return  __('%s Map: %2s', tourLabelString(), $view->tour->title);
		}
		if($view && isset($view->items)){
			if($searchParams = getQueryParams()){
				if(isset($searchParams['tags'])){
					$tags = array_map(function($tag){
						return '"'.$tag.'"';
					},explode(',',$searchParams['tags']));
					return __('Map: %1s %2s tagged %3s', count($view->items), storyLabelString('plural'), oxfordAmp($tags));
				}
			}
			return __('Map: %1s %2s', count($view->items), storyLabelString('plural'));
		}
		return __('Map');
	}
	private function defaultJSONSource()
	{
		if(in_array('mobile-json', get_current_action_contexts())){
			$params = array('output'=>'mobile-json');
			if($addlParams = getQueryParams()){
				$params = array_merge($addlParams, $params);
			}
			return html_escape(url().'?'.http_build_query($params));
		}
		return null;
	}
	private function defaultRange()
	{
		$range = array();
		$db = get_db();
		$itemTable = $db->getTable( 'Item' );
		$items = $itemTable->fetchObjects(
			<<<SQL
			SELECT i.* FROM {$db->prefix}items i
			WHERE i.public
			SQL
		);
		$range = array_map(
			function($item) use ($range){
				if(hasLocation($item) && isCuratescapeStory($item)){
					return $item->id;
				}
				return null;
			}, $items );
		return array_values(array_filter($range));
	}
}