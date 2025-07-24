// See also CuratescapeTour methods: tourGeolocationMap & tourItemCaption
document.addEventListener('DOMContentLoaded', function() {
	// let mapfigure = document.querySelector('.tour-items-map');
	// let tourId = mapfigure.dataset.tourId;
	// let tourItems =mapfigure.dataset.tourItems.split(",");
	// let map = geolocationShortcode1OmekaMapBrowse.map;
	// let clustered = geolocationShortcode1OmekaMapBrowse.options.cluster;
	// let markers = geolocationShortcode1OmekaMapBrowse.markers;
	// // ADD QUERY PARAMETERS TO TOUR ITEM LINKS FROM THE GEOLOCATION MAP
	// document.addEventListener('click', function(e) {
	// 	let target = e.target.closest('.geolocation_balloon a');
	// 	if(target && target.href){
	// 		e.preventDefault();
	// 		let url = new URL(target.href);
	// 		let params = url.searchParams;
	// 		let itemId = url.pathname.split('/').pop();
	// 		params.append('tour', tourId);
	// 		params.append('index', tourItems.indexOf(itemId).toString());
	// 		window.location.assign(url.toString());
	// 	}
	// })
	// // OPEN MARKER BUTTONS
	// const openMarker = (index) => {
	// 	mapfigure.scrollIntoView({ behavior: "smooth", block: "center" });
	// 	markers[index].fire('click');
	// }
	// map.once('moveend', ()=>{ // a proxy for marker availability 
	// 	markers = markers.reverse(); // this should match the list order
	// 	markers.forEach(marker=>{ // account for clustered markers
	// 		marker.on('click', function(e){
	// 			if(clustered){
	// 				map.eachLayer((layer) => {
	// 					if(typeof layer.zoomToShowLayer === 'function'){
	// 						layer.zoomToShowLayer(marker, ()=>{
	// 							marker.openPopup();
	// 						});
	// 					}
	// 				});
	// 			}
	// 		});
	// 	})
	// 	showOnMapButtons = document.querySelectorAll('[data-item-id]'); // only on items having map location
	// 	showOnMapButtons.forEach((button,i)=>{
	// 		button.addEventListener("click", (e)=>{
	// 			openMarker(i)
	// 		})
	// 	})
	// });	
})