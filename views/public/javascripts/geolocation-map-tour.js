// (not in use)
// See Curatescape_View_Helper_HookPublicHead 
// See Curatescape_View_Helper_CuratescapeMap::GeolocationShortcode
document.addEventListener('DOMContentLoaded', function() { // deferred
	// ADD QUERY PARAMETERS TO TOUR ITEM LINKS INSIDE THE GEOLOCATION MAP
	if(typeof geolocationShortcode1OmekaMapBrowse !== 'undefined'){
		let mapfigure = document.querySelector('#curatescape-map-figure');
		if(!mapfigure) return;
		let data = mapfigure.dataset;
		if(!data) return;
		let tourItemsRange = data.range ? data.range.split(",") : [];
		if(!tourItemsRange.length) return;
		let tourId = new URL(window.location.href).pathname.split('/').pop();
		document.addEventListener('click', function(e) {
			let target = e.target.closest('.geolocation_balloon a');
			if(target && target.href){
				e.preventDefault();
				let url = new URL(target.href);
				let params = url.searchParams;
				let itemId = url.pathname.split('/').pop();
				params.append('tour', tourId);
				params.append('index', tourItemsRange.indexOf(itemId).toString());
				window.location.assign(url.toString());
			}
		})
	}
})