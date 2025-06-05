document.addEventListener('DOMContentLoaded', function() {
	const lightgallery = document.querySelector('.lightgallery');
	if(lightgallery){
		lightgallery.addEventListener('lgAfterOpen', () => {
			let fullscreenbutton = document.querySelector('.lg-maximize');
			if(fullscreenbutton){
				document.addEventListener('keydown', (e, isEscape = false) => {
					if ("key" in e) {
						if (e.key === "Escape" || e.key === "Esc") {
							if(fullscreenbutton.closest('.lg-inline') == null){
								fullscreenbutton.click(); // close fullscreen on ESC
								// @todo: is there a more direct way to do this?
							}
						}
					}
				});
			}
		})
	}
});