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
		?>
		<figure id="curatescape-map-figure" class="<?php echo $class;?>"
			data-json-source="<?php echo $jsonSource;?>"
			data-primary-layer="<?php echo flexOption('geolocation_basemap','CartoDB.Voyager');?>"
			data-secondary-layer=""
			data-mapbox-id="<?php echo flexOption('geolocation_mapbox_map_id', '');?>"
			data-mapbox-token="<?php echo flexOption('geolocation_mapbox_access_token', '');?>"
			data-root-url="<?php echo WEB_ROOT;?>"
			data-color="<?php echo flexOption('curatescape_map_marker_color', '#222');?>"
			data-height="<?php echo flexOption('geolocation_item_map_height', 0);?>"
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
		?>
		<figure id="curatescape-map-figure" class="<?php echo $class;?>"
			data-json-source="<?php echo $jsonSource;?>"
			data-primary-layer="<?php echo flexOption('geolocation_basemap','CartoDB.Voyager');?>"
			data-secondary-layer=""
			data-lat="<?php echo flexOption('geolocation_default_latitude', '');?>"
			data-lon="<?php echo flexOption('geolocation_default_longitude', '');?>"
			data-zoom="<?php echo flexOption('geolocation_default_zoom_level', 12);?>"
			data-mapbox-id="<?php echo flexOption('geolocation_mapbox_map_id', '');?>"
			data-mapbox-token="<?php echo flexOption('geolocation_mapbox_access_token', '');?>"
			data-cluster="<?php echo flexOption('geolocation_cluster', false);?>"
			data-height="<?php echo flexOption('geolocation_item_map_height', 0);?>"
			data-root-url="<?php echo WEB_ROOT;?>"
			data-fitbounds-label="<?php echo __('Zoom to fit all');?>" 
			data-color="<?php echo $color = flexOption('curatescape_map_marker_color', '#222');?>"
			data-featured-color="<?php echo flexOption('curatescape_map_marker_featured_color', $color);?>"
			data-featured-star="<?php echo flexOption('curatescape_map_marker_featured_star', 0);?>"
			data-fixed-center="<?php echo $isGlobal ? flexOption('curatescape_map_fixed_center', 0) : 0;?>"
		>
			<?php echo $isGlobal && get_option('curatescape_map_subjects_select') ? $this->subjectSelect() : null;?>
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
		setOptionMinValue('geolocation_per_page', count($range));
		$map = get_view()->shortcodes('[geolocation range='.implode(',',$range).' '.$height.']');
		$html .= '<figure id="curatescape-map-figure" class="'.$class.'" data-range="'.implode(',',$range).'">';
			$html .=  $map;
			$html .= '<figcaption class="curatescape-map-caption">';
				$html .= $figcaption;
			$html .= '</figcaption>';
		$html .= '</figure>';
		return $html;
	}
	private function subjectSelect($html = null)
	{
		$subjects = elementValuesById('49');
		if(!count($subjects)) return null;
		$html .= '<select hidden>';
		$html .= '<option value="">'.__('All %s', storyLabelString('plural')).': '.count($subjects).'</option>';
		foreach($subjects as $subject){
		  $html .= '<option value="'.strip_tags(urlencode($subject['text'])).'">'.strip_tags($subject['text']).': '.$subject['total'].'</option>';
		}
		$html .= '</select>';
		return $html;
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
			return WEB_ROOT.html_escape(url().'?'.http_build_query($params));
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