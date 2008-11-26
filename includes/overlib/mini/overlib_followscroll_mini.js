//\/////
//\  overLIB Follow Scroll Plugin
//\  This file requires overLIB 4.00 or later.
//\
//\  overLIB 4.05 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2004. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//\/////
if (typeof olInfo=='undefined'||olInfo.simpleversion<400) alert('overLIB 4.00 or later is required for the Follow Scroll Plugin.');registerCommands('followscroll,followscrollrefresh');
if (typeof ol_followscroll=='undefined') var ol_followscroll=0;if (typeof ol_followscrollrefresh=='undefined') var ol_followscrollrefresh=100;
var o3_followscroll=0;var o3_followscrollrefresh=100;
function setScrollVariables() {o3_followscroll=ol_followscroll;o3_followscrollrefresh=ol_followscrollrefresh;}
function parseScrollExtras(pf,i,ar) {var k=i,v;if (k<ar.length) {if (ar[k]==FOLLOWSCROLL) { eval(pf+'followscroll=('+pf+'followscroll==0)?1:0'); return k; }
if (ar[k]==FOLLOWSCROLLREFRESH) { eval(pf+'followscrollrefresh='+ar[++k]); return k; }
}
return-1;}
function scroll_placeLayer() {var placeX, placeY, widthFix=0;var hasAnchor=(typeof o3_anchor!='undefined'&&o3_anchor);
if (eval('o3_frame.'+docRoot)&&eval("typeof o3_frame."+docRoot+".clientWidth=='number'")&&eval('o3_frame.'+docRoot+'.clientWidth')) {iwidth=eval('o3_frame.'+docRoot+'.clientWidth');} else if (typeof(o3_frame.innerWidth)=='number') {widthFix=Math.ceil(1.2*(o3_frame.outerWidth-o3_frame.innerWidth));iwidth=o3_frame.innerWidth;}
if (hasAnchor) {placeX=rmrkPosition[0];placeY=rmrkPosition[1];} else {
winoffset=(olIe4)?eval('o3_frame.'+docRoot+'.scrollLeft'):o3_frame.pageXOffset;var parsedWidth=parseInt(o3_width);
if (o3_fixx>-1||o3_relx!=null) {
placeX=(o3_relx!=null?( o3_relx<0?winoffset+o3_relx+iwidth-parsedWidth-widthFix:winoffset+o3_relx):o3_fixx);} else {
if (o3_hauto==1) {if ((o3_x-winoffset)>(iwidth/2)) {o3_hpos=LEFT;} else {o3_hpos=RIGHT;}
}
if (o3_hpos==CENTER) {placeX=o3_x+o3_offsetx-(parsedWidth/2);
if (placeX<winoffset) placeX=winoffset;}
if (o3_hpos==RIGHT) {placeX=o3_x+o3_offsetx;
if ((placeX+parsedWidth)>(winoffset+iwidth-widthFix)) {placeX=iwidth+winoffset-parsedWidth-widthFix;if (placeX<0) placeX=0;}
}
if (o3_hpos==LEFT) {placeX=o3_x-o3_offsetx-parsedWidth;if (placeX<winoffset) placeX=winoffset;}
if (o3_snapx>1) {var snapping=placeX % o3_snapx;
if (o3_hpos==LEFT) {placeX=placeX-(o3_snapx+snapping);} else {
placeX=placeX+(o3_snapx-snapping);}
if (placeX<winoffset) placeX=winoffset;}
}
if (eval('o3_frame.'+docRoot)&&eval("typeof o3_frame."+docRoot+".clientHeight=='number'")&&eval('o3_frame.'+docRoot+'.clientHeight')) {iheight=eval('o3_frame.'+docRoot+'.clientHeight');} else if (typeof(o3_frame.innerHeight)=='number') {iheight=o3_frame.innerHeight;}
scrolloffset=(olIe4)?eval('o3_frame.'+docRoot+'.scrollTop'):o3_frame.pageYOffset;var parsedHeight=(o3_aboveheight?parseInt(o3_aboveheight):(olNs4?over.clip.height:over.offsetHeight));
if (o3_fixy>-1||o3_rely!=null) {
placeY=(o3_rely!=null?(o3_rely<0?scrolloffset+o3_rely+iheight-parsedHeight:scrolloffset+o3_rely):o3_fixy);} else {
if (o3_vauto==1) {if ((o3_y-scrolloffset)>(iheight/2)) {o3_vpos=ABOVE;} else {o3_vpos=BELOW;}
}
if (o3_vpos==ABOVE) {if (o3_aboveheight==0) o3_aboveheight=parsedHeight;
placeY=o3_y-(o3_aboveheight+o3_offsety);if (placeY<scrolloffset) placeY=scrolloffset;} else {
placeY=o3_y+o3_offsety;}
if (o3_snapy>1) {var snapping=placeY % o3_snapy;
if (o3_aboveheight>0&&o3_vpos==ABOVE) {placeY=placeY-(o3_snapy+snapping);} else {placeY=placeY+(o3_snapy-snapping);}
if (placeY<scrolloffset) placeY=scrolloffset;}
}
}
repositionTo(over,placeX,placeY);
if (o3_followscroll&&o3_sticky&&(o3_relx||o3_rely)&&(typeof o3_draggable=='undefined'||!o3_draggable)) {if (typeof over.scroller=='undefined'||over.scroller.canScroll) over.scroller=new Scroller(placeX-winoffset,placeY-scrolloffset,o3_followscrollrefresh);}
}
function Scroller(X,Y,refresh) {this.canScroll=0;this.refresh=refresh;this.x=X;this.y=Y;this.timer=setTimeout("repositionOver()",this.refresh);}
function cancelScroll() {if (!o3_followscroll||typeof over.scroller=='undefined') return;over.scroller.canScroll=1;
if (over.scroller.timer) {clearTimeout(over.scroller.timer);over.scroller.timer=null;}
}
function getPageScrollY() {if (o3_frame.pageYOffset) return o3_frame.pageYOffset;if (eval(docRoot)) return eval('o3_frame.'+docRoot+'.scrollTop');return-1;}
function getPageScrollX() {if (o3_frame.pageXOffset) return o3_frame.pageXOffset;if (eval(docRoot)) return eval('o3_frame.'+docRoot+'.scrollLeft');return-1;}
function getLayerTop(layer) {if (layer.pageY) return layer.pageY;if (layer.style.top) return parseInt(layer.style.top);return-1;}
function getLayerLeft(layer) {if (layer.pageX) return layer.pageX;if (layer.style.left) return parseInt(layer.style.left);return-1;}
function repositionOver() {var X, Y, pgLeft, pgTop;pgTop=getPageScrollY();pgLeft=getPageScrollX();X=getLayerLeft(over)-pgLeft;Y=getLayerTop(over)-pgTop;
if (X!=over.scroller.x||Y!=over.scroller.y) repositionTo(over, pgLeft+over.scroller.x, pgTop+over.scroller.y);over.scroller.timer=setTimeout("repositionOver()", over.scroller.refresh);}
registerRunTimeFunction(setScrollVariables);registerCmdLineFunction(parseScrollExtras);registerHook("hideObject",cancelScroll,FAFTER);registerHook("placeLayer",scroll_placeLayer,FREPLACE);
