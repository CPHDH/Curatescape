import { Map, TileLayer, Marker, Popup, Control, FeatureGroup, DivIcon } from 'leaflet'; // req. external importmap
const mapcontainer = document.querySelector('#curatescape-map-canvas');
const mapfigure = document.querySelector('#curatescape-map-figure');
let requestedMarker = null;
let map = null;
let bounds = null;
let group = {};
let cluster_group = {};
let mapDidReset = false;
let all_markers = [];
const tileLayerConfig = (name, mbUser, mbToken, mbStyle, mbLabel, stadiaKey, stadiaPreferEU, fallback = 'CARTO_VOYAGER')=>{
	let tiles = [];
	tiles.CARTO_POSITRON = new TileLayer(
		"//cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}{retina}.png",
		{
			attribution:
			'<a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> | <a href="https://cartodb.com/attributions" target="_blank">CartoDB</a>',
			retina: '@2x',
			label: 'Street (Carto Positron)',
		}
	);
	tiles.CARTO_DARKMATTER = new TileLayer(
		"//cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}{retina}.png",
		{
			attribution:
			'<a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> | <a href="https://cartodb.com/attributions" target="_blank">CartoDB</a>',
			retina: '@2x',
			label: 'Street (Carto Dark Matter)',
		}
	);
	tiles.CARTO_VOYAGER = new TileLayer(
		"//cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}{retina}.png",
		{
			attribution:
			'<a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> | <a href="https://cartodb.com/attributions" target="_blank">CartoDB</a>',
			retina: '@2x',
			label: 'Street (Carto Voyager)',
		}
	);
	tiles.OSM_HUMANITARIAN = new TileLayer(
		"//{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png",
		{
			attribution:
			'<a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> | <a href="https://www.hotosm.org" target="_blank">Humanitarian OpenStreetMap Team</a> | <a href="https://openstreetmap.fr" target="_blank">OpenStreetMap France</a>',
			label: 'Street (OSM Humanitarian)',
		}
	);
	tiles.STADIA_OSMBRIGHT = new TileLayer(
		"//{server}.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{retina}.png{stadia_auth_optional}", 
		{
			server: !stadiaPreferEU ? 'tiles' : 'tiles-eu',
			attribution: '<a href="https://stadiamaps.com/" target="_blank">Stadia Maps</a> | <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> | <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>',
			retina: '@2x',
			label: 'Street (OSM Bright)',
			stadia_auth_optional: stadiaKey ? '?api_key='+stadiaKey : '',
		}
	);
	tiles.STADIA_ALIDADESMOOTH = new TileLayer(
		'//{server}.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png{stadia_auth_optional}',
		{
			server: !stadiaPreferEU ? 'tiles' : 'tiles-eu',
			maxZoom: 20,
			attribution: '<a href="https://stadiamaps.com/" target="_blank">Stadia Maps</a> | <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> | <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>', 
			label: 'Street (Alidade Smooth)',
			stadia_auth_optional: stadiaKey ? '?api_key='+stadiaKey : '',
		}
	);
	tiles.STADIA_ALIDADESMOOTHDARK = new TileLayer(
		'//{server}.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png{stadia_auth_optional}', 
		{
			server: !stadiaPreferEU ? 'tiles' : 'tiles-eu',
			maxZoom: 20,
			attribution: '<a href="https://stadiamaps.com/" target="_blank">Stadia Maps</a> | <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> | <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>', 
			label: 'Street (Alidade Smooth Dark)',
			stadia_auth_optional: stadiaKey ? '?api_key='+stadiaKey : '',
		}
	);
	tiles.STADIA_ALIDADESATELLITE = 	new TileLayer(
		'//{server}.stadiamaps.com/tiles/alidade_satellite/{z}/{x}/{y}{r}.png{stadia_auth_optional}', 
		{
			server: !stadiaPreferEU ? 'tiles' : 'tiles-eu',
			maxZoom: 20,
			attribution: '<a href="https://stadiamaps.com/" target="_blank">Stadia Maps</a> | <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> | <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>', 
			label: 'Street (Alidade Satellite)',
			stadia_auth_optional: stadiaKey ? '?api_key='+stadiaKey : '',
		}
	);
	tiles.STADIA_STAMENTERRAIN = new TileLayer(
		"//{server}.stadiamaps.com/tiles/stamen_terrain/{z}/{x}/{y}{retina}.png{stadia_auth_optional}",
		{
			server: !stadiaPreferEU ? 'tiles' : 'tiles-eu',
			attribution:
			'<a href="https://stadiamaps.com/" target="_blank">Stadia Maps</a> | <a href="https://stamen.com/" target="_blank">Stamen Design</a> | <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a>',
			retina: '@2x',
			label: 'Terrain (Stamen Terrain)',
			stadia_auth_optional: stadiaKey ? '?api_key='+stadiaKey : '',
		}
	);
	tiles.STADIA_STAMENTONER = new TileLayer(
		"//{server}.stadiamaps.com/tiles/stamen_toner_lite/{z}/{x}/{y}{retina}.png{stadia_auth_optional}",
		{
			server: !stadiaPreferEU ? 'tiles' : 'tiles-eu',
			attribution:
			'<a href="https://stadiamaps.com/" target="_blank">Stadia Maps</a> | <a href="https://stamen.com/" target="_blank">Stamen Design</a> | <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a>',
			retina: '@2x',
			label: 'Street (Stamen Toner Lite)',
			stadia_auth_optional: stadiaKey ? '?api_key='+stadiaKey : '',
		}
	);
	tiles.STADIA_OUTDOORS = new TileLayer(
		"//{server}.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{retina}.png",
		{
			server: !stadiaPreferEU ? 'tiles' : 'tiles-eu',
			attribution: '<a href="https://stadiamaps.com/" target="_blank">Stadia Maps</a> | <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a>',
			retina: '@2x',
			label: 'Street (Stadia Outdoors)',
		}
	);
	tiles.MAPBOX = new TileLayer(
		"https://api.mapbox.com/styles/v1/{username}/{style_id}/tiles/{z}/{x}/{y}?access_token={access_token}",
		{
			attribution: '<a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> | <a href="https://www.mapbox.com/about/maps/" target="_blank">Mapbox</a> ',
			username: mbUser,
			style_id: mbStyle,
			access_token: mbToken,
			label: mbLabel ? mbLabel : 'Mapbox',
		}
	);
	if(name === 'MAPBOX'){
		if(mbUser && mbStyle && mbToken){
			return tiles[name];
		}
		return tiles[fallback];
	}
	return tiles[name];
}
const markerIcon = (featured)=>{
	let opt = {
		markerColor: (featured) ? attr('data-featured-color') : attr('data-color'),
		innerColor: '#fff',
		shadowColor: 'rgba(0,0,0,.25)',
		strokeColor: 'rgba(0,0,0,.15)',
		strokeWidth: '2px',
	};
	let innerShape = `<path fill="${opt.innerColor}" stroke="${opt.strokeColor}" stroke-width="${opt.strokeWidth}" d="M29.31,14.72c0,2.93-2.38,5.31-5.31,5.31-2.93,0-5.31-2.38-5.31-5.31s2.38-5.31,5.31-5.31c2.93,0,5.31,2.38,5.31,5.31,0,0,0,0,0,0Z"/>`;
	if( attr('data-featured-star') && featured ){
		innerShape = `<polygon fill="${opt.innerColor}" stroke="${opt.strokeColor}" stroke-width="${opt.strokeWidth}" points="24.2 19.6 18.8 22.8 20.2 16.7 15.5 12.6 21.7 12.1 24.2 6.3 26.6 12.1 32.9 12.6 28.1 16.7 29.5 22.8 24.2 19.6"/>`;
	}
	let iconSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><ellipse class="shadow" fill="${opt.shadowColor}" stroke-width="0px" cx="24" cy="40.16" rx="13.47" ry="7.48"/><path fill="${opt.markerColor}" stroke="${opt.strokeColor}" stroke-width="${opt.strokeWidth}" d="M37.84,15.32c-.17,2.73-.92,5.39-2.19,7.82-1.4,2.89-3.03,5.67-4.86,8.3-1.77,2.6-3.54,4.91-4.87,6.56-.66.83-1.22,1.49-1.6,1.95-.13.15-.23.28-.32.38-.09-.1-.2-.23-.33-.39-.39-.46-.94-1.13-1.6-1.97-1.33-1.67-3.1-3.99-4.87-6.6-1.83-2.64-3.45-5.42-4.86-8.31-1.26-2.4-2.01-5.04-2.19-7.75-.15-7.81,6.04-14.27,13.84-14.44,7.81.18,14,6.64,13.84,14.44Z"/>${innerShape}</svg>`;
	return new DivIcon({
		className: "leaflet-data-marker",
		html: iconSVG,
		iconAnchor: [22, 44],
		iconSize: [44, 44],
		popupAnchor: [0, -44]
	});
}
const attr = (string, el = mapfigure)=>{
	return el.hasAttribute(string) ? el.getAttribute(string) : null;
}
const safeText = (value) => {
  var d = document.createElement("div");
  d.innerHTML = value;
  return d.innerText;
};
const getCommaSeparatedValue = (string, index)=>{
	let arr = string.split(',');
	return arr[index] ? arr[index].trim() : null;
}
const tileLayersAdd = ()=>{
	let tileLayers = [];
	// primary tile layer
	let primary = tileLayerConfig(
		attr('data-primary-layer'),
		attr('data-mb-user'),
		attr('data-mb-token'),
		getCommaSeparatedValue(attr('data-mb-id'), 0),
		getCommaSeparatedValue(attr('data-mb-label'), 0),
		attr('data-stadia-key'),
		attr('data-prefer-eu')
	);	
	tileLayers[primary.options.label] = primary; 
	primary.addTo(map);
	// secondary tile layer and layer controls?
	if(attr('data-secondary-layer')){
		let secondary = tileLayerConfig(
			attr('data-secondary-layer'),
			attr('data-mb-user'),
			attr('data-mb-token'),
			getCommaSeparatedValue(attr('data-mb-id'), 1),
			getCommaSeparatedValue(attr('data-mb-label'), 1),
			attr('data-stadia-key'),
			attr('data-prefer-eu')
		);
		tileLayers[secondary.options.label] = secondary;
		const tileLayersControl = new Control.Layers(tileLayers).addTo(map); 
	}
}
const subjectSelectControls = ()=>{
  // not a proper leaflet control
  let subjectSelect = document.querySelector(".curatescape-map select");
  if(subjectSelect){
	subjectSelect.removeAttribute("hidden");
	subjectSelect.addEventListener("change", (e)=>{
		markerReset(e.target.value, e.target.options[e.target.selectedIndex].text)
	})
  }
}
const markerReset = (term, label)=>{
	mapDidReset = true;
	if (map.hasLayer(group)) map.removeLayer(group);
	if (map.hasLayer(cluster_group)) map.removeLayer(cluster_group);
	// map_title = label;
	all_markers = [];
	markersAdd(null, term);
}
const dataSource = (term)=>{
	if(term){
		return attr('data-root-url') +
		"/items/browse?search=&advanced[0][element_id]=49&advanced[0][type]=contains&advanced[0][terms]=" +
		term + "&output=mobile-json";
	}else{
		return attr('data-json-source');
	}
}
const mapBounds = (requestedMarker,term)=>{
	bounds = group.getBounds();
	if (!requestedMarker) {
		if(attr('data-fixed-center') == "1" && !term && !mapDidReset){
			return map.setView([attr('data-lat'), attr('data-lon')], attr('data-zoom'));
		}
		return map.fitBounds(bounds, { padding: [20,20] });
	}
}
const infoWindow = (item, i, tourid)=>{
	let params = tourid ? "?tour=" + tourid + "&index=" + i : "";
	let href = attr('data-root-url') + "/items/show/" + item.id + params;
	let address = item.address ? item.address.replace(/(<([^>]+)>)/gi, "") : item.latitude + "," + item.longitude;
	let title = item.title;
	if(item.subtitle){
		title += `<span class="curatescape-iw-subtitle"><span class="curatescape-iw-separator">: </span>${item.subtitle}</span>`
	}
	// Info window
	let html = `
	<div class="curatescape-iw">
	<a href="${href}" class="curatescape-iw-image portrait" style="background-image:url(${item.fullsize}); title="${item.title}"></a>
	<div class="curatescape-iw-content">
		<a href="${href}" class="curatescape-iw-title">${title}</a>
		<div class="curatescape-iw-address">${address}</div>
	</div> 
	</div>`;
	return html;
}
const markersAdd = (requested_id = null,term = null)=>{
	// pauseInteraction(map, isGlobalMap);
	// loadingTitleAdd();
	let istour = (attr('data-json-source').includes('/tours/'));
	let tourid = istour ? '@todo' : null;
	fetch(dataSource(term)).then((response) => response.json()).then((data) => {
		if (data.items.length) {
		data.items.forEach((item, i) => {
			// Marker / Info Window
			let marker = new Marker([item.latitude, item.longitude], {
				icon: markerIcon(item.featured),
				title: safeText(item.title),
				alt: safeText(item.title),
				item_id: item.id.toString(),
			}).bindPopup(infoWindow(item, i, tourid));
			all_markers.push(marker);
			// Store requested marker...
			if (item.id == requested_id) {
				requestedMarker = marker;
			}
			group = new FeatureGroup(all_markers);
			group.addTo(map);
		});
	}
	// Bounds
	mapBounds(requestedMarker);
	// if (map_attr.cluster == "1") {
	//   markerRequestZoom = 18;
	//   loadJS(map_attr.clusterJs, () => {
	// 	// Clusters...
	// 	const getRadius = (zoom, rad = 0) => {
	// 	  return 60 + zoom;
	// 	};
	// 	cluster_group = L.markerClusterGroup({
	// 	  removeOutsideVisibleBounds: false,
	// 	  maxClusterRadius: getRadius,
	// 	  spiderfyOnMaxZoom: true,
	// 	  showCoverageOnHover: true,
	// 	  polygonOptions: {
	// 		fillColor: "#000",
	// 		color: "#000",
	// 		weight: 0,
	// 		opacity: 0,
	// 		fillOpacity: 0.25,
	// 	  },
	// 	});
	// 	group = L.featureGroup(all_markers);
	// 	cluster_group.addLayer(group);
	// 	cluster_group.addTo(map);
	// 	// Bounds
	// 	bounds = group.getBounds();
	// 	if (!requestedMarker) {
	// 	  if(!isEmpty(term) || map_attr.fixedCenter !== "1"){
	// 		map.fitBounds(bounds, { padding: [20,20] });
	// 	  }else if(mapDidReset && map_attr.fixedCenter == "1"){
	// 		map.setView([map_attr.lat, map_attr.lon], map_attr.zoom);
	// 	  }
	// 	}
		// resumeInteraction(map, !isGlobalMap, isGlobalMap);
	//   });
	// } else {
	//   // No Clusters...
	//   group = L.featureGroup(all_markers);
	//   group.addTo(map);
	//   // Bounds
	//   bounds = group.getBounds();
	//   if (!requestedMarker) {
	// 	if(!isEmpty(term) || map_attr.fixedCenter !== "1"){
	// 	  map.fitBounds(bounds,{ padding: [20,20] });
	// 	}else if(mapDidReset && map_attr.fixedCenter == "1"){
	// 	  map.setView([map_attr.lat, map_attr.lon], map_attr.zoom);
	// 	}
	//   }
	//   // resumeInteraction(map, !isGlobalMap, isGlobalMap);
	// }
	// Open Requested Marker
	// if (requestedMarker) {
	//   pauseInteraction(map, isGlobalMap);
	//   map.flyTo(requestedMarker._latlng, markerRequestZoom);
	//   map.once("moveend", () => {
	// 	requestedMarker.openPopup();
	// 	setMarkerFocus(requestedMarker, map);
	// 	resumeInteraction(map, !isGlobalMap, isGlobalMap);
	//   });
	// } else {
	//   if (!isGlobalMap) setMapFocus(map);
	// }
	// loadingTitleRemove();
  });
}
const curatescapeMap = ()=>{
	let multi = (attr('data-maptype') === "multi");
	map = new Map(mapcontainer, {
		scrollWheelZoom: !multi,
		tap: false,
	}).setView([attr('data-lat'), attr('data-lon')], 13);
	map.attributionControl.setPrefix('');
	tileLayersAdd();
	subjectSelectControls();
	markersAdd();
}
// @todo intersection observer
document.addEventListener('DOMContentLoaded', ()=>{
	curatescapeMap();
});