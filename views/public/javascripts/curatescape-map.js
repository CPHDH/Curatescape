// CONSTANTS
const mapcanvas = document.querySelector('#curatescape-map-canvas');
const mapfigure = document.querySelector('#curatescape-map-figure');
// SCOPED VARS
let map = null;
let bounds = null;
let markers = [];
// FUNCTIONS
const attr = (string, el = mapfigure)=>{
	return el.hasAttribute(string) ? el.getAttribute(string) : null;
}
const safeText = (value) => {
  var d = document.createElement("div");
  d.innerHTML = value;
  return d.innerText;
};
const getCommaSeparatedValue = (string, index)=>{
	if(!string) return null;
	let arr = string.split(',');
	return arr[index] ? arr[index].trim() : null;
}
const stylesConfig = (name, label, stadiaKey, stadiaPreferEU, fallback = 'OFM_LIBERTY')=>{
	stadiaKey = stadiaKey ? '?api_key='+stadiaKey : '';
	stadiaPreferEU = stadiaPreferEU ? 'tiles-eu' : 'tiles';
	let styles = [];
	styles.OFM_LIBERTY = {
		url: `//tiles.openfreemap.org/styles/liberty`, 
		label: label ? label : 'Open Free Maps | Liberty',
	};
	styles.STADIA_OSMBRIGHT = {
		url: `//${stadiaPreferEU}.stadiamaps.com/styles/osm_bright.json${stadiaKey}`,
		label: label ? label : 'Stadia | OSM Bright',
	};
	styles.STADIA_ALIDADESMOOTH = {
		url: `//${stadiaPreferEU}.stadiamaps.com/styles/alidade_smooth.json${stadiaKey}`,
		label: label ? label : 'Stadia | Alidade Smooth',
	};
	styles.STADIA_ALIDADESMOOTHDARK = {
		url: `//${stadiaPreferEU}.stadiamaps.com/styles/alidade_smooth_dark.json${stadiaKey}`,
		label: label ? label : 'Stadia | Alidade Smooth Dark',
	};
	styles.STADIA_ALIDADESATELLITE = {
		url: `//${stadiaPreferEU}.stadiamaps.com/styles/alidade_satellite.json${stadiaKey}`,
		label: label ? label : 'Stadia | Alidade Satellite',
	};
	styles.STADIA_STAMENTERRAIN = {
		url: `//${stadiaPreferEU}.stadiamaps.com/styles/stamen_terrain.json${stadiaKey}`,
		label: label ? label : 'Stadia | Stamen Terrain',
	};
	styles.STADIA_STAMENTONER =  {
		url: `//${stadiaPreferEU}.stadiamaps.com/styles/stamen_toner_lite.json${stadiaKey}`,
		label: label ? label : 'Stadia | Stamen Toner',
	};
	styles.STADIA_OUTDOORS = {
		url: `//${stadiaPreferEU}.stadiamaps.com/styles/outdoors.json${stadiaKey}`, 
		label: label ? label : 'Stadia | Outdoors',
	};
	styles.CARTO_POSITRON = {
		url: `//basemaps.cartocdn.com/gl/positron-gl-style/style.json`, 
		label: label ? label : 'CartoDB | Positron',
	};
	styles.CARTO_DARKMATTER = {
		url: `//basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json`, 
		label: label ? label : 'CartoDB | Dark Matter',
	};
	styles.CARTO_VOYAGER = {
		url: `//basemaps.cartocdn.com/gl/voyager-gl-style/style.json`, 
		label: label ? label : 'CartoDB | Voyager',
	};
	return typeof styles[name] !== 'undefined' ? styles[name] : styles[fallback];
}

const subjectSelectControls = ()=>{
	// not a proper control
	let subjectSelect = document.querySelector(".curatescape-map select");
	if(subjectSelect){
		subjectSelect.removeAttribute("hidden");
		subjectSelect.addEventListener("change", (e)=>{
			setMarkers(dataSource(e.target.options[e.target.selectedIndex].text));
		})
	}
}
const navigationControls = ()=>{
	return map.addControl(new maplibregl.NavigationControl({
		visualizePitch: true,
		showZoom: true,
		showCompass: true
	}));
}
const geolocationControls = ()=>{
	let geolocate = new maplibregl.GeolocateControl({
		positionOptions: { enableHighAccuracy: true },
		trackUserLocation: true
	});
	geolocate.on('click', () => {
		geolocate.trigger();
	});
	return map.addControl(geolocate);
}
const fullscreenControls = ()=>{
	return map.addControl(new maplibregl.FullscreenControl({
		container: document.querySelector('body')
	}));
}
const fitBoundsControl = ()=>{
	class FitBoundsControl {
		constructor(bounds, options) {
			this.bounds = bounds; 
			this.options = options || {}; 
		}
		onAdd(map) {
			this.map = map;
			this.container = document.createElement('div');
			this.container.className = 'maplibregl-ctrl maplibregl-ctrl-group';
			const button = document.createElement('button');
			button.title = attr('data-fitbounds-label');
			// button.textContent = ''; // @todo icon
			button.onclick = () => {
				this.map.fitBounds(this.bounds, this.options);
			};
			this.container.appendChild(button);
			return this.container;
		}
		onRemove() {
			this.container.parentNode.removeChild(this.container);
			this.map = undefined;
		}
	}
	return map.addControl(new FitBoundsControl([[-79, 43], [-73, 45]],{ padding: 50, maxZoom: 15,}), 'top-right'); 
}
const addControls = ()=>{
	subjectSelectControls(); // @todo FIX!
	navigationControls();
	geolocationControls();
	fullscreenControls();
	fitBoundsControl(); // @todo add actual bounds!!!! add icon!!!
	// @todo add view reset control!!!
	// @todo add pitch control!!!
	// @todo add layer control!!!
}

const setStyleLayers = ()=>{
	let styleLayers = [];
	let primary = stylesConfig(
		attr('data-primary-layer'),
		getCommaSeparatedValue(attr('data-mb-label'), 0),
		attr('data-stadia-key'),
		attr('data-prefer-eu')
	);
	styleLayers[primary.label] = primary;
	map.setStyle(primary.url);
	// if(attr('data-secondary-layer')){
	// 	let secondary = stylesConfig(
	//		attr('data-secondary-layer')
	// 		getCommaSeparatedValue(attr('data-mb-label'), 1),
	// 		attr('data-stadia-key'),
	// 		attr('data-prefer-eu')
	// 	);
	// 	styleLayers[secondary.label] = secondary;
	// 	map.setStyle(secondary.url);
	// 	// const tileLayersControl = new Control.Layers(tileLayers).addTo(map); 
	// }
}
const newMarker = (popup,lon,lat,featured)=>{
	let marker = new maplibregl.Marker({
		color: featured ? attr('data-featured-color') : attr('data-color'),
	});
	marker.setLngLat([lon,lat]);
	marker.setPopup(popup);
	marker.addTo(map);
	return marker;
}
const removeAllMarkers = ()=>{
  markers.forEach((m)=>{
	m.remove()
  });
  markers=[];
}
const setMarkers = (src)=>{
	removeAllMarkers();
	fetch(src).then((response) => response.json()).then((data) => {
		if(data.items){
			data.items.forEach((item,i)=>{
				let popup = new maplibregl.Popup().setHTML(popupContent(item,i,attr('data-tour')));
				let marker = newMarker(popup,item.longitude,item.latitude,item.featured);
				markers[item.id] = marker;
			});
		}
	});
}
const dataSource = (term)=>{
	if(term){
		return  `${attr('data-root-url')}/items/browse?search=&advanced[0][element_id]=49&advanced[0][type]=contains&advanced[0][terms]=${term}&output=mobile-json`;
	}else{
		return attr('data-json-source');
	}
}
const popupContent = (item, i, tourid)=>{
	let params = tourid ? "?tour=" + tourid + "&index=" + i : "";
	let href = attr('data-root-url') + "/items/show/" + item.id + params;
	let address = item.address ? item.address.replace(/(<([^>]+)>)/gi, "") : item.latitude + "," + item.longitude;
	let title = item.title;
	if(item.subtitle){
		title += `<span class="curatescape-iw-subtitle"><span class="curatescape-iw-separator">: </span>${item.subtitle}</span>`
	}
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
// INITIALIZE MAP
// @todo intersection observer
// @todo optimize library load-in
document.addEventListener('DOMContentLoaded', ()=>{
	map = new maplibregl.Map({
		container: mapcanvas,
		center: [-81.693637, 41.499678],
		zoom: 15,
		bearing: 0,
		// pitch: 15,
		// scrollZoom: false,
		// interactive: false,
	});
	setStyleLayers(); // @todo update to handle Custom URLs
	addControls();
	setMarkers(dataSource());
});