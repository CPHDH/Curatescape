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
		<h2><?php echo __('Map');?></h2>
		<curatescape-map>
			<figure id="curatescape-map-figure" class="<?php echo $class;?>"
			data-maptype="single"
			data-json-source="<?php echo $jsonSource;?>"
			data-primary-layer="<?php echo flexOption('curatescape_map_primary_layer','CARTO_VOYAGER');?>"
			data-secondary-layer="<?php echo flexOption('curatescape_map_secondary_layer','');?>"
			data-cluster="0" 
			data-custom-label="<?php echo flexOption('curatescape_map_custom_label','');?>"
			data-custom-url="<?php echo flexOption('curatescape_map_custom_url','');?>"
			data-stadia-key="<?php echo flexOption('curatescape_map_stadia_key','');?>"
			data-prefer-eu="<?php echo flexOption('curatescape_map_prefer_eu',0);?>"
			data-root-url="<?php echo WEB_ROOT;?>"
			data-fitbounds-label="<?php echo __('Zoom to fit');?>"
			data-reset-label="<?php echo __('Reset to initial view');?>" 
			data-style-swap-label="<?php echo __('Base Map');?>" 
			data-skip-link-label="<?php echo __('Skip %s Map', storyLabelString());?>"
			data-marker-labels="<?php echo storyLabelString().','.storyLabelString('plural');?>" 
			data-color="<?php echo flexOption('curatescape_map_marker_color', '#222');?>"
			>
				<div class="curatescape-map">
					<div id="curatescape-map-canvas" aria-label="<?php echo __('Interactive Map');?>">
						<span id="map-status" aria-live="polite" data-curatescape-screenreader-only="true"><?php echo $ariaLiveMessage;?></span>
					</div>
				</div>
				<figcaption id="curatescape-map-caption"><?php echo $figcaption;?></figcaption>
				<?php echo $this->scriptsCuratescapeMap();?>
			</figure>
		</curatescape-map>
		<?php
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
		$ariaLiveMessage = __('Loading %s', storyLabelString('plural'));
		?>
		<curatescape-map>
			<figure id="curatescape-map-figure" class="<?php echo $class;?>"
			data-maptype="multi"
			data-initial-load="<?php echo $ariaLiveMessage;?>"
			data-tour="<?php echo $tourId;?>"
			data-json-source="<?php echo $jsonSource;?>"
			data-primary-layer="<?php echo flexOption('curatescape_map_primary_layer','CARTO_VOYAGER');?>"
			data-secondary-layer="<?php echo flexOption('curatescape_map_secondary_layer','');?>"
			data-custom-label="<?php echo flexOption('curatescape_map_custom_label','');?>"
			data-custom-url="<?php echo flexOption('curatescape_map_custom_url','');?>"
			data-stadia-key="<?php echo flexOption('curatescape_map_stadia_key','');?>"
			data-prefer-eu="<?php echo flexOption('curatescape_map_prefer_eu',0);?>"
			data-lat="<?php echo flexOption('geolocation_default_latitude', '');?>"
			data-lon="<?php echo flexOption('geolocation_default_longitude', '');?>"
			data-zoom="<?php echo flexOption('geolocation_default_zoom_level', 12);?>"
			data-cluster="<?php echo flexOption('curatescape_map_clusters', 0);?>"
			data-cluster-colors="<?php echo flexOption('curatescape_map_cluster_colors', '');?>"
			data-root-url="<?php echo WEB_ROOT;?>"
			data-fitbounds-label="<?php echo __('Zoom to fit all');?>"
			data-reset-label="<?php echo __('Reset to initial view');?>"
			data-style-swap-label="<?php echo __('Base Map');?>"
			data-skip-link-label="<?php echo __('Skip %s Map', ($tourId ? tourLabelString() : storyLabelString()));?>"
			data-map-list-labels="<?php echo __('View %s List', storyLabelString('plural')).','.__('List Ready');?>"
			data-marker-labels="<?php echo storyLabelString().','.storyLabelString('plural');?>" 
			data-cluster-labels="<?php echo __('Cluster').','.__('Clusters');?>" 
			data-color="<?php echo $color = flexOption('curatescape_map_marker_color', '#222');?>"
			data-featured-color="<?php echo flexOption('curatescape_map_marker_featured_color', $color);?>"
			data-featured-star="<?php echo flexOption('curatescape_map_marker_featured_star', 0);?>"
			data-fixed-center="<?php echo $isGlobal ? flexOption('curatescape_map_fixed_center', 0) : 0;?>"
			data-terms-json="<?php echo $this->getTermsJson(49, $isGlobal, $class);?>"
			>
				<div class="curatescape-map">
					<div id="curatescape-map-canvas" aria-label="<?php echo __('Interactive Map');?>">
						<span id="map-status" aria-live="polite" data-curatescape-screenreader-only="true"><?php echo $ariaLiveMessage;?></span>
					</div>
				</div>
				<figcaption id="curatescape-map-caption"><?php echo $figcaption;?></figcaption>
				<?php echo $this->scriptsCuratescapeMap();?>
			</figure>
		</curatescape-map>
		<?php
	}
	public function GeolocationShortcode($range = null, $tour = null, $figcaption = null, $class="items-map", $html = null)
	{
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
			$html .= '<figcaption id="curatescape-map-caption">';
				$html .= $figcaption;
			$html .= '</figcaption>';
		$html .= '</figure>';
		return $html;
	}
	private function scriptsCuratescapeMap()
	{
	?>
	<script defer src="https://unpkg.com/maplibre-gl@^5.7.3/dist/maplibre-gl.js"></script>
	<link href="https://unpkg.com/maplibre-gl@^5.7.3/dist/maplibre-gl.css" rel="stylesheet" />
	<link href="<?php echo src('curatescape-map.css', 'css');?>" rel="stylesheet" />
	<script type="module" src="<?php echo src('curatescape-map.js', 'javascripts');?>"></script>
	<?php
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
	private function getTermsJson($elementId, $isGlobal, $class, $allItemTypes = false)
	{
		if( 
			!$isGlobal || 
			!get_option('curatescape_map_subjects_select') || 
			$class === "shortcode-no-subjects"
		) return null;
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
		if($values){
			array_unshift($values, [
				'text'=>__('All %s', storyLabelString('plural')),
				'total'=>$this->totalItems(),
				'default'=>true,
			]);
			return urlencode(json_encode($values));
		}
		return null;
	}
	private function defaultFigcaption()
	{
		$view = get_view();
		if($view && isset($view->item)){
			$sa = itm($view->item, 'Street Address');
			$ai = itm($view->item, 'Access Information');
			if($sa || $ai) return '<span class="defaultcaption">'.implode(' | ', array_filter(array($sa,$ai))).'</span>';
		}
		if($view && isset($view->tour)){
			return  '<span class="defaultcaption">'.__('%s Map: %2s', tourLabelString(), $view->tour->title).'</span>';
		}
		if($view && isset($view->items)){
			if($searchParams = getQueryParams()){
				if(isset($searchParams['tags'])){
					$tags = array_map(function($tag){
						return '"'.$tag.'"';
					},explode(',',$searchParams['tags']));
					return '<span class="defaultcaption">'.__('Map: %1s %2s tagged %3s', count($view->items), storyLabelString('plural'), oxfordAmp($tags)).'</span>';
				}
			}
			return '<span class="defaultcaption" data-curatescape-map-screenreader-only="true">'.__('Map: %1s %2s', count($view->items), storyLabelString('plural')).'</span>';
		}
		return '<span class="defaultcaption" data-curatescape-map-screenreader-only="true">'.__('Map').'</span>';
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