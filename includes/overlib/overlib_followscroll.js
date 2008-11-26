//\/////
//\  overLIB Follow Scroll Plugin
//\  This file requires overLIB 4.00 or later.
//\
//\  overLIB 4.05 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2004. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//   $Revision: 1.1 $                      $Date: 2004/09/15 05:00:18 $
//\/////


////////
// PRE-INIT
// Ignore these lines, configuration is below.
////////
if (typeof olInfo == 'undefined' || olInfo.simpleversion < 400) alert('overLIB 4.00 or later is required for the Follow Scroll Plugin.');
registerCommands('followscroll,followscrollrefresh');


////////
// DEFAULT CONFIGURATION
// You don't have to change anything here if you don't want to. All of this can be
// changed on your html page or through an overLIB call.
////////
// Default value for scroll is not to scroll (0)
if (typeof ol_followscroll=='undefined') var ol_followscroll=0;
if (typeof ol_followscrollrefresh=='undefined') var ol_followscrollrefresh=100;

////////
// END OF CONFIGURATION
// Don't change anything below this line, all configuration is above.
////////







////////
// INIT
////////
// Runtime variables init. Don't change for config!
var o3_followscroll=0;
var o3_followscrollrefresh=100;


////////
// PLUGIN FUNCTIONS
////////
function setScrollVariables() {
	o3_followscroll=ol_followscroll;
	o3_followscrollrefresh=ol_followscrollrefresh;
}

// Parses Shadow and Scroll commands
function parseScrollExtras(pf,i,ar) {
	var k=i,v;
	if (k < ar.length) {
		if (ar[k]==FOLLOWSCROLL) { eval(pf +'followscroll=('+pf+'followscroll==0) ? 1 : 0'); return k; }
		if (ar[k]==FOLLOWSCROLLREFRESH) { eval(pf+'followscrollrefresh='+ar[++k]); return k; }
	}
	return -1;
}



// Function to support scroll feature (overloads default)
function scroll_placeLayer() {
	var placeX, placeY, widthFix = 0;
	var hasAnchor=(typeof o3_anchor != 'undefined' && o3_anchor);
	
	// HORIZONTAL PLACEMENT
	if (eval('o3_frame.'+docRoot) && eval("typeof o3_frame."+docRoot+".clientWidth=='number'") && eval('o3_frame.'+docRoot+'.clientWidth')) {
		iwidth = eval('o3_frame.'+docRoot+'.clientWidth');
	} else if (typeof(o3_frame.innerWidth) == 'number') {
		widthFix = Math.ceil(1.2*(o3_frame.outerWidth - o3_frame.innerWidth));
		iwidth = o3_frame.innerWidth;
	}

	if (hasAnchor) {
		placeX = rmrkPosition[0];
		placeY = rmrkPosition[1];
	} else {	
		// Horizontal scroll offset
		winoffset=(olIe4) ? eval('o3_frame.'+docRoot+'.scrollLeft') : o3_frame.pageXOffset;
		var parsedWidth = parseInt(o3_width);
	
		if (o3_fixx > -1 || o3_relx != null) {
			// Fixed position
			placeX=(o3_relx != null ? ( o3_relx < 0 ? winoffset +o3_relx+ iwidth - parsedWidth - widthFix : winoffset+o3_relx) : o3_fixx);
		} else {  
			// If HAUTO, decide what to use.
			if (o3_hauto == 1) {
				if ((o3_x - winoffset) > (iwidth / 2)) {
					o3_hpos = LEFT;
				} else {
					o3_hpos = RIGHT;
				}
			}  		
	
			// From mouse
			if (o3_hpos == CENTER) { // Center
				placeX = o3_x+o3_offsetx-(parsedWidth/2);
	
				if (placeX < winoffset) placeX = winoffset;
			}
	
			if (o3_hpos == RIGHT) { // Right
				placeX = o3_x+o3_offsetx;
	
				if ((placeX+parsedWidth) > (winoffset+iwidth - widthFix)) {
					placeX = iwidth+winoffset - parsedWidth - widthFix;
					if (placeX < 0) placeX = 0;
				}
			}
			if (o3_hpos == LEFT) { // Left
				placeX = o3_x-o3_offsetx-parsedWidth;
				if (placeX < winoffset) placeX = winoffset;
			}  	
	
			// Snapping!
			if (o3_snapx > 1) {
				var snapping = placeX % o3_snapx;
	
				if (o3_hpos == LEFT) {
					placeX = placeX - (o3_snapx+snapping);
				} else {
					// CENTER and RIGHT
					placeX = placeX+(o3_snapx - snapping);
				}
	
				if (placeX < winoffset) placeX = winoffset;
			}
		}	
	
		// VERTICAL PLACEMENT
		if (eval('o3_frame.'+docRoot) && eval("typeof o3_frame."+docRoot+".clientHeight=='number'") && eval('o3_frame.'+docRoot+'.clientHeight')) {
			iheight = eval('o3_frame.'+docRoot+'.clientHeight');
		} else if (typeof(o3_frame.innerHeight)=='number') {
			iheight = o3_frame.innerHeight;
		}
	
		// Vertical scroll offset
		scrolloffset=(olIe4) ? eval('o3_frame.'+docRoot+'.scrollTop') : o3_frame.pageYOffset;
		var parsedHeight=(o3_aboveheight ? parseInt(o3_aboveheight) : (olNs4 ? over.clip.height : over.offsetHeight));
	
		if (o3_fixy > -1 || o3_rely != null) {
			// Fixed position
			placeY=(o3_rely != null ? (o3_rely < 0 ? scrolloffset+o3_rely+iheight - parsedHeight : scrolloffset+o3_rely) : o3_fixy);
		} else {
			// If VAUTO, decide what to use.
			if (o3_vauto == 1) {  
				if ((o3_y - scrolloffset) > (iheight/2)) {
					o3_vpos = ABOVE;
				} else {
					o3_vpos = BELOW;
				}
			}
	
			// From mouse
			if (o3_vpos == ABOVE) {
				if (o3_aboveheight == 0) o3_aboveheight = parsedHeight; 
	
				placeY = o3_y - (o3_aboveheight+o3_offsety);
				if (placeY < scrolloffset) placeY = scrolloffset;
			} else {
				// BELOW
				placeY = o3_y+o3_offsety;
			} 
	
			// Snapping!
			if (o3_snapy > 1) {
				var snapping = placeY % o3_snapy;  			
	
				if (o3_aboveheight > 0 && o3_vpos == ABOVE) {
					placeY = placeY - (o3_snapy+snapping);
				} else {
					placeY = placeY+(o3_snapy - snapping);
				} 			
	
				if (placeY < scrolloffset) placeY = scrolloffset;
			}
		}
	}

	// Actually move the object.
	repositionTo(over,placeX,placeY);
	
	if (o3_followscroll && o3_sticky && (o3_relx || o3_rely) && (typeof o3_draggable == 'undefined' || !o3_draggable)) {
		if (typeof over.scroller=='undefined' || over.scroller.canScroll) over.scroller = new Scroller(placeX-winoffset,placeY-scrolloffset,o3_followscrollrefresh);
	}
}



///////
// SUPPORT ROUTINES FOR SCROLL FEATURE
///////

// Scroller constructor
function Scroller(X,Y,refresh) {
	this.canScroll=0;
	this.refresh=refresh;
	this.x=X;
	this.y=Y;
	this.timer=setTimeout("repositionOver()",this.refresh);
}

// Removes the timer to stop replacing the layer.
function cancelScroll() {
	if (!o3_followscroll || typeof over.scroller == 'undefined') return;
	over.scroller.canScroll = 1;
	
	if (over.scroller.timer) {
		clearTimeout(over.scroller.timer);
		over.scroller.timer=null;
	}
}

// Find out how much we've scrolled.
	function getPageScrollY() {
	if (o3_frame.pageYOffset) return o3_frame.pageYOffset;
	if (eval(docRoot)) return eval('o3_frame.' + docRoot + '.scrollTop');
	return -1;
}
function getPageScrollX() {
	if (o3_frame.pageXOffset) return o3_frame.pageXOffset;
	if (eval(docRoot)) return eval('o3_frame.'+docRoot+'.scrollLeft');
	return -1;
}

// Find out where our layer is
function getLayerTop(layer) {
	if (layer.pageY) return layer.pageY;
	if (layer.style.top) return parseInt(layer.style.top);
	return -1;
}
function getLayerLeft(layer) {
	if (layer.pageX) return layer.pageX;
	if (layer.style.left) return parseInt(layer.style.left);
	return -1;
}

// Repositions the layer if needed
function repositionOver() {
	var X, Y, pgLeft, pgTop;
	pgTop = getPageScrollY();
	pgLeft = getPageScrollX();
	X = getLayerLeft(over)-pgLeft;
	Y = getLayerTop(over)-pgTop;
	
	if (X != over.scroller.x || Y != over.scroller.y) repositionTo(over, pgLeft+over.scroller.x, pgTop+over.scroller.y);
	over.scroller.timer = setTimeout("repositionOver()", over.scroller.refresh);
}

////////
// PLUGIN REGISTRATIONS
////////
registerRunTimeFunction(setScrollVariables);
registerCmdLineFunction(parseScrollExtras);
registerHook("hideObject",cancelScroll,FAFTER);
registerHook("placeLayer",scroll_placeLayer,FREPLACE);
//end 
