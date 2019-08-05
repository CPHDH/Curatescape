var theme_name='curatescape';
jQuery(document).ready(function($) {
	console.log('MMenu ready...');
	loadJS('//'+location.host+'/themes/'+theme_name+'/javascripts/jquery.mmenu/jquery.mmenu.all.js',function(){
		// ============================ 
		// MMENU	
		var navbars = [
			{
				// search bar
				position: "top",
				content: ["<span class='offscreen-search'>"+$('form#sidebar-search-form')[0].outerHTML+"</span>"],
				height:2,
			},{
				// random button
		        position: "bottom",
		        content: ["<span class='offscreen-random'><a href='"+$('.random-story-link').attr('href')+"'><i class='fa fa-random fa-lg' aria-hidden='true'></i> View A Random Story<a></span>"],
		        height:1,
			}
	    ];		
		$('form#sidebar-search-form').remove();
		
		var extensions = [
				"pagedim-black",
				"fx-menu-fade",
			    "shadow-page",
			    "border-full"
			];
		
		var social= new Array();
			if(typeof $('a.social.email').attr('href') !== 'undefined'){
				social.push('<a class="fa fa-envelope" href="'+$('a.social.email').attr('href')+'"></a>');
			}
			if(typeof $('a.social.facebook').attr('href') !== 'undefined'){
				social.push('<a class="fa fa-facebook" target="_blank" rel="noopener" href="'+$('a.social.facebook').attr('href')+'"></a>');
			}
			if(typeof $('a.social.twitter').attr('href') !== 'undefined'){
				social.push('<a class="fa fa-twitter" target="_blank" rel="noopener" href="'+$('a.social.twitter').attr('href')+'"></a>');
			}
	
		var appstores= new Array();
			if(typeof $('a.appstore.ios').attr('href') !== 'undefined'){
				appstores.push('<a class="fa fa-apple sidebar-app-link" href="'+$('a.appstore.ios').attr('href')+'"> <span class="sidebar-app-title">App Store</span></a>');
			}
			if(typeof $('a.appstore.android').attr('href') !== 'undefined'){
				appstores.push('<a class="fa fa-android sidebar-app-link" href="'+$('a.appstore.android').attr('href')+'"> <span class="sidebar-app-title">Google Play</span></a>');
			}
	
		
		if($( window ).width() > '374'){
		    
		    // app stores
			if(appstores.length > 1){
			navbars.unshift({ 
		        position: "top",
		        content: appstores
				});	
			}    		
			// social media
		    if(social.length){
				navbars.push({ 
					position: "bottom",
					content: social		    
			    });
		    }	
		}	
		
		if($( window ).width() > '400'){
			// more spacious list for larger screens
			extensions.push("listview-huge"); 
			
					
		}
			
		$("#offscreen-menu").mmenu(
		{ 	// options
			slidingSubmenus: false,
			extensions:extensions, // viewport-dependent
			offCanvas: {
				position: "right"
			},		
			keyboardNavigation: {
			    enable:true,
			    enhance:true,
			},
			navbar: {
				title: null
			},        
			navbars: navbars // viewport-dependent
		}, 
		{ 	// config
			offCanvas: {
				pageSelector: "#page-content",
			}	
		});			
	});	

	// ============================ 
	// SMART PUNCTUATION
	function curlies(element) {
	    function smarten(text) {
	        return text
	            /* opening singles */
	            .replace(/(^|[-\u2014\s(\["])'/g, '$1\u2018')
	
	            /* closing singles & apostrophes */
	            .replace(/'/g, '\u2019')
	
	            /* opening doubles */
	            .replace(/(^|[-\u2014/\[(\u2018\s])"/g, '$1\u201c')
	
	            /* closing doubles */
	            .replace(/"/g, '\u201d')
	    };
	    var children = element.children;
	    if (children.length) {
	        for(var i = 0, l = children.length; i < l; i++) {
	            curlies(children[i]);
	        }
	    } else {
	        element.innerHTML = smarten(element.innerHTML);
	    }
	};	
	curlies( document.body );	
	
});	



