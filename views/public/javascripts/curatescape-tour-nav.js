class CuratescapeTourNav extends HTMLElement {
	constructor(){
		super();
		this.shadow = this.attachShadow({ mode: "open" });
	}
	connectedCallback(){
		//this.getAttributes();
		this.uiElements();
		this.styleSheet();
	}
	disconnectedCallback(){

	}
	uiElements(){
		this.container = document.createElement("nav");
		this.container.id = 'container';
		this.container.setAttribute('role', 'region');
		this.container.setAttribute('aria-label', this.attr('tour-nav-container-label'));
		// previous tour item (start)
		this.container.appendChild(
			this.tourItemLink(
				'previous',
				this.attr('tour-id'),
				this.attr('previous-index'),
				this.attr('previous-link-title'),
				this.attr('previous-item-url'),
				this.attr('previous-item-title'),
				this.attr('previous-item-thumb'),
			)
		);
		// tour info (center)
		this.container.appendChild(
			this.tourDetail(
				'detail',
				this.attr('tour-id'),
				this.attr('tour-title'),
				this.attr('tour-nav-info-label'),
			)
		);
		// next tour item (end)
		this.container.appendChild(
			this.tourItemLink(
				'next',
				this.attr('tour-id'),
				this.attr('next-index'),
				this.attr('next-link-title'),
				this.attr('next-item-url'),
				this.attr('next-item-title'),
				this.attr('next-item-thumb'),
			)
		);
		this.shadow.appendChild(this.container);
	}
	attr(string){
		return this.hasAttribute(string) ? this.getAttribute(string) : null;
	}
	tourDetail(id, tourid, title, tourinfo){
		if(!id || !tourid || !title || !tourinfo){
			return this.spacer(id, tourid, title, tourinfo);
		}
		let link = document.createElement('a');
		link.href = '/tours/show/'+tourid;
		link.title = title;
		link.textContent = tourinfo;
		let tourDetail = document.createElement("div");
		tourDetail.id = id;
		tourDetail.appendChild(link)
		return tourDetail;
	}
	orientationClass(img){
		img.onload = function() {
		  return this.naturalHeight > this.naturalWidth ? 'portrait' : 'landscape';
		}
		return 'default';
	}
	tourItemLink(id, tourid, index, titleattr, url, text, imgsrc){
		if(!id || !tourid || !index || !titleattr || !url || !text){
			return this.spacer(id, tourid, index, titleattr, url, text, imgsrc);
		}
		let img = document.createElement('img');
		img.onload = function() {
			if(this.naturalHeight > this.naturalWidth){
				img.classList.add('portrait')
			}else{
				img.classList.add('default');
			}
		}
		img.loading = 'lazy';
		img.src = imgsrc;
		img.alt = text;
		let link = document.createElement('a');
		link.id = id;
		link.title = titleattr;
		link.href = url + '?tour='+tourid + '&index=' + index;
		link.appendChild(img);
		return link;
	}
	spacer(logmessage){
		if(logmessage) console.log(logmessage)
		let spacer = document.createElement('div');
		spacer.classList.add('spacer');
		return spacer;
	}
	styleSheet(){
		const css = new CSSStyleSheet();
		css.replaceSync(`
		:host{
			--tour-nav-box-shadow: 0 0 10px rgba(0,0,0,.15);
			--tour-nav-text-color: #fff;
			--tour-nav-button-color: var(--tour-nav-text-color);
			--tour-nav-background-color: rgba(0,0,0,.925);
			--tour-nav-img-background: rgba(256,256,256,.15);
			--tour-nav-img-border-width: 1px;
			--tour-nav-img-border: var(--tour-nav-img-border-width) solid var(--tour-nav-img-background);
			--tour-nav-img-border-radius: calc(var(--tour-nav-border-radius) - var(--tour-nav-padding) - var(--tour-nav-img-border-width));
			--tour-nav-img-width: calc(var(--tour-nav-height) * var(--tour-nav-image-width-multiplier));
			--tour-nav-padding: 5px;
			--tour-nav-height: 60px;
			--tour-nav-max-width: 370px;
			--tour-nav-border-radius: calc( var(--tour-nav-height) * 0.5);
			--tour-nav-image-width-multiplier: 1.5;
			
			container-type: inline-size;
			container-name: nav;
			width: 100%;
			display: block;
			position: fixed;
			z-index: 999;
			bottom: 0;
			left: 0;
			right: 0;
		}
		#container{
			background-color: var(--tour-nav-background-color);
			color: var(--tour-nav-text-color);
			min-height: var(--tour-nav-height);
			max-width: var(--tour-nav-max-width);
			border-radius: var(--tour-nav-border-radius);
			display: flex;
			flex-direction: row;
			align-items: center;
			text-align: center;
			justify-content: space-between;
			width: 95%;
			margin: 0 auto calc(env(safe-area-inset-bottom) + 10px);
			padding: var(--tour-nav-padding);
			font-family: inherit;
			box-shadow: var(--tour-nav-box-shadow);
		}
		a{
			color: var(--tour-nav-button-color);
			text-decoration: none;
			display: flex;
			overflow: hidden;
		}
		a img{
			object-fit: cover;
			object-position: center;
			color: transparent;
			font-size: 0;
			overflow: hidden;
			background-color: var(--tour-nav-img-background);
			height: var(--tour-nav-height);
			width: var(--tour-nav-img-width);
			border: var(--tour-nav-img-border);
			border-radius: 0 var(--tour-nav-img-border-radius) var(--tour-nav-img-border-radius) 0;
		}
		#container a:first-child img{
			border-radius: var(--tour-nav-img-border-radius) 0  0 var(--tour-nav-img-border-radius);
		}
		a img.portrait{
			object-position: center 20%;
		}
		#container .spacer{
			min-width: var(--tour-nav-img-width);
		}
		#detail a{
			padding: var(--tour-nav-padding);
		}
		`);
		this.shadow.adoptedStyleSheets = [css];
	}
}

document.addEventListener('DOMContentLoaded', function() { 
	// register and initialize component
	customElements.define('curatescape-tour-nav', CuratescapeTourNav);
});
