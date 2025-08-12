// CONSTANTS
const mapcanvas = document.querySelector('#curatescape-map-canvas');
const mapfigure = document.querySelector('#curatescape-map-figure');
// SCOPED VARS
let map = null;
let bounds = null;
let term = null;
let markers = [];
let styleLayers = [];
let currentStyleLayer = 0;
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
const setStyleLayers = (styleIndex = 0)=>{
	styleLayers[0] = stylesConfig(
		attr('data-primary-layer'),
		getCommaSeparatedValue(attr('data-custom-label'), 0),
		attr('data-stadia-key'),
		attr('data-prefer-eu')
	);
	if(attr('data-secondary-layer')){
		styleLayers[1] = stylesConfig(
			attr('data-secondary-layer'),
			getCommaSeparatedValue(attr('data-custom-label'), 1),
			attr('data-stadia-key'),
			attr('data-prefer-eu')
		);
	}
	currentStyleLayer = styleIndex;
	map.setStyle(styleLayers[currentStyleLayer].url);
}
const subjectSelectControls = ()=>{
	// not a proper control
	let subjectSelect = document.querySelector('#subject-select-control select');
	if(subjectSelect){
		subjectSelect.removeAttribute("hidden");
		subjectSelect.addEventListener("change", (e)=>{
			term = e.target.options[e.target.selectedIndex].value;
			setMarkers(dataSource(term));
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
		container: mapcanvas
	}));
}
const fitBoundsControl = ()=>{
	class FitBoundsControl {
		onAdd(map) {
			this.map = map;
			this.container = document.createElement('div');
			this.container.className = 'maplibregl-ctrl maplibregl-ctrl-group custom fitbounds';
			const button = document.createElement('button');
			button.title = attr('data-fitbounds-label');
			button.style.backgroundImage = `url("data:image/svg+xml;charset=utf-8,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E %3Cpath style='fill:%23333333;' d='M396.795 396.8H320V448h128V320h-51.205zM396.8 115.205V192H448V64H320v51.205zM115.205 115.2H192V64H64v128h51.205zM115.2 396.795V320H64v128h128v-51.205z'/%3E %3C/svg%3E")`;
			button.onclick = () => {
				this.map.fitBounds(bounds, { padding: 50, maxZoom: 15,});
			};
			this.container.appendChild(button);
			return this.container;
		}
		onRemove() {
			this.container.parentNode.removeChild(this.container);
			this.map = undefined;
		}
	}
	return map.addControl(new FitBoundsControl()); 
}
const styleSwapControl = ()=>{
	class StyleSwapControl {
		constructor(){
			this.titlePre = attr('data-style-swap-label') ? attr('data-style-swap-label') + ': ' : '';
			this.defaultIndex = styleLayers[currentStyleLayer + 1] !== 'undefined' ? currentStyleLayer + 1 : 0;
			this.buttonLabelDefault = this.titlePre + styleLayers[this.defaultIndex].label;
		}
		onAdd(map) {
			this.map = map;
			this.container = document.createElement('div');
			this.container.className = 'maplibregl-ctrl maplibregl-ctrl-group custom styleswap';
			const button = document.createElement('button');
			button.title = this.buttonLabelDefault;
			button.style.backgroundImage = `url("data:image/svg+xml;charset=utf-8,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E %3Cpath style='fill:%23333333;' d='M256 256c-13.47 0-26.94-2.39-37.44-7.17l-148-67.49C63.79 178.26 48 169.25 48 152.24s15.79-26 22.58-29.12l149.28-68.07c20.57-9.4 51.61-9.4 72.19 0l149.37 68.07c6.79 3.09 22.58 12.1 22.58 29.12s-15.79 26-22.58 29.11l-148 67.48C282.94 253.61 269.47 256 256 256zm176.76-100.86z'/%3E %3Cpath style='fill:%23333333;' d='M441.36 226.81L426.27 220l-38.77 17.74-94 43c-10.5 4.8-24 7.19-37.44 7.19s-26.93-2.39-37.42-7.19l-94.07-43L85.79 220l-15.22 6.84C63.79 229.93 48 239 48 256s15.79 26.08 22.56 29.17l148 67.63C229 357.6 242.49 360 256 360s26.94-2.4 37.44-7.19l147.87-67.61c6.81-3.09 22.69-12.11 22.69-29.2s-15.77-26.07-22.64-29.19z'/%3E %3Cpath style='fill:%23333333;' d='M441.36 330.8l-15.09-6.8-38.77 17.73-94 42.95c-10.5 4.78-24 7.18-37.44 7.18s-26.93-2.39-37.42-7.18l-94.07-43L85.79 324l-15.22 6.84C63.79 333.93 48 343 48 360s15.79 26.07 22.56 29.15l148 67.59C229 461.52 242.54 464 256 464s26.88-2.48 37.38-7.27l147.92-67.57c6.82-3.08 22.7-12.1 22.7-29.16s-15.77-26.07-22.64-29.2z'/%3E %3C/svg%3E")`;
			button.onclick = () => {
				let nextIndex = typeof styleLayers[currentStyleLayer + 1] !== 'undefined' ? currentStyleLayer + 1 : 0;
				let previousIndex = nextIndex == 1 ? 0 : 1;
				setStyleLayers(nextIndex);
				currentStyleLayer = nextIndex;
				button.title = this.titlePre + styleLayers[previousIndex].label
			};
			this.container.appendChild(button);
			return this.container;
		}
		onRemove() {
			this.container.parentNode.removeChild(this.container);
			this.map = undefined;
		}
	}
	return styleLayers.length > 1 ? map.addControl(new StyleSwapControl()) : null; 
}
const addControls = ()=>{
	subjectSelectControls(); 
	navigationControls();
	geolocationControls();
	styleSwapControl();
	fitBoundsControl(); 
	// fullscreenControls();
	// @todo add view reset control!!!
	// @todo add layer control!!!
}
const markerSVG = (color, featured=false, height=41, width=27)=>{
	return `<svg display="block" height="${height}px" width="${width}px" viewBox="0 0 ${width} ${height}">
		<g fill-rule="nonzero">
			<g transform="translate(3.0, 29.0)" fill="#000000">
				<ellipse opacity="0.04" cx="10.5" cy="5.80029008" rx="10.5" ry="5.25002273"></ellipse>
				<ellipse opacity="0.04" cx="10.5" cy="5.80029008" rx="10.5" ry="5.25002273"></ellipse>
				<ellipse opacity="0.04" cx="10.5" cy="5.80029008" rx="9.5" ry="4.77275007"></ellipse>
				<ellipse opacity="0.04" cx="10.5" cy="5.80029008" rx="8.5" ry="4.29549936"></ellipse>
				<ellipse opacity="0.04" cx="10.5" cy="5.80029008" rx="7.5" ry="3.81822308"></ellipse>
				<ellipse opacity="0.04" cx="10.5" cy="5.80029008" rx="6.5" ry="3.34094679"></ellipse>
				<ellipse opacity="0.04" cx="10.5" cy="5.80029008" rx="5.5" ry="2.86367051"></ellipse>
				<ellipse opacity="0.04" cx="10.5" cy="5.80029008" rx="4.5" ry="2.38636864"></ellipse>
			</g>
			<g fill="${color}">
				<path d="M27,13.5 C27,19.074644 20.250001,27.000002 14.75,34.500002 C14.016665,35.500004 12.983335,35.500004 12.25,34.500002 C6.7499993,27.000002 0,19.222562 0,13.5 C0,6.0441559 6.0441559,0 13.5,0 C20.955844,0 27,6.0441559 27,13.5 Z"></path>
			</g>
			<g opacity="0.2" fill="#000000">
				<path d="M13.5,0 C6.0441559,0 0,6.0441559 0,13.5 C0,19.222562 6.7499993,27 12.25,34.5 C13,35.522727 14.016664,35.500004 14.75,34.5 C20.250001,27 27,19.074644 27,13.5 C27,6.0441559 20.955844,0 13.5,0 Z M13.5,1 C20.415404,1 26,6.584596 26,13.5 C26,15.898657 24.495584,19.181431 22.220703,22.738281 C19.945823,26.295132 16.705119,30.142167 13.943359,33.908203 C13.743445,34.180814 13.612715,34.322738 13.5,34.441406 C13.387285,34.322738 13.256555,34.180814 13.056641,33.908203 C10.284481,30.127985 7.4148684,26.314159 5.015625,22.773438 C2.6163816,19.232715 1,15.953538 1,13.5 C1,6.584596 6.584596,1 13.5,1 Z"></path>
			</g>
			<g style="${attr('data-featured-star') && featured ? 'visibility: hidden' : 'visibility: visible'}" transform="translate(8.0, 8.0)">
				<circle fill="#000000" opacity="0.2" stroke="#000000" stroke-width="2" cx="5.5" cy="5.5" r="5.4999962"></circle>
				<circle fill="#FFFFFF" cx="5.5" cy="5.5" r="5.4999962"></circle>
			</g>
			<g style="${attr('data-featured-star') && featured ? 'visibility: visible' : 'visibility: hidden'}">
				<polygon fill="#000000" stroke="#000000" stroke-width="2" opacity="0.2" points="13.5 17.97 8.02 21.17 9.37 14.97 4.64 10.75 10.95 10.12 13.5 4.32 16.05 10.12 22.36 10.75 17.63 14.97 18.98 21.17 13.5 17.97" />
				<polygon fill="#FFFFFF" points="13.5 17.94 8.02 21.14 9.37 14.94 4.64 10.72 10.95 10.09 13.5 4.28 16.05 10.09 22.36 10.72 17.63 14.94 18.98 21.14 13.5 17.94" />
			</g>
		</g>
	</svg>`;
}
const newMarker = (popup,title,lon,lat,featured)=>{
	let color = featured ? attr('data-featured-color') : attr('data-color');
	const icon = document.createElement('div');
	icon.className = 'maplibregl-marker maplibregl-marker-anchor-center';
	icon.title = title;
	icon.innerHTML = markerSVG(color, featured);
	let marker = new maplibregl.Marker({
		element: icon,
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
	bounds = null
}
const setMarkers = (src)=>{
	setLoading(term);
	fetch(src).then((response) => response.json()).then((data) => {
		removeAllMarkers();
		bounds = new maplibregl.LngLatBounds();
		if(data.items){
			data.items.forEach((item,i)=>{
				let extendloc = new maplibregl.LngLat(item.longitude,item.latitude);
				bounds.extend(extendloc);
				let popup = newPopup(item,i,attr('data-tour'));
				let marker = newMarker(popup,item.title,item.longitude,item.latitude,item.featured);
				markers[item.id] = marker;
			});
			if(attr('data-fixed-center') == '0' || term){
				map.fitBounds(bounds, { padding: 50, maxZoom: 15,});
			}
			removeLoading();
		}
	},(err)=>{
		removeLoading();
	});
}
const pauseInteractivity = ()=>{
	map.scrollZoom.disable();
	map.dragPan.disable();
	map.doubleClickZoom.disable();
	map.keyboard.disable();
	map.boxZoom.disable();
	map.dragRotate.disable();
	map.touchZoomRotate.disable();
}
const resumeInteractivity = ()=>{
	map.scrollZoom.enable();
	map.dragPan.enable();
	map.doubleClickZoom.enable();
	map.keyboard.enable();
	map.boxZoom.enable();
	map.dragRotate.enable();
	map.touchZoomRotate.enable();
}
const setLoading = (term)=>{
	pauseInteractivity();
	let selectIcon = document.querySelector('.curatescape-map #subject-select-control .indicator');
	if( selectIcon ) selectIcon.classList.add('loading');
	
	let mapStatus = document.querySelector('#curatescape-map-canvas #map-status');
	if(mapStatus){
		let message = attr('data-initial-load')
		if(term){
			message += ': '+term;
		}
		mapStatus.innerText = message;
	}
}
const removeLoading = ()=>{
	resumeInteractivity();
	let selectIcon = document.querySelector('.curatescape-map #subject-select-control .indicator');
	if( selectIcon ) selectIcon.classList.remove('loading');
}
const dataSource = (term)=>{
	if(term){
		return `${attr('data-root-url')}/items/browse?search=&advanced[0][element_id]=49&advanced[0][type]=contains&advanced[0][terms]=${term}&output=mobile-json`;
	}else{
		return attr('data-json-source');
	}
}
const newPopup = (item, i, tourid)=>{
	let params = tourid ? "?tour=" + tourid + "&index=" + i : "";
	let href = attr('data-root-url') + "/items/show/" + item.id + params;
	let address = item.address ? item.address.replace(/(<([^>]+)>)/gi, "") : item.latitude + "," + item.longitude;
	let title = item.title;
	if(item.subtitle){
		title += `<span class="curatescape-iw-subtitle"><span class="curatescape-iw-separator">: </span>${item.subtitle}</span>`
	}
	let html = `
	<div class="curatescape-iw">
		<a href="${href}" class="curatescape-iw-image portrait" style="background-image:url(${item.fullsize});"></a>
		<div class="curatescape-iw-content">
			<a href="${href}" class="curatescape-iw-title">${title}</a>
			<div class="curatescape-iw-address">${address}</div>
		</div> 
	</div>`;
	return new maplibregl.Popup({offset: 22}).setHTML(html);
}
// INITIALIZE MAP
// @todo intersection observer
// @todo optimize library load-in
document.addEventListener('DOMContentLoaded', ()=>{
	map = new maplibregl.Map({
		container: mapcanvas,
		center: [attr('data-lon'), attr('data-lat')],
		zoom: attr('data-zoom'),
		bearing: 0,
		scrollZoom: (attr('data-maptype') !== 'multi'),
		attributionControl: {compact: true},
		interactive: false,
	}).once("mousedown",()=>{
		map.scrollZoom.enable();
	});
	setStyleLayers(); // @todo update to handle Custom URLs
	addControls();
	setMarkers(dataSource());
});