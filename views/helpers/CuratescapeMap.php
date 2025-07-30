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
		data-maptype="single"
		data-json-source="<?php echo $jsonSource;?>"
		data-primary-layer="<?php echo flexOption('curatescape_map_primary_layer','CARTO_VOYAGER');?>"
		data-secondary-layer="<?php echo flexOption('curatescape_map_secondary_layer','');?>"
		data-mb-user="<?php echo flexOption('curatescape_map_mb_user','');?>"
		data-mb-token="<?php echo flexOption('curatescape_map_mb_token','');?>"
		data-mb-id="<?php echo flexOption('curatescape_map_mb_style_id','');?>"
		data-mb-label="<?php echo flexOption('curatescape_map_mb_label','');?>"
		data-stadia-key="<?php echo flexOption('curatescape_map_stadia_key','');?>"
		data-prefer-eu="<?php echo flexOption('curatescape_map_prefer_eu',0);?>"
		data-root-url="<?php echo WEB_ROOT;?>"
		data-color="<?php echo flexOption('curatescape_map_marker_color', '#222');?>"
		>
			<div class="curatescape-map">
				<div id="curatescape-map-canvas"></div>
			</div>
			<figcaption class="curatescape-map-caption"><?php echo $figcaption;?></figcaption>
		</figure>
		<?php
		$this->scriptsCuratescapeMap();
	}
	public function Multi($figcaption = null, $isGlobal = false, $class = "multi", $tourId = null, $jsonSource = null)
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
		data-maptype="multi"
		data-tour="<?php echo $tourId;?>"
		data-json-source="<?php echo $jsonSource;?>"
		data-primary-layer="<?php echo flexOption('curatescape_map_primary_layer','CARTO_VOYAGER');?>"
		data-secondary-layer="<?php echo flexOption('curatescape_map_secondary_layer','');?>"
		data-mb-user="<?php echo flexOption('curatescape_map_mb_user','');?>"
		data-mb-token="<?php echo flexOption('curatescape_map_mb_token','');?>"
		data-mb-id="<?php echo flexOption('curatescape_map_mb_style_id','');?>"
		data-mb-label="<?php echo flexOption('curatescape_map_mb_label','');?>"
		data-stadia-key="<?php echo flexOption('curatescape_map_stadia_key','');?>"
		data-prefer-eu="<?php echo flexOption('curatescape_map_prefer_eu',0);?>"
		data-lat="<?php echo flexOption('geolocation_default_latitude', '');?>"
		data-lon="<?php echo flexOption('geolocation_default_longitude', '');?>"
		data-zoom="<?php echo flexOption('geolocation_default_zoom_level', 12);?>"
		data-cluster="<?php echo flexOption('geolocation_cluster', false);?>"
		data-root-url="<?php echo WEB_ROOT;?>"
		data-fitbounds-label="<?php echo __('Zoom to fit all');?>" 
		data-color="<?php echo $color = flexOption('curatescape_map_marker_color', '#222');?>"
		data-featured-color="<?php echo flexOption('curatescape_map_marker_featured_color', $color);?>"
		data-featured-star="<?php echo flexOption('curatescape_map_marker_featured_star', 0);?>"
		data-fixed-center="<?php echo $isGlobal ? flexOption('curatescape_map_fixed_center', 0) : 0;?>"
		>
			<div class="curatescape-map">
				<?php if( $isGlobal && get_option('curatescape_map_subjects_select')){
					echo $this->subjectSelect();
				} ?>
				<div id="curatescape-map-canvas"></div>
			</div>
			<figcaption class="curatescape-map-caption"><?php echo $figcaption;?></figcaption>
		</figure>
		<?php
		$this->scriptsCuratescapeMap();
	}
	public function GeolocationShortcode($range = null, $tour = null, $figcaption = null, $class="items-map", $html = null)
	{
		// GEOLOCATION DOES DO NOT (YET) SUPPORT RANGE IN SHORTCODE
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
		$html .= isset($tour) ? $this->scriptsGeolocationMapTour() : null;
		return $html;
	}
	private function scriptsCuratescapeMap()
	{
	?>
	<script type="importmap">
		{ "imports": { "leaflet": "https://unpkg.com/leaflet@2.0.0-alpha/dist/leaflet.js" } }
	</script>
	<script type="module" src="<?php echo src('curatescape-map.js', 'javascripts');?>"></script>
	<?php
	}
	private function scriptsGeolocationMapTour()
	{
	?>
	<script defer="defer" src="<?php echo src('geolocation-map-tour.js', 'javascripts');?>"></script>
	<?php
	}
	private function subjectSelect($html = null, $allItemTypes = false, $totalItems = null)
	{
		
		if(!isset($totalItems)){
			$totalItems = $this->totalItems();
		}
		$records_label = $allItemTypes ? __('Items') : storyLabelString('plural');
		$subjects = $this->getPublicStoryMapTerms(49, $allItemTypes);
		if(!count($subjects)) return null;
		$html .= '<select hidden>';
		$html .= '<option value="">'.__('All %s', $records_label).': '.$totalItems.'</option>';
		foreach($subjects as $subject){
		  $html .= '<option value="'.strip_tags(urlencode($subject['text'])).'">'.strip_tags($subject['text']).': '.$subject['total'].'</option>';
		}
		$html .= '</select>';
		return $html;
	}
	private function totalItems($allItemTypes = false){
		$ands = array();
		if(!$allItemTypes){
			$ands[] = 'AND i.item_type_id = '.itemTypeID();
		}
		$ands = implode(' ', $ands); 
		$db = get_db();
		$prefix=$db->prefix;
		$q = $db->query(
			<<<SQL
			SELECT loc.item_id FROM {$prefix}locations AS loc
			INNER JOIN {$prefix}items AS i ON loc.item_id = i.id
			WHERE i.public = 1 {$ands}
			SQL
		);
		$results = $q->fetchAll();
		return count($results);
	}
	private function getPublicStoryMapTerms($elementId, $allItemTypes = false)
	{
		$ands = array();
		if(!$allItemTypes){
			$ands[] = 'AND i.item_type_id = '.itemTypeID();
		}
		$ands = implode(' ', $ands); 
		$db = get_db();
		$prefix=$db->prefix;
		$q = $db->query(
			<<<SQL
			SELECT TRIM(et.text) as text, count(*) as total FROM {$prefix}locations AS loc
			INNER JOIN {$prefix}element_texts AS et ON loc.item_id = et.record_id 
			INNER JOIN {$prefix}items AS i ON loc.item_id = i.id
			WHERE et.element_id = {$elementId} AND et.record_type = 'Item' AND i.public = 1 {$ands}
			GROUP BY text
			ORDER BY text ASC;
			SQL
		);
		$values = $q->fetchAll();
		return $values;
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