<?php 
function tourbuilder_tour_map_data($tour=null){
	$locations=$tour->getItems();
	$points=[];
	foreach( $locations as $l ){ 
		$item=get_record_by_id( 'Item', $l->id );
		$mapdata=get_db()->getTable( 'Location' )->findLocationByItem( $item, true );
	   	if($mapdata){
		   	$points[]=array(
		   		'id'=>$mapdata['item_id'],
		   		'lat'=>$mapdata['latitude'],
		   		'lon'=>$mapdata['longitude'],
		   		'title'=>metadata($item,array('Dublin Core','Title')),
		   		);	
	   	}
	}
    return count($points)>0 ? json_encode($points) : false;	
}	
function tourbuilder_map_script($data,$zoomToItem=false,$panToUser=false){ ?>
	<script>
		var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
		var zoomToItem='<?php echo $zoomToItem;?>';
		var panToUser='<?php echo $panToUser;?>';
		function initMap() {
		  // Base map
		  var location = {lat: 39.961176, lng: -82.998794};
		  window.map = new google.maps.Map(document.getElementById('map'), {
		    zoom: 9,
		    scrollwheel: false,
		    center: location,
		  });
		  setMarkers(map);
		}
		
		// Data points  
		var points=<?php echo $data;?>;
		console.log(points);
		// Adds markers to the map. 
		function setMarkers(map) {
		  latlngbounds = new google.maps.LatLngBounds();
		  for (var i = 0; i < points.length; i++) {
		    
		    var p = points[i];

			var infowindow = new google.maps.InfoWindow({
				content: '',
			});		    
		    var marker = new google.maps.Marker({
		      position: {lat: p['lat'], lng: p['lon']},
		      map: map,
		      title: p['title'],
		      html: '<div class="map_item_container"><h4 class="map_item_link"><a href="/items/show/'+p['id']+'">'+p['title']+'</a></h4></div>',
		    });
			marker.addListener('click', function() {
			    infowindow.setContent(this.html);
			    infowindow.open(map, this);
			  });
		    var latlng = new google.maps.LatLng(p['lat'], p['lon']);
		    latlngbounds.extend(latlng);
		    
		  }
		  map.fitBounds(latlngbounds);
		  
		  if(zoomToItem){
				var listener = google.maps.event.addListener(map, "idle", function() { 
				  
				  // set zoom level to single item settings
				  map.setZoom(points[0]['zoom']); 
				  
				  // open single marker
				  if(w>600){
					  infowindow.setContent(marker.html);
					  infowindow.open(map, marker);
				  }
				  google.maps.event.removeListener(listener); 
				});			  
		  }  	
				 
		}
		
		// Initialize the map
		initMap();
		
		function map_fullscreen(e){
			var parent = jQuery( e ).parents('figure');
			parent.toggleClass('fullscreen');
			var txt = jQuery(parent).hasClass('fullscreen') ? 'Close' : 'View';
			jQuery('#items-map #fullscreen-request toggle').text(txt);  
			google.maps.event.trigger(map, 'resize');
		}

		function map_geolocation(){
			map = window.map;
			if (navigator.geolocation) {
				jQuery('#items-map circle').addClass('trying');
				navigator.geolocation.getCurrentPosition(function(position) {
				  console.log(position.coords.latitude,position.coords.longitude);
				  user = new google.maps.LatLng(position.coords.latitude , position.coords.longitude);
				  var marker = new google.maps.Marker({
					    position: user,
					    map: map,
					    icon: {
					      path: google.maps.SymbolPath.CIRCLE,
					      scale: 10,
					      strokeColor: '#FFFFFF',
					      strokeOpacity: 1.0,
					      strokeWeight: 3,
					      fillColor: '#4285F4',
					      fillOpacity: 1.0,
					    },
					});	
				  latlngbounds.extend(user);	
				  map.fitBounds(latlngbounds);
				  if(panToUser){ // global map only
					  map.panTo(user);
					  map.setZoom(12);					  
				  }

				  jQuery('#items-map circle').addClass('enabled').removeClass('trying');
				  jQuery('#items-map #location-request toggle').text('Your');  
				}, function() {
				  error('Geo Location is not supported');
				});			  
			}else{
				error('Error: navigator.geolocation');
			} 
		}		
	</script>
<?php	} ?>	
		
<?php if(plugin_is_active('Geolocation') && (tourbuilder_tour_map_data($tour)) ): ?>
<aside id="sidebar tour-map">
	<div id="items-map" class="inline">
		<div id="map" style="min-height:300px; width: 100%;"></div>
		<?php tourbuilder_map_script(tourbuilder_tour_map_data($tour)); ?>
	</div>
</aside>	
<?php endif;?>	
	