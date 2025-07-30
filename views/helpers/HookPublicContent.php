<?php
class Curatescape_View_Helper_HookPublicContent extends Zend_View_Helper_Abstract{
	public function HookPublicContent($args)
	{
		return $this;
	}
	public function homeBottom()
	{
		if(!is_current_url('/')) return null;
		if(option('curatescape_home_map') == 'bottom'){
			echo $this->homeMap('bottom', option('curatescape_home_map_heading'), option('curatescape_home_map_caption'));
		}
	}
	public function homeTop()
	{
		if(!is_current_url('/')) return null;
		if(option('curatescape_home_map') == 'top'){
			echo $this->homeMap('top', option('curatescape_home_map_heading'), option('curatescape_home_map_caption'));
		}
	}
	private function homeMap($class = null, $heading = null, $figcaption = null, $html = null)
	{
		if(isset($heading)){
			$heading = '<h2 class="curatescape-map-title">'.plainText($heading).'</h2>';
		}
		if(isset($figcaption)){
			$figcaption = allowLinks($figcaption);
		}
		?>
		<div class="curatescape-home-content <?php echo $class;?>">
			<?php echo $heading;?>
			<?php 
			if(option('curatescape_map_mirror_geolocation')){
				echo get_view()->CuratescapeMap()->GeolocationShortcode(null, null, $figcaption, "home-items-map");
			}else{
				echo get_view()->CuratescapeMap()->Multi($figcaption, true, "home", null, WEB_ROOT.'/items/browse?output=mobile-json');
			}
			?>
		</div>
		<?php
	}

}