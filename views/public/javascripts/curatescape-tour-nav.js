class CuratescapeTourNav extends HTMLElement {
	constructor(){
		super();
		this.shadow = this.attachShadow({ mode: "open" });
	}
	connectedCallback(){
		this.uiElements();
		this.styleSheet();
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
		setTimeout(()=>{ // enables animation
			this.container.setAttribute('data-loaded','true');
		}, 0);
	}
	attr(string){
		return this.hasAttribute(string) ? this.getAttribute(string) : null;
	}
	tourDetail(id, tourid, title, tourinfo){
		if(!id || !tourid || !title || !tourinfo){
			return this.spacer();
		}
		let infoSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path d="M256 56C145.72 56 56 145.72 56 256s89.72 200 200 200 200-89.72 200-200S366.28 56 256 56zm0 82a26 26 0 11-26 26 26 26 0 0126-26zm64 226H200v-32h44v-88h-32v-32h64v120h44z"/></svg>'; // @todo: customizable using file URL
		let link = document.createElement('a');
		link.href = '/tours/show/'+tourid;
		link.title = title;
		link.innerHTML = infoSvg + '<span>' + tourinfo + '</span>';
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
			return this.spacer();
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
	spacer(titleattr){
		let spacer = document.createElement('div');
		spacer.classList.add('spacer');
		if(titleattr){
			spacer.title = titleattr;
		}
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
			--tour-nav-spacer-background-color: rgba(256,256,256, 0.25);
			--tour-nav-spacer-border: 1px solid transparent;
			
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
			margin: 0 auto calc(0 - var(--tour-nav-height) * 2);
			padding: var(--tour-nav-padding);
			font-family: inherit;
			box-shadow: var(--tour-nav-box-shadow);
			transition: all 0.5s linear;
			opacity: 0;
		}
		@media (prefers-reduced-motion: reduce) {
			#container{
				transition: unset;
			}
		}
		#container[data-loaded]{
			opacity: 1;
			margin: 0 auto calc(env(safe-area-inset-bottom) + 10px);
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
			cursor: not-allowed;
			background-color: var(--tour-nav-spacer-background-color);
			height: var(--tour-nav-height);
			width: var(--tour-nav-img-width);
			border: var(--tour-nav-spacer-border);
		}
		#container .spacer:last-child{
			border-radius: 0 var(--tour-nav-img-border-radius) var(--tour-nav-img-border-radius) 0;
		}
		#container .spacer:first-child{
			border-radius: var(--tour-nav-img-border-radius) 0  0 var(--tour-nav-img-border-radius);
		}
		#detail a{
			padding: var(--tour-nav-padding);
			display: flex;
			flex-direction: row;
			align-items: center;
		}
		svg{
			height: 1.5em;
			fill: var(--tour-nav-text-color);
			margin-right: 3px;
			opacity: 0.5;
		}
		`);
		this.shadow.adoptedStyleSheets = [css];
	}
}

document.addEventListener('DOMContentLoaded', function() { 
	customElements.define('curatescape-tour-nav', CuratescapeTourNav);
});
