import Mmenu from './../oncanvas/mmenu.oncanvas';
import options from './_options';
import * as DOM from '../_dom';
import * as support from '../../core/_support';
import { extendShorthandOptions } from './_options';
import { extend, touchDirection } from '../../core/_helpers';
//  Add the options.
Mmenu.options.scrollBugFix = options;
export default function () {
    //	The scrollBugFix add-on fixes a scrolling bug
    //		1) on touch devices
    //		2) in an off-canvas menu
    //		3) that -when opened- blocks the UI from interaction
    if (!support.touch || // 1
        !this.opts.offCanvas || // 2
        !this.opts.offCanvas.blockUI // 3
    ) {
        return;
    }
    //	Extend options.
    var options = extendShorthandOptions(this.opts.scrollBugFix);
    this.opts.scrollBugFix = extend(options, Mmenu.options.scrollBugFix);
    if (!options.fix) {
        return;
    }
    var touchDir = touchDirection(this.node.menu);
    /**
     * Prevent an event from doing its default and stop its propagation.
     * @param {ScrollBehavior} evnt The event to stop.
     */
    function stop(evnt) {
        evnt.preventDefault();
        evnt.stopPropagation();
    }
    //  Prevent the page from scrolling when scrolling in the menu.
    this.node.menu.addEventListener('scroll', stop, {
        //  Make sure to tell the browser the event will be prevented.
        passive: false
    });
    //  Prevent the page from scrolling when dragging in the menu.
    this.node.menu.addEventListener('touchmove', evnt => {
        var panel = evnt.target.closest('.mm-panel');
        if (panel) {
            //  When dragging a non-scrollable panel,
            //      we can simple preventDefault and stopPropagation.
            if (panel.scrollHeight === panel.offsetHeight) {
                stop(evnt);
                //  When dragging a scrollable panel,
                //      that is fully scrolled up (or down).
                //      It will not trigger the scroll event when dragging down (or up) (because you can't scroll up (or down)),
                //      so we need to match the dragging direction with the scroll position before preventDefault and stopPropagation,
                //      otherwise the panel would not scroll at all in any direction.
            }
            else if (
            //  When scrolled up and dragging down
            (panel.scrollTop == 0 && touchDir.get() == 'down') ||
                //  When scrolled down and dragging up
                (panel.scrollHeight ==
                    panel.scrollTop + panel.offsetHeight &&
                    touchDir.get() == 'up')) {
                stop(evnt);
            }
            //  When dragging anything other than a panel.
        }
        else {
            stop(evnt);
        }
    }, {
        //  Make sure to tell the browser the event can be prevented.
        passive: false
    });
    //  Some small additional improvements
    //	Scroll the current opened panel to the top when opening the menu.
    this.bind('open:start', () => {
        var panel = DOM.children(this.node.pnls, '.mm-panel_opened')[0];
        panel.scrollTop = 0;
    });
    //	Fix issue after device rotation change.
    window.addEventListener('orientationchange', evnt => {
        var panel = DOM.children(this.node.pnls, '.mm-panel_opened')[0];
        panel.scrollTop = 0;
        //	Apparently, changing the overflow-scrolling property triggers some event :)
        panel.style['-webkit-overflow-scrolling'] = 'auto';
        panel.style['-webkit-overflow-scrolling'] = 'touch';
    });
}
