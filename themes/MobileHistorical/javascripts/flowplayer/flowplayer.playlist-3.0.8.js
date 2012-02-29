/**
 * flowplayer.playlist 3.0.8. Flowplayer JavaScript plugin.
 * 
 * This file is part of Flowplayer, http://flowplayer.org
 *
 * Author: Tero Piirainen, <info@flowplayer.org>
 * Copyright (c) 2008-2010 Flowplayer Ltd
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * SEE: http://www.opensource.org/licenses
 * 
 * Date: 2010-05-04 05:33:23 +0000 (Tue, 04 May 2010)
 * Revision: 3405 
 */ 
(function($) {
	
	$f.addPlugin("playlist", function(wrap, options) {


		// self points to current Player instance
		var self = this;	
		
		var opts = {
			playingClass: 'playing',
			pausedClass: 'paused',
			progressClass:'progress',
			template: '<a href="${url}">${title}</a>',
			loop: false,
			playOnClick: true,
			manual: false
		};		
		
		$.extend(opts, options);
		wrap = $(wrap);		
		var manual = self.getPlaylist().length < 1 || opts.manual; 
		var els = null;
		
		
//{{{ "private" functions
				
		function toString(clip) {
			var el = template;
			
			$.each(clip, function(key, val) {	
				if (!$.isFunction(val)) {
					el = el.replace("$\{" +key+ "\}", val).replace("$%7B" +key+ "%7D", val);			
				}
			}); 
			return el;
		}
		
		// assign onClick event for each clip
		function bindClicks() {			
			els = getEls().unbind("click.playlist").bind("click.playlist", function() {
				return play($(this), els.index(this));						
			});		
		}
		
		function buildPlaylist() {
			wrap.empty();
			
			$.each(self.getPlaylist(), function() {  
				wrap.append(toString(this)); 
			});				
			
			bindClicks();
		} 

		
		function play(el, clip)  {
		
			if (el.hasClass(opts.playingClass) || el.hasClass(opts.pausedClass)) {
				self.toggle();
				
			} else {
				el.addClass(opts.progressClass);
				self.play(clip); 							
			}			
			
			return false;
		}	
		
		
		function clearCSS() {
			if (manual) { els = getEls(); }
			els.removeClass(opts.playingClass);
			els.removeClass(opts.pausedClass);
			els.removeClass(opts.progressClass);			
		}
		
		function getEl(clip) {
			return (manual) ? els.filter("[href=" + clip.originalUrl + "]") : els.eq(clip.index);	
		}
		
		function getEls() {
			var els = wrap.find("a");
			return els.length ? els : wrap.children();
		}
//}}}  
		 
		/* setup playlists with onClick handlers */ 
		
		// internal playlist
		if (!manual) {
			
			var template = wrap.is(":empty") ? opts.template : wrap.html(); 
			buildPlaylist();			
			
			
		// manual playlist
		} else {
			
			els = getEls();			
			
			// allows dynamic addition of elements
			if ($.isFunction(els.live)) {
				var foo = $(wrap.selector + " a");
				if (!foo.length) { foo = $(wrap.selector + " > *"); }
				
				foo.live("click", function() {
					var el = $(this);
					return play(el, el.attr("href"));
				});
				
			} else {
				els.click(function() {
					var el = $(this);
					return play(el, el.attr("href"));
				});					
			}
						 
					
			// setup player to play first clip
			var clip = self.getClip(0);
			if (!clip.url && opts.playOnClick) {
				clip.update({url: els.eq(0).attr("href")});		
			}   
			
		}
		
		// onBegin
		self.onBegin(function(clip) {
			clearCSS();		
			getEl(clip).addClass(opts.playingClass);
		});	
		
		// onPause	
		self.onPause(function(clip) {
			getEl(clip).removeClass(opts.playingClass).addClass(opts.pausedClass);		
		});	
		
		// onResume
		self.onResume(function(clip) {
			getEl(clip).removeClass(opts.pausedClass).addClass(opts.playingClass);		
		});		
		
		// what happens when clip ends ?
		if (!opts.loop && !manual) {
			
			// stop the playback exept on the last clip, which is stopped by default
			self.onBeforeFinish(function(clip) {
				if (!clip.isInStream && clip.index < els.length -1) {
					return false;
				}
			}); 
		}
		
		// on manual setups perform looping here
		if (manual && opts.loop) {
			self.onBeforeFinish(function(clip) {
				var el = getEl(clip);
				if (el.next().length) {
					el.next().click();	 		
				} else {
					els.eq(0).click();	
				} 
				return false;				
			}); 
		}
		
		// onUnload
		self.onUnload(function() {
			clearCSS();		
		});
		
		// onPlaylistReplace
		if (!manual) {
			self.onPlaylistReplace(function() {
				buildPlaylist();		
			});
		}
		
		// onClipAdd
		self.onClipAdd(function(clip, index) {	
			els.eq(index).before(toString(clip));			
			bindClicks(); 
		});		
		
		return self;
		
	});
		
})(jQuery);		
