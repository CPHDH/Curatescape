document.addEventListener('DOMContentLoaded', function() { // deferred
	// GEOLOCATION PLUGIN MAP TO FIGURE + FIGCAPTION
	let geolocation_plugin_map = document.querySelector("#geolocation .geolocation-map");
	let curatescape_map_caption = document.querySelector("figcaption.curatescape-map-caption");
	if(geolocation_plugin_map && curatescape_map_caption){
		geolocation_plugin_map.after(curatescape_map_caption);
		let fig = document.createElement('figure');
		wrap(geolocation_plugin_map, fig);
		fig.appendChild(curatescape_map_caption);
		curatescape_map_caption.dataset.curatescapeHidden=false;
	}
});