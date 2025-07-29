import L,{ Map, TileLayer, Marker, Popup, Control } from 'leaflet'; // req. external importmap
const tileLayerConfig = (name, mbUser, mbToken, mbStyle, mbLabel, stadiaKey, stadiaPreferEU, fallback = 'CARTO_VOYAGER')=>{
	let tiles = [];
	tiles.CARTO_POSITRON = new TileLayer(
		"//cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}{retina}.png",
		{
			attribution:
			'<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://cartodb.com/attributions">CartoDB</a>',
			retina: '@2x',
			label: 'Street (Carto Positron)',
		}
	);
	tiles.CARTO_DARKMATTER = new TileLayer(
		"//cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}{retina}.png",
		{
			attribution:
			'<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://cartodb.com/attributions">CartoDB</a>',
			retina: '@2x',
			label: 'Street (Carto Dark Matter)',
		}
	);
	tiles.CARTO_VOYAGER = new TileLayer(
		"//cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}{retina}.png",
		{
			attribution:
			'<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://cartodb.com/attributions">CartoDB</a>',
			retina: '@2x',
			label: 'Street (Carto Voyager)',
		}
	);
	tiles.OSM_HUMANITARIAN = new TileLayer(
		"//{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png",
		{
			attribution:
			'<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://www.hotosm.org">Humanitarian OpenStreetMap Team</a> | <a href="https://openstreetmap.fr">OpenStreetMap France</a>',
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
			'<a href="https://stadiamaps.com/">Stadia Maps</a> | <a href="https://stamen.com/">Stamen Design</a> | <a href="https://openmaptiles.org/">OpenMapTiles</a>',
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
			'<a href="https://stadiamaps.com/">Stadia Maps</a> | <a href="https://stamen.com/">Stamen Design</a> | <a href="https://openmaptiles.org/">OpenMapTiles</a>',
			retina: '@2x',
			label: 'Street (Stamen Toner Lite)',
			stadia_auth_optional: stadiaKey ? '?api_key='+stadiaKey : '',
		}
	);
	tiles.STADIA_OUTDOORS = new TileLayer(
		"//{server}.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{retina}.png",
		{
			server: !stadiaPreferEU ? 'tiles' : 'tiles-eu',
			attribution: '<a href="https://stadiamaps.com/">Stadia Maps</a> | <a href="https://openmaptiles.org/">OpenMapTiles</a>',
			retina: '@2x',
			label: 'Street (Stadia Outdoors)',
		}
	);
	tiles.MAPBOX = new TileLayer(
		"https://api.mapbox.com/styles/v1/{username}/{style_id}/tiles/{z}/{x}/{y}?access_token={access_token}",
		{
			attribution: '<a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://www.mapbox.com/about/maps/" target="_blank">Mapbox</a> ',
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
const attr = (string, el)=>{
	return el.hasAttribute(string) ? el.getAttribute(string) : null;
}
const getCommaSeparatedValue = (string, index)=>{
	let arr = string.split(',');
	return arr[index] ? arr[index].trim() : null;
}
const tileLayersAdd = (map, mapfigure)=>{
	let tileLayers = [];
	// primary tile layer
	let primary = tileLayerConfig(
		attr('data-primary-layer', mapfigure),
		attr('data-mb-user', mapfigure),
		attr('data-mb-token', mapfigure),
		getCommaSeparatedValue(attr('data-mb-id', mapfigure), 0),
		getCommaSeparatedValue(attr('data-mb-label', mapfigure), 0),
		attr('data-stadia-key', mapfigure),
		attr('data-prefer-eu', mapfigure)
	);	
	tileLayers[primary.options.label] = primary; 
	primary.addTo(map);
	// secondary tile layer and layer controls?
	if(attr('data-secondary-layer', mapfigure)){
		let secondary = tileLayerConfig(
			attr('data-secondary-layer', mapfigure),
			attr('data-mb-user', mapfigure),
			attr('data-mb-token', mapfigure),
			getCommaSeparatedValue(attr('data-mb-id', mapfigure), 1),
			getCommaSeparatedValue(attr('data-mb-label', mapfigure), 1),
			attr('data-stadia-key', mapfigure),
			attr('data-prefer-eu', mapfigure)
		);
		tileLayers[secondary.options.label] = secondary;
		const tileLayersControl = new Control.Layers(tileLayers).addTo(map); 
	}
}
const curatescapeMap = (mapcontainer, mapfigure)=>{
	const map = new Map(mapcontainer, {
		scrollWheelZoom: attr('data-maptype', mapfigure) !== "multi",
		tap: false,
	}).setView([attr('data-lat', mapfigure), attr('data-lon', mapfigure)], 13);
	map.attributionControl.setPrefix('');
	tileLayersAdd(map, mapfigure);
}
// @todo intersection observer
document.addEventListener('DOMContentLoaded', ()=>{
	curatescapeMap(document.querySelector('#curatescape-map-canvas'), document.querySelector('#curatescape-map-figure'));
});