class CuratescapeMap extends HTMLElement {
	// using shadow DOM to isolate map styles from theme css
	constructor() {
		super();
		this.fig = document.querySelector('#curatescape-map-figure');
		this.shadow = this.attachShadow({ mode: "open" });
	}
	connectedCallback() {
		this.styleSheet();
		this.uiElements();
	}
	disconnectedCallback() {
		resetMap();
		resetPopups();
		resetMarkerRequest();
		resetMarkerEvents();
		resetSkipLink();
		bounds = null;
		term = null;
		geojson = null;
		styleLayers.length = 0;
		currentStyleLayer = 0;
		bitmapMarkerReg = null;
		markerBitmapFeat = null;
	}
	uiElements() {
		this.shadow.appendChild(this.fig);
	}
	styleSheet() {
		const css = new CSSStyleSheet();
		css.replaceSync(`:host{}`);
		this.shadow.adoptedStyleSheets = [css];
	}
}
// USER PREFS
const prefReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
// ELEMENTS
const mapcanvas = document.querySelector('#curatescape-map-canvas');
const mapfigure = document.querySelector('#curatescape-map-figure');
const mapfigcaption = document.querySelector("#curatescape-map-caption");
const subjectSelect = document.querySelector('#subject-select-control select');
const skipLink = document.querySelector('[data-curatescape-map-keyboard-only]');
const ariaLiveRegion = document.querySelector('#curatescape-map-canvas #map-status');
// SCOPED VARS
let map = null;
let bounds = null;
let term = null;
let geojson = null;
let popups = [];
let styleLayers = [];
let currentStyleLayer = 0;
let bitmapMarkerReg = null;
let markerBitmapFeat = null;
let loadingIndicator = null;
// TRACKED EVENTS
let subjectSelectListener = null;
let markerRequestListener = null;
let markerClick = null;
let clusterClick = null;
let cursorPointer = null;
let cursorDefault = null;
let skipHandler = null;
// FUNCTIONS
const htmlEntities = (value) => { // safe/plain text
	var d = document.createElement('div');
	d.innerHTML = value;
	return d.innerText;
}
const attr = (string, asInteger = false, el = mapfigure) => {
	let value = el && el.hasAttribute(string) ? el.getAttribute(string) : null
	return asInteger ? parseInt(value) : value;
}
const announce = (message) => { // aria-live region status updates
	if (!ariaLiveRegion) return;
	ariaLiveRegion.innerText = '';
	// timeout to announce identical messages
	setTimeout(()=>{ariaLiveRegion.innerText = htmlEntities(message)}, 100);
}
const getCommaSeparatedValue = (string, index, allowFallback = false, fallbackIndex = 0) => {
	if (!string) return null;
	let arr = string.split(',');
	let value = arr[index] ? arr[index].trim() : null;
	if(!value && allowFallback){
		// fallback used to address potential index mismatch in custom map settings
		// e.g. when only *second* layer is set to CUSTOM_URL (at *first/only* index)
		value = arr[fallbackIndex] ? arr[fallbackIndex].trim() : null;
	}
	return value;
}
const rgbParse = (color) => { // returns a comma-separated string, eg 256,256,256
	if (!color) return null;
	try {
		let el = document.createElement('span');
		el.style.color = color;
		el.style.visibility = 'hidden';
		el.style.position = 'absolute';
		document.body.appendChild(el);
		let rgb = window.getComputedStyle(el).color;
		let match = rgb.match(/\d+/g);
		document.body.removeChild(el);
		return match ? match.slice(0, 3).join(',') : null;
	} catch(error) {
		console.error('Failed to parse RGB colors:', error);
		return;
	}
}
const toBitmap = (svgString, retina = true, width = 27, height = 41, mime = 'image/png') => { // symbol layers must use bitmap
	if (retina) {
		width = width * 2;
		height = height * 2;
	}
	return new Promise((resolve, reject) => {
		const img = new Image();
		img.onload = () => {
			const canvas = document.createElement('canvas');
			canvas.width = width;
			canvas.height = height;
			const ctx = canvas.getContext('2d');
			ctx.clearRect(0, 0, width, height);
			ctx.drawImage(img, 0, 0, width, height);
			const pngDataURL = canvas.toDataURL(mime);
			resolve(pngDataURL);
		};
		img.onerror = () => reject(new Error('Failed to load SVG image'));
		img.src = 'data:image/svg+xml;base64,' + btoa(svgString);
	});
};
const markerSVG = (color, featured = false, star = false, height = 41, width = 27) => {
	color = (color && typeof color === 'string') ? color : '#2c83cb';
	return `<svg display="block" height="${height}px" width="${width}px" viewBox="0 0 ${width} ${height}" xmlns="http://www.w3.org/2000/svg">
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
			<g style="${star && featured ? 'visibility: hidden' : 'visibility: visible'}" transform="translate(8.0, 8.0)">
				<circle fill="#000000" opacity="0.2" stroke="#000000" stroke-width="2" cx="5.5" cy="5.5" r="5.4999962"></circle>
				<circle fill="#FFFFFF" cx="5.5" cy="5.5" r="5.4999962"></circle>
			</g>
			<g style="${star && featured ? 'visibility: visible' : 'visibility: hidden'}">
				<polygon fill="#000000" stroke="#000000" stroke-width="2" opacity="0.2" points="13.5 17.97 8.02 21.17 9.37 14.97 4.64 10.75 10.95 10.12 13.5 4.32 16.05 10.12 22.36 10.75 17.63 14.97 18.98 21.17 13.5 17.97" />
				<polygon fill="#FFFFFF" points="13.5 17.94 8.02 21.14 9.37 14.94 4.64 10.72 10.95 10.09 13.5 4.28 16.05 10.09 22.36 10.72 17.63 14.94 18.98 21.14 13.5 17.94" />
			</g>
		</g>
	</svg>`;
}
const stylesConfig = (name, label, stadiaKey, preferEU, styleIndex = 0, fallback = 'CARTO_VOYAGER') => {
	stadiaKey = stadiaKey ? '?api_key=' + stadiaKey : '';
	preferEU = Boolean(preferEU) ? 'tiles-eu' : 'tiles';
	let styles = [];
	styles.OFM_LIBERTY = {
		url: `//tiles.openfreemap.org/styles/liberty`,
		label: label ? label : 'Open Free Maps | Liberty',
	};
	styles.STADIA_OSMBRIGHT = {
		url: `//${preferEU}.stadiamaps.com/styles/osm_bright.json${stadiaKey}`,
		label: label ? label : 'Stadia | OSM Bright',
	};
	styles.STADIA_ALIDADESMOOTH = {
		url: `//${preferEU}.stadiamaps.com/styles/alidade_smooth.json${stadiaKey}`,
		label: label ? label : 'Stadia | Alidade Smooth',
	};
	styles.STADIA_ALIDADESMOOTHDARK = {
		url: `//${preferEU}.stadiamaps.com/styles/alidade_smooth_dark.json${stadiaKey}`,
		label: label ? label : 'Stadia | Alidade Smooth Dark',
	};
	styles.STADIA_ALIDADESATELLITE = {
		url: `//${preferEU}.stadiamaps.com/styles/alidade_satellite.json${stadiaKey}`,
		label: label ? label : 'Stadia | Alidade Satellite',
	};
	styles.STADIA_STAMENTERRAIN = {
		url: `//${preferEU}.stadiamaps.com/styles/stamen_terrain.json${stadiaKey}`,
		label: label ? label : 'Stadia | Stamen Terrain',
	};
	styles.STADIA_STAMENTONER = {
		url: `//${preferEU}.stadiamaps.com/styles/stamen_toner_lite.json${stadiaKey}`,
		label: label ? label : 'Stadia | Stamen Toner',
	};
	styles.STADIA_OUTDOORS = {
		url: `//${preferEU}.stadiamaps.com/styles/outdoors.json${stadiaKey}`,
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
	styles.CUSTOM_URL = {
		url: getCommaSeparatedValue(attr('data-custom-url'), styleIndex, true),
		label: label ? label : 'Custom Style',
	};
	return typeof styles[name] !== 'undefined' ? styles[name] : styles[fallback];
}
const setStyleLayers = (styleIndex = 0) => {
	styleLayers[0] = stylesConfig(
		attr('data-primary-layer'),
		getCommaSeparatedValue(attr('data-custom-label'), 0),
		attr('data-stadia-key'),
		attr('data-prefer-eu', true),
		0
	);
	if (attr('data-secondary-layer')) {
		styleLayers[1] = stylesConfig(
			attr('data-secondary-layer'),
			getCommaSeparatedValue(attr('data-custom-label'), 1),
			attr('data-stadia-key'),
			attr('data-prefer-eu', true),
			1
		);
	}
	map.setStyle(styleLayers[styleIndex].url);
}
const subjectSelectControls = () => {
	// @todo: convert to native control *then* add fullscreen button
	let data = attr('data-terms-json');
	if(!data) return;
	class SubjectSelectControl{
		constructor() {
			this.json = decodeURIComponent(data);
		}
		onAdd(map) {
			this.map = map;
			this.container = document.createElement('div');
			this.container.className = 'maplibregl-ctrl maplibregl-ctrl-group custom subject-select';
			this.indicator = document.createElement('span');
			this.indicator.className = 'indicator';
			this.container.appendChild(this.indicator);
			this.select = document.createElement('select');
			try{
				JSON.parse(this.json).forEach((term)=>{
					let option = document.createElement('option');
					option.value = term.default ? '' : term.text
					option.innerText = `${decodeURIComponent(term.text.replace(/\+/g, " "))}: ${term.total}`;
					this.select.appendChild(option);
				});
			} catch(error) {
				console.error('Failed to parse terms for subject select controls:', error);
				return;
			}
			this.changeHandler = (e) => {
				resetPopups();
				term = e.target.options[e.target.selectedIndex].value;
				setMarkers(dataSource(term));
			}
			this.select.addEventListener("change", this.changeHandler);
			this.container.appendChild(this.select);
			loadingIndicator = this.indicator; // see setLoading()...
			return this.container;
		}
		onRemove(map) {
			if (this.select && this.changeHandler) {
				this.select.removeEventListener('change', this.changeHandler);
			}
			if (this.container && this.container.parentNode) {
				this.container.parentNode.removeChild(this.container);
			}
			this.changeHandler = null;
			this.select = null;
			this.indicator = null;
			this.container = null;
			this.json = null;
			this.map = null;
		}
	}
	return map.addControl(new SubjectSelectControl(), 'top-left');
	
}
const fitBoundsControl = () => {
	class FitBoundsControl {
		onAdd(map) {
			this.map = map;
			this.container = document.createElement('div');
			this.container.className = 'maplibregl-ctrl maplibregl-ctrl-group custom fitbounds';
			this.button = document.createElement('button');
			this.button.title = this.button.ariaLabel = attr('data-fitbounds-label');
			this.button.style.backgroundImage = `url("data:image/svg+xml;charset=utf-8,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E %3Cpath style='fill:%23333333;' d='M396.795 396.8H320V448h128V320h-51.205zM396.8 115.205V192H448V64H320v51.205zM115.205 115.2H192V64H64v128h51.205zM115.2 396.795V320H64v128h128v-51.205z'/%3E %3C/svg%3E")`;
			this.clickHandler = () => {
				if (this.map && bounds) {
					this.map.fitBounds(bounds, { 
						padding: {top: 50, bottom:25, left: 25, right: 75}, 
						maxZoom: 15,
						animate: !prefReducedMotion, 
					});
				}
			};
			this.button.addEventListener('click', this.clickHandler);
			this.container.appendChild(this.button);
			return this.container;
		}
		onRemove() {
			if (this.button && this.clickHandler) {
				this.button.removeEventListener('click', this.clickHandler);
			}
			if (this.container && this.container.parentNode) {
				this.container.parentNode.removeChild(this.container);
			}
			this.clickHandler = null;
			this.button = null;
			this.container = null;
			this.map = null;
		}
	}
	return map.addControl(new FitBoundsControl());
}
const styleSwapControl = () => {
	class StyleSwapControl {
		constructor() {
			this.titlePre = attr('data-style-swap-label') ? attr('data-style-swap-label') + ': ' : '';
			this.defaultIndex = typeof styleLayers[currentStyleLayer + 1] !== 'undefined' ? currentStyleLayer + 1 : 0;
			this.buttonLabelDefault = this.titlePre + styleLayers[this.defaultIndex].label;
		}
		onAdd(map) {
			this.map = map;
			this.container = document.createElement('div');
			this.container.className = 'maplibregl-ctrl maplibregl-ctrl-group custom styleswap';
			this.button = document.createElement('button');
			this.button.title = this.button.ariaLabel = this.buttonLabelDefault;
			this.button.style.backgroundImage = `url("data:image/svg+xml;charset=utf-8,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E %3Cpath style='fill:%23333333;' d='M256 256c-13.47 0-26.94-2.39-37.44-7.17l-148-67.49C63.79 178.26 48 169.25 48 152.24s15.79-26 22.58-29.12l149.28-68.07c20.57-9.4 51.61-9.4 72.19 0l149.37 68.07c6.79 3.09 22.58 12.1 22.58 29.12s-15.79 26-22.58 29.11l-148 67.48C282.94 253.61 269.47 256 256 256zm176.76-100.86z'/%3E %3Cpath style='fill:%23333333;' d='M441.36 226.81L426.27 220l-38.77 17.74-94 43c-10.5 4.8-24 7.19-37.44 7.19s-26.93-2.39-37.42-7.19l-94.07-43L85.79 220l-15.22 6.84C63.79 229.93 48 239 48 256s15.79 26.08 22.56 29.17l148 67.63C229 357.6 242.49 360 256 360s26.94-2.4 37.44-7.19l147.87-67.61c6.81-3.09 22.69-12.11 22.69-29.2s-15.77-26.07-22.64-29.19z'/%3E %3Cpath style='fill:%23333333;' d='M441.36 330.8l-15.09-6.8-38.77 17.73-94 42.95c-10.5 4.78-24 7.18-37.44 7.18s-26.93-2.39-37.42-7.18l-94.07-43L85.79 324l-15.22 6.84C63.79 333.93 48 343 48 360s15.79 26.07 22.56 29.15l148 67.59C229 461.52 242.54 464 256 464s26.88-2.48 37.38-7.27l147.92-67.57c6.82-3.08 22.7-12.1 22.7-29.16s-15.77-26.07-22.64-29.2z'/%3E %3C/svg%3E")`;
			this.clickHandler = () => {
				if (!this.map || !styleLayers || styleLayers.length <= 1) return;
				let nextIndex = typeof styleLayers[currentStyleLayer + 1] !== 'undefined' ? currentStyleLayer + 1 : 0;
				let previousIndex = nextIndex == 1 ? 0 : 1;
				map.once('styledata', () => {
					currentStyleLayer = nextIndex;
					this.button.title = this.button.ariaLabel = this.titlePre + styleLayers[previousIndex].label;
					setMarkers(dataSource(term), false);
				});
				setStyleLayers(nextIndex);
			};
			this.button.addEventListener('click', this.clickHandler);
			this.container.appendChild(this.button);
			return this.container;
		}
		onRemove() {
			if (this.button && this.clickHandler) {
				this.button.removeEventListener('click', this.clickHandler);
			}
			if (this.container && this.container.parentNode) {
				this.container.parentNode.removeChild(this.container);
			}
			this.clickHandler = null;
			this.button = null;
			this.container = null;
			this.map = null;
		}
	}
	return styleLayers.length > 1 ? map.addControl(new StyleSwapControl()) : null;
}
const keyboardEnhancements = () => {
	class KeyboardAltControls {
		onAdd(map) {
			this.mapcanvas = mapcanvas;
			this.mapfigcaption = mapfigcaption;
			this.container = document.createElement('div');
			this.container.className = 'maplibregl-ctrl maplibregl-ctrl-group custom keyboard-only skiplink';
			// SKIP
			this.skipButton = document.createElement('button');
			this.skipButton.innerText = attr('data-skip-link-label');
			this.skipHandler = async (e) =>{
				e.preventDefault();
				if (document.fullscreenElement) {
					await document.exitFullscreen();
				}
				this.mapfigcaption.setAttribute('tabIndex','0');
				this.mapfigcaption.scrollIntoView({
					behavior: (prefReducedMotion ? 'instant' : 'smooth'),
					block: 'start',
				})
				this.mapfigcaption.focus({preventScroll: true});
				this.mapfigcaption.removeAttribute('tabIndex');
			}
			this.skipButton.addEventListener('click', this.skipHandler);
			this.container.appendChild(this.skipButton);
			// multi maps only...
			if(attr('data-maptype') !== 'multi') return this.container;
			// LIST
			this.listButton = document.createElement('button');
			this.listButton.innerText = getCommaSeparatedValue(attr('data-map-list-labels'), 0);
			this.listHandler = (e) =>{
				pauseInteractivity();
				e.preventDefault();
				this.pois = map.getSource('pois');
				this.pois.getData().then((data)=>{
					this.results = '';
					this.results = document.createElement('div');
					this.results.className = 'keyboard-results';
					this.results.setAttribute('role','region');
					this.results.setAttribute('aria-label', getCommaSeparatedValue(attr('data-marker-labels'), 1));
					// close button
					this.closeButton = document.createElement('button');
					this.closeButton.id = 'keyboard-close-button';
					this.closeButton.ariaLabel = 'ESC';
					this.closeHandler = (e) => {
						e.preventDefault();
						this.results = '';
						if(this.escapeHandler){
							document.removeEventListener('keyup', this.escapeHandler);
						}
						e.target.parentElement.remove();
						resumeInteractivity();
					}
					this.closeButton.addEventListener('click', this.closeHandler);
					this.results.appendChild(this.closeButton);
					// escape
					this.escapeHandler = (e) => {
						e = e || window.event;
						var isEscape = false;
						if ("key" in e) {
							isEscape = (e.key === "Escape" || e.key === "Esc");
						} else {
							isEscape = (e.keyCode === 27);
						}
						if (isEscape) {
							this.closeButton.click();
						}
					};
					document.addEventListener('keyup', this.escapeHandler);
					// focus out
					this.focusOutHandler = (e) => {
						if (this.results.contains(e.relatedTarget)) return;
						this.results.remove();
						this.skipButton.focus();
					}
					this.results.addEventListener('focusout', this.focusOutHandler);
					// item list
					let tourid = attr('data-tour');
					let ul = document.createElement('ul');
					data.features.forEach(i =>{
						let props = i.properties;
						let params = tourid ? "?tour=" + tourid + "&index=" + props.index : "";
						let li = document.createElement('li');
						let a = document.createElement('a');
							a.className = 'keyboard-item-link';
							a.href = htmlEntities(`/items/show/${props.id + params}`);
							let subtitle = props.subtitle ? ': '+props.subtitle : '';
							a.innerHTML = `<strong>${htmlEntities(props.title + subtitle)}</strong>`;
						if(props.address){
							a.innerHTML += `<small>${htmlEntities(props.address)}</small>`;
						}
						li.appendChild(a);
						ul.appendChild(li);
						this.results.appendChild(ul);
					});
					this.mapcanvas.appendChild(this.results);
					this.results.setAttribute('tabIndex','0');
					this.results.focus();
					this.results.removeAttribute('tabIndex');
					announce(getCommaSeparatedValue(attr('data-map-list-labels'), 1));
				});
			}
			this.listButton.addEventListener('click', this.listHandler);
			this.container.appendChild(this.listButton);
			return this.container;
		}
		onRemove() {
			if (this.skipButton && this.skipHandler) {
				this.skipButton.removeEventListener('click', this.skipHandler);
			}
			if (this.listButton && this.listHandler) {
				this.listButton.removeEventListener('click', this.listHandler);
			}
			if(this.closeButton && this.closeHandler) {
				this.closeButton.removeEventListener('click', this.closeHandler);
			}
			if(this.results && this.focusOutHandler){
				this.results.removeEventListener('focusout', this.focusOutHandler);
			}
			if (this.container && this.container.parentNode) {
				this.container.parentNode.removeChild(this.container);
			}
			if(this.escapeHandler){
				document.removeEventListener('keyup', this.escapeHandler);
			}
			this.skipHandler = null;
			this.skipButton = null;
			this.listHandler = null;
			this.listButton = null;
			this.closeButton = null;
			this.closeHandler = null;
			this.escapeHandler = null;
			this.focusOutHandler = null;
			this.container = null;
			this.pois = null;
			this.mapcanvas = null;
			this.mapfigcaption = null;
			this.results = null;
		}
	}
	return map.addControl(new KeyboardAltControls(), 'top-left');
}
const navigationControls = () => {
	return map.addControl(new maplibregl.NavigationControl({
		visualizePitch: true,
		showZoom: true,
		showCompass: true
	}));
}
const geolocationControls = () => {
	let geolocate = new maplibregl.GeolocateControl({
		positionOptions: { enableHighAccuracy: true },
		trackUserLocation: false
	});
	geolocate.on('click', () => {
		geolocate.trigger();
	});
	return map.addControl(geolocate);
}
const fullscreenControls = () => {
	return map.addControl(new maplibregl.FullscreenControl());
}
const addControls = () => {
	if (!map) return;
	keyboardEnhancements();
	subjectSelectControls();
	fullscreenControls();
	navigationControls();
	geolocationControls();
	styleSwapControl();
	fitBoundsControl();
}
const pauseInteractivity = () => {
	if (!map) return;
	map.scrollZoom.disable();
	map.dragPan.disable();
	map.doubleClickZoom.disable();
	map.keyboard.disable();
	map.boxZoom.disable();
	map.dragRotate.disable();
	map.touchZoomRotate.disable();
}
const resumeInteractivity = () => {
	if (!map) return;
	map.scrollZoom.enable();
	map.dragPan.enable();
	map.doubleClickZoom.enable();
	map.keyboard.enable();
	map.boxZoom.enable();
	map.dragRotate.enable();
	map.touchZoomRotate.enable();
}
const setLoading = (term) => {
	pauseInteractivity();
	if (loadingIndicator) loadingIndicator.classList.add('loading');
	let message = attr('data-initial-load');
	if(term){
		message += ': ' + decodeURIComponent(term.replace(/\+/g, " "));
	}
	announce(message);
}
const removeLoading = () => {
	resumeInteractivity();
	if (loadingIndicator) loadingIndicator.classList.remove('loading');
}
const dataSource = (term) => {
	if (term) {
		return `${attr('data-root-url')}/items/browse?search=&advanced[0][element_id]=49&advanced[0][type]=contains&advanced[0][terms]=${term}&output=mobile-json`;
	} else {
		return attr('data-json-source');
	}
}
const flyToById = async (id, zoom = 16) => {
	if (!map) return;
	const targetFeature = geojson.features.find(feature =>
		feature.properties.id == id || feature.properties.id == parseInt(id)
	);
	if (!targetFeature) {
		console.warn(`Marker with id ${id} not found`);
		return;
	}
	const coordinates = targetFeature.geometry.coordinates;
	map.once('moveend', () => {
		setPopup(targetFeature.properties);
	});
	map.flyTo({
		center: coordinates,
		zoom: zoom,
		essential: true,
		animate: !prefReducedMotion,
		offset: [0, 88],
	});
};
const resetMarkerRequest = () => {
	if (markerRequestListener) {
		document.removeEventListener("markerRequest", markerRequestListener);
		markerRequestListener = null;
	}
};
const initMarkerRequestListener = () => {
	if (!mapfigure) return;
	resetMarkerRequest();
	markerRequestListener = (e) => {
		mapfigure.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
		flyToById(e.detail);
	}
	document.addEventListener('markerRequest', markerRequestListener);
}
const resetMarkerLayers = (clusters) => {
	if (clusters) {
		if (map.getLayer('clusters-shader')) map.removeLayer('clusters-shader');
		if (map.getLayer('clusters')) map.removeLayer('clusters');
		if (map.getLayer('cluster-count')) map.removeLayer('cluster-count');
	}
	if (map.getLayer('unclustered-point')) map.removeLayer('unclustered-point');
	if (map.getSource('pois')) map.removeSource('pois');
}
const markerLayers = async (geojson, clusters = false) => {
	if (!map) return;
	clusters = Boolean(clusters);
	// Cleanup
	resetMarkerLayers(clusters);
	// Source
	let sourceConfig = {
		type: 'geojson',
		data: geojson,
	}
	if (clusters) {
		sourceConfig.cluster = true;
		sourceConfig.clusterMaxZoom = 16;
		sourceConfig.clusterRadius = 40;
	}
	map.addSource('pois', sourceConfig);
	// Layer(s)
	let layerConfig = {
		id: 'unclustered-point',
		type: 'symbol',
		source: 'pois',
		layout: {
			'icon-image': [
				'case',
				['==', ['get', 'featured'], 1],
				'marker-featured',
				'marker-regular'
			],
			'icon-size': 0.5,
			'icon-allow-overlap': true,
		},
	}
	if (clusters) {
		layerConfig.filter = ['!', ['has', 'point_count']];
	}
	map.addLayer(layerConfig);
	if (clusters) {
		clusterLayers();
	}
}
const clusterLayers = (clusterColors = ['110,204,57', '240,194,12', '241,128,23']) => {
	if (!map) return;
	// User colors?
	if (attr('data-cluster-colors')) {
		let dataColors = attr('data-cluster-colors').split('|').map(color => rgbParse(color.trim()));
		clusterColors = dataColors.length === 3 ? dataColors : clusterColors; // exactly 3 colors required
	}
	// Config
	let clusterConfig = {
		small: {
			size: 12,
			rgb: clusterColors[0],
			max: 10,
		},
		medium: {
			size: 16,
			rgb: clusterColors[1],
			max: 50,
		},
		large: {
			size: 20,
			rgb: clusterColors[2],
		},
		shade: {
			rgb: '0,0,0',
		}
	}
	// Helpers (mainly for clarity)
	let color = (size, opacity = 1) => `rgba(${clusterConfig[size].rgb},${opacity})`;
	let size = (size, plus = 0) => clusterConfig[size].size + plus;
	let max = (size) => clusterConfig[size].max;
	let borderWidth = 6;
	// Background shade
	map.addLayer({
		id: 'clusters-shader',
		type: 'circle',
		source: 'pois',
		filter: ['has', 'point_count'],
		paint: {
			'circle-color': color('shade', 0.75),
			'circle-radius': ['step', ['get', 'point_count'],
				size('small', borderWidth), max('small'),
				size('medium', borderWidth), max('medium'),
				size('large', borderWidth),
			],
		},
	});
	// Circle
	map.addLayer({
		id: 'clusters',
		type: 'circle',
		source: 'pois',
		filter: ['has', 'point_count'],
		paint: {
			'circle-color': ['step', ['get', 'point_count'],
				color('small', 0.7), max('small'),
				color('medium', 0.7), max('medium'),
				color('large', 0.7),
			],
			'circle-radius': ['step', ['get', 'point_count'],
				size('small'), max('small'),
				size('medium'), max('medium'),
				size('large'),
			],
			'circle-stroke-width': borderWidth,
			'circle-stroke-color': ['step', ['get', 'point_count'],
				color('small', 0.9), max('small'),
				color('medium', 0.9), max('medium'),
				color('large', 0.9),
			],
		},
	});
	// Count
	map.addLayer({
		id: 'cluster-count',
		type: 'symbol',
		source: 'pois',
		filter: ['has', 'point_count'],
		layout: {
			'text-field': '{point_count_abbreviated}',
			'text-size': 14,
		},
		paint: {
			'text-color': '#ffffff',
			'text-opacity': 1.0,
			'text-halo-color': color('shade', 0.15),
			'text-halo-width': 0.25,
			'text-halo-blur': 2
		}
	});
}
const resetPopups = () => {
	popups.forEach(p => {
		p.remove();
	});
	popups.length = 0;
}
const resetMap = () => {
	if (map) {
		map.remove();
	}
	map = null;
}
const setPopup = (props) => {
	resetPopups();
	let tourid = attr('data-tour');
	let params = tourid ? "?tour=" + tourid + "&index=" + props.index : "";
	let subtitle = props.subtitle ? ': '+props.subtitle : '';
	let infowindow = `
	<div class="curatescape-iw">
		<a href="${attr('data-root-url')}/items/show/${props.id + params}" class="curatescape-iw-image portrait" style="background-image:url(${props.fullsize});"></a>
		<div class="curatescape-iw-content">
			<a href="${attr('data-root-url')}/items/show/${props.id}" class="curatescape-iw-title">
				${htmlEntities(props.title + subtitle)}
			</a>
			<div class="curatescape-iw-address">
				${props.address ? htmlEntities(props.address) : htmlEntities(props.latitude + ',' + props.longitude)}
			</div>
		</div>
	</div>`;
	let popup = new maplibregl.Popup({ offset: 22, closeButton: true }).setLngLat([props.longitude, props.latitude]).setHTML(infowindow);
	popups.push(popup);
	popup.addTo(map);
}
const setMarkers = async (src, fitBoundsAllowed = true, initialLoad = false) => {
	if (!map) return;
	// loading...
	setLoading(term);
	// wait for marker images...
	if(initialLoad) await addImageSources();
	// Data
	fetch(src).then((response) => {
		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}
		return response.json()
	}).then((data) => {
		// Curatescape JSON -> GeoJSON FeatureCollection
		let items = attr('data-maptype') === 'multi' ? data.items : [data];
		geojson = {
			type: 'FeatureCollection',
			features: (items || []).map((item, index) => ({
				type: 'Feature',
				geometry: { type: 'Point', coordinates: [+item.longitude, +item.latitude] },
				properties: { ...item, index },
			})),
		};
		// Add Markers
		markerLayers(geojson, attr('data-cluster', true));
		// Bounds
		bounds = new maplibregl.LngLatBounds();
		geojson.features.forEach((f) => bounds.extend(f.geometry.coordinates));
		if (fitBoundsAllowed && (!attr('data-fixed-center', true) || term)) {
			map.fitBounds(bounds, { 
				padding: {top: 25, bottom:25, left: 75, right: 75}, 
				maxZoom: 15, 
				animate: !prefReducedMotion && items.length !== 1, // no animation for single
			});
		}
		// Events
		initMarkerEvents();
		// end loading...
		removeLoading();
	}).catch((err) => {
		removeLoading();
		console.error('Failed to load markers:', err);
	});
};
const resetMarkerEvents = () => {
	if (!map) return;
	map.off('click', 'unclustered-point', markerClick);
	map.off('click', 'clusters', clusterClick);
	markerClick = null;
	clusterClick = null;
	if (map.getLayer('clusters')) {
		map.off('mouseenter', 'clusters', cursorPointer);
		map.off('mouseleave', 'clusters', cursorDefault);
	}
	if (map.getLayer('unclustered-point')) {
		map.off('mouseenter', 'unclustered-point', cursorPointer);
		map.off('mouseleave', 'unclustered-point', cursorDefault);
	}
	cursorPointer = null;
	cursorDefault = null;
}
const initMarkerEvents = () => {
	if (!map) return;
	resetMarkerEvents();
	// Marker Click
	markerClick = (e) => {
		let props = e.features[0].properties;
		let message = getCommaSeparatedValue(attr('data-marker-labels'), 0);
		if(props.title){
			message += ': ' + props.title
		}
		if(props.address){
			message += ' (' + props.address + ')'
		}
		announce(message);
		if(attr('data-maptype') !== 'single'){
			setPopup(props);
		}
	}
	map.on('click', 'unclustered-point', markerClick);
	// Cluster Click
	clusterClick = async (e) => {
		const clusterId = e.features[0].properties.cluster_id;
		const coords = e.features[0].geometry.coordinates;
		const zoom = await map.getSource('pois').getClusterExpansionZoom(clusterId);
		const leaves = await map.getSource('pois').getClusterLeaves(clusterId);
		let message = getCommaSeparatedValue(attr('data-cluster-labels'), 0);
		if(leaves.length){
			message += ': ' + leaves.length + ' ' + getCommaSeparatedValue(attr('data-marker-labels'), 1)
		}
		announce(message);
		map.flyTo({
			center: coords,
			zoom: zoom,
			essential: true,
			animate: !prefReducedMotion,
		});
	}
	if (map.getSource('pois')) {
		map.on('click', 'clusters', clusterClick);
	}
	// Cursor Management
	cursorPointer = () => map.getCanvas().style.cursor = 'pointer';
	cursorDefault = () => map.getCanvas().style.cursor = '';
	if (map.getLayer('clusters')) {
		map.on('mouseenter', 'clusters', cursorPointer);
		map.on('mouseleave', 'clusters', cursorDefault);
	}
	if (map.getLayer('unclustered-point')) {
		map.on('mouseenter', 'unclustered-point', cursorPointer);
		map.on('mouseleave', 'unclustered-point', cursorDefault);
	}
}
const addImageSources = async () => {
	if (!map) return;
	if (!map.hasImage('marker-regular')){
		bitmapMarkerReg = await map.loadImage(await toBitmap(markerSVG(attr('data-color'), false, false)));
		map.addImage('marker-regular', bitmapMarkerReg.data);
	}
	if(attr('data-maptype') === 'single' && !map.hasImage('marker-featured')) {
		map.addImage('marker-featured', bitmapMarkerReg.data);
		return;
	}
	if (!map.hasImage('marker-featured')){
		let featuredColor = attr('data-featured-color') || attr('data-color');
		let featuredStar = attr('data-featured-star', true);
		if(attr('data-color') === featuredColor && !featuredStar){ // identical settings
			markerBitmapFeat = bitmapMarkerReg; 
		}
		markerBitmapFeat = markerBitmapFeat || await map.loadImage(await toBitmap(markerSVG(featuredColor, true, featuredStar)));
		map.addImage('marker-featured', markerBitmapFeat.data);
	}
}
const resetSkipLink = () => {
	if(!skipHandler || !skipLink) return;
	skipLink.removeEventListener('click', skipHandler);
	skipHandler = null;
}
const initSkipLinkListener = () => {
	if(!skipLink || !mapfigcaption) return;	
	resetSkipLink();
	skipHandler = (e) =>{
		e.preventDefault();
		mapfigcaption.setAttribute('tabIndex','0');
		mapfigcaption.scrollIntoView({
			behavior: (prefReducedMotion ? 'instant' : 'smooth'),
			block: 'start',
		})
		mapfigcaption.focus({preventScroll: true});
		mapfigcaption.removeAttribute('tabIndex');
	}
	skipLink.addEventListener('click', skipHandler);
}
const CuratescapeMapInit = () => {
	if (!mapcanvas) {
		console.error('Map canvas element not found.');
		return;
	}
	try {
		map = new maplibregl.Map({
			container: mapcanvas,
			center: [attr('data-lon'), attr('data-lat')],
			zoom: attr('data-zoom'),
			bearing: 0,
			scrollZoom: (attr('data-maptype') !== 'multi'),
			attributionControl: { compact: true },
			interactive: false,
		}).once("mousedown", () => {
			map.scrollZoom.enable();
		}).once('idle', () => {
			initSkipLinkListener();
			if(typeof attr('data-tour') == 'string'){
				initMarkerRequestListener();
			}
		}).once('styledata', () => {
			setMarkers(dataSource(), true, true);
			addControls();
			mapfigure.setAttribute('data-loaded', 'true');
		});
		setStyleLayers();
	} catch (error) {
		console.error('Failed to initialize map:', error);
	}
}
// INITIALIZE MAP
document.addEventListener('DOMContentLoaded', () => {
	customElements.define('curatescape-map', CuratescapeMap);
	CuratescapeMapInit();
});