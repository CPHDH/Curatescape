document.addEventListener('DOMContentLoaded', function() { // deferred
	// TRIGGER CURATESCAPE-MAP MARKER EVENT
	let markerButtons = document.querySelectorAll('[data-item-id]') || [];
	markerButtons.forEach((marker)=>{
		marker.addEventListener('click',(e)=>{
			e.preventDefault;
			let id = marker.getAttribute('data-item-id');
			let markerRequest = new CustomEvent("markerRequest", { "detail": id });
			document.dispatchEvent(markerRequest);
		})
	})
	// INTERCEPT TOUR ITEM LINKS FROM THE GEOLOCATION MAP (and add query params)
	if(typeof geolocationShortcode1OmekaMapBrowse !== 'undefined'){
		let mapfigure = document.querySelector('#curatescape-map-figure');
		if(!mapfigure) return;
		let data = mapfigure.dataset;
		if(!data) return;
		let tourItemsRange = data.range ? data.range.split(",") : [];
		if(!tourItemsRange.length) return;
		let tourId = new URL(window.location.href).pathname.split('/').pop();
		mapfigure.addEventListener('click', function(e) {
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