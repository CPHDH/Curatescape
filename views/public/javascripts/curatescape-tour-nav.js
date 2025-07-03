/* ===
CuratescapeTourNav
Author: Erin Bell
Copyright: 2025 Curatescape
License: GPL
=== */
class CuratescapeTourNav extends HTMLElement {
	constructor(){
		super();
		this.shadow = this.attachShadow({ mode: "open" });
	}
	connectedCallback(){
		this.getAttributes();
		this.uiElements();
		this.styleSheet();
	}
	disconnectedCallback(){

	}
	getAttributes(){
		this.tourNavContainerLabel = this.hasAttribute("tour-nav-container-label") ? this.getAttribute("tour-nav-container-label") : null;
		this.tourId = this.hasAttribute("tour-id") ? this.getAttribute("tour-id") : null;
		this.tourTitle = this.hasAttribute("tour-title") ? this.getAttribute("tour-title") : null;
		this.previousIndex = this.hasAttribute("previous-index") ? this.getAttribute("previous-index") : null;
		this.previousLinkTitle = this.hasAttribute("previous-link-title") ? this.getAttribute("previous-link-title") : null;
		this.previousItemThumb = this.hasAttribute("previous-item-thumb") ? this.getAttribute("previous-item-thumb") : null;
		this.previousItemUrl = this.hasAttribute("previous-item-url") ? this.getAttribute("previous-item-url") : null;
		this.previousItemTitle = this.hasAttribute("previous-item-title") ? this.getAttribute("previous-item-title") : null;
		this.nextIndex = this.hasAttribute("next-index") ? this.getAttribute("next-index") : null;
		this.nextLinkTitle = this.hasAttribute("next-link-title") ? this.getAttribute("next-link-title") : null;
		this.nextItemThumb = this.hasAttribute("next-item-thumb") ? this.getAttribute("next-item-thumb") : null;
		this.nextItemUrl = this.hasAttribute("next-item-url") ? this.getAttribute("next-item-url") : null;
		this.nextItemTitle = this.hasAttribute("next-item-title") ? this.getAttribute("next-item-title") : null;
	}

	uiElements(){
		this.container = document.createElement("div");
		this.container.id = 'tour-nav-inner';
		this.container.setAttribute('role', 'region');
		this.container.setAttribute('aria-label', this.tourNavContainerLabel);
		this.shadow.appendChild(this.container);
	}

	styleSheet(){
		const css = new CSSStyleSheet();
		css.replaceSync(`
		:host{
			--background-color;
			--text-color;
			
			container-type: inline-size;
			container-name: nav;
			width:100%;
			display:block;
			position:fixed;
			bottom:0;
			left:0;
			right:0;
		}
		#tour-nav-inner{
			display: flex;
			flex-direction: row;
			align-items: center;
			width: 100%;
			background-color:var(--background-color, crimson);
			color:var(--color, white);
			min-height: 50px;
		}
		`);
		this.shadow.adoptedStyleSheets = [css];
	}
}
// register component
customElements.define('curatescape-tour-nav', CuratescapeTourNav);