import PhotoSwipeLightbox from './PhotoSwipe/dist/photoswipe.esm.min.js';
const getVideoDimensions = (url) => {
	// @todo: revise and run on page load to modify html before this file loads 
	return new Promise((resolve, reject) => {
		reject(e.message)
		let video = document.createElement('video');
		video.setAttribute('src', url);
		video.addEventListener('loadedmetadata', () => {
			resolve( { width: video.videoWidth, height: video.videoHeight } );
		});
		video.addEventListener('error', (e) => {
			reject(e.message);
		});
	});
}
const initLightbox = ()=>{
	let data = [];
	document.querySelectorAll('#pswp-container .pswp-item').forEach((item,i) => {
		data.push({
		  src: item.dataset.pswpSrc,
		  h: item.dataset.pswpHeight,
		  w: item.dataset.pswpWidth,
		  type: item.dataset.pswpType,
		  fallbackmessage: item.dataset.pswpFallbackmessage,
		  caption: item.nextElementSibling.innerHTML,
		});
		// click item to open lightbox
		item.onclick = (e) => {
			e.preventDefault();
			openLightboxAtIndex(i, data);
		};
	});
}
const openLightboxAtIndex = (i,data)=>{
	let lightbox = new PhotoSwipeLightbox({
		dataSource: data,
		index: i,
		bgOpacity: 1,
		pswpModule: () => import('./PhotoSwipe/dist/photoswipe.esm.min.js')
	});
	// add captions support
	lightbox.on('uiRegister', ()=>{
		lightbox.ui.registerElement({
			name: 'pswp-caption',
			order: 9,
			isButton: false,
			appendTo: 'root',
			onInit: (el, lightbox) => {
				lightbox.on('change', () => {
					el.innerHTML = '<div style="'+getCalculatedLinkColor()+'">' + data[lightbox.currSlide.index].caption + '</div>' || '';
				});
			}
	  });
	});
	// add a/v and doc support
	lightbox.on('contentLoad', (e) => {
		const { content } = e;
		const browserClassName = browserString();
		if (content.type === 'audio') {
			e.preventDefault();
			content.element = document.createElement('div');
			content.element.className = 'pswp__audio-container';
			const audio = document.createElement('audio');
			audio.setAttribute('controls','controls');
			audio.setAttribute('src', content.data.src);
			audio.classList.add(browserClassName);
			content.element.appendChild(audio);
		}
		if (content.type === 'video') {
			e.preventDefault();
			content.element = document.createElement('div');
			content.element.className = 'pswp__video-container';
			const video = document.createElement('video');
			video.setAttribute('controls','controls');
			video.setAttribute('src', content.data.src);
			video.classList.add(browserClassName);
			content.element.appendChild(video);
		}
		if (content.type === 'document') {
			e.preventDefault();
			content.element = document.createElement('div');
			content.element.className = 'pswp__document-container';
			const download = document.createElement('a');
			download.setAttribute('href', content.data.src);
			download.setAttribute('download', content.data.src.match(/[^/]+$/)[0]);
			download.classList.add('button');
			download.classList.add('curatescape-pswp-button');
			download.innerText = content.data.fallbackmessage;
			if(browserClassName=='chromium') {
				// Chromium PDF Viewer (Chrome, Edge, Opera, etc.)
				// must use iframe to avoid "permissions policy violation: fullscreen..."
				const iframe = document.createElement('iframe');
				iframe.setAttribute('allow', 'fullscreen *');
				iframe.setAttribute('src', content.data.src);
				iframe.setAttribute('height', content.data.h);
				iframe.setAttribute('width', content.data.w);
				iframe.classList.add(browserClassName);
				iframe.innerHTML = download;
				content.element.appendChild(iframe);
			} else if(browserClassName=='firefox'){
				// Firefox PDF Viewer
				// must use object to avoid automatically downloading file
				const object = document.createElement('object');
				object.setAttribute('data', content.data.src);
				object.setAttribute('height', content.data.h);
				object.setAttribute('width', content.data.w);
				object.classList.add(browserClassName);
				object.innerHTML = download;
				content.element.appendChild(object);
			} else {
				const div = document.createElement('div');
				div.classList.add('document-thumb');
				const img = document.createElement('img');
				img.setAttribute('src', content.data.src.replace('original','fullsize').replace('.pdf','.jpg'));
				img.setAttribute('height', '500');
				img.setAttribute('width', '500');
				div.classList.add(browserClassName);
				div.appendChild(img);
				div.appendChild(download);
				content.element.appendChild(div);
			}
		}
	});
	// close
	lightbox.on('close', () => {
	  lightbox.destroy();
	});
	// init on item
	lightbox.init();
}
// INIT
initLightbox();