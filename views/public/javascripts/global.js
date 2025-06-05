// HELPERS
const wrap = (el, wrapper) =>{
	// wrap element in tag
	if (el && el.parentNode) {
		el.parentNode.insertBefore(wrapper, el);
		wrapper.appendChild(el);
	}
}
const getCalculatedLinkColor = (opacity = 1)=>{
	// try to derive lighter color from theme link styles
	let sampleElement = document.querySelector('a');
	let colorSample = sampleElement ? window.getComputedStyle(sampleElement).color : false;
	let colorLightened = colorSample ? 'hsl(from '+colorSample+' h s 70% / ' + opacity + ')' : false;
	return colorLightened ? '--pswp-accent-color:'+colorLightened : '';
}
const browserString = ()=>{
	// used only for PDF display
	let ua = navigator.userAgent;
	if(/Chrome/.test(ua)){
		return 'chromium';
	}
	if(/Firefox/.test(ua)){
		return 'firefox';
	}
	if(/iPad|iPhone|iPod/.test(ua)){
		return 'ios-webkit';
	}
	if(/Safari/.test(ua)){
		return 'macos-webkit';
	}
	return 'other';
}
