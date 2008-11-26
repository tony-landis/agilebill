//\/////
//\  overLIB Anchor Plugin
//\  This file requires overLIB 4.00 or later.
//\
//\  overLIB 4.05 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2004. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//\/////
if (typeof olInfo=='undefined'||olInfo.simpleversion<400) alert('overLIB 4.00 or later is required for the Anchor Plugin.');registerCommands('anchor,anchorx,anchory,noanchorwarn,anchoralign');
if (typeof ol_anchor=='undefined') var ol_anchor='';if (typeof ol_anchorx=='undefined') var ol_anchorx=0;if (typeof ol_anchory=='undefined') var ol_anchory=0;if (typeof ol_noanchorwarn=='undefined') var ol_noanchorwarn=1;if (typeof ol_anchoralign=='undefined') var ol_anchoralign='UL';
var o3_anchor="";var o3_anchorx=0;var o3_anchory=0;var o3_noanchorwarn=1;var o3_anchoralign='UL';var mrkObj, rmrkPosition;
function setAnchorVariables() {o3_anchor=ol_anchor;o3_anchorx=ol_anchorx;o3_anchory=ol_anchory;o3_noanchorwarn=ol_noanchorwarn;o3_anchoralign=ol_anchoralign;mrkObj=null;}
function parseAnchorExtras(pf,i,ar) {var v, k=i;
if (k<ar.length) {if (ar[k]==ANCHOR) { eval(pf+"anchor='"+escSglQuote(ar[++k])+"'"); return k; }
if (ar[k]==ANCHORX) { eval(pf+'anchorx='+ar[++k]); return k; }
if (ar[k]==ANCHORY) { eval(pf+'anchory='+ar[++k]); return k; }
if (ar[k]==NOANCHORWARN) { eval(pf+'noanchorwarn=('+pf+'noanchorwarn==1)?0:1'); return k; }
if (ar[k]==ANCHORALIGN) { k=opt_MULTIPLEARGS(++k, ar, (pf+'anchoralign'));  return k; }
}
return-1;}
function checkAnchorObject() {var w=o3_anchor;
if (w) {if (!(mrkObj=getAnchorObjectRef(w))) {if (o3_noanchorwarn) {alert('WARNING!  Reference mark "'+w+'" not found.');return false;} else w='';}
}
return true;}
function anchorPlaceLayer() {var placeX, placeY, widthFix=0;
if (eval('o3_frame.'+docRoot)&&eval("typeof o3_frame."+docRoot+".clientWidth=='number'")&&eval('o3_frame.'+docRoot+'.clientWidth')) {iwidth=eval('o3_frame.'+docRoot+'.clientWidth');} else if (typeof(o3_frame.innerWidth)=='number') {widthFix=Math.ceil(1.2*(o3_frame.outerWidth-o3_frame.innerWidth));iwidth=o3_frame.innerWidth;}
if (o3_anchor) {placeX=rmrkPosition[0];placeY=rmrkPosition[1];} else {
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
repositionTo(over, placeX, placeY);if (typeof o3_scroll!='undefined'&& o3_scroll&& o3_sticky&& (o3_relx||o3_rely)&& (typeof o3_draggable=='undefined'||!o3_draggable)) {if (typeof over.scroller=='undefined'||over.scroller.canScroll) over.scroller=new scroller(placeX-winoffset, placeY-scrolloffset, setTimeout("repositionOver()", 20));}
}
function anchorPreface() {if (!mrkObj) return;rmrkPosition=getAnchorLocation(mrkObj);}
function getAnchorObjectRef(aObj) {return getRefById(aObj, o3_frame.document)||getRefByName(aObj, o3_frame.document)
}
function getAnchorLocation(objRef){var mkObj, of, offsets, mlyr
mkObj=mlyr=objRef
offsets=[o3_anchorx, o3_anchory]
if (document.layers){if (typeof mlyr.length!='undefined'&& mlyr.length>1) {mkObj=mlyr[0]
offsets[0]+=mlyr[0].x+mlyr[1].pageX
offsets[1]+=mlyr[0].y+mlyr[1].pageY
} else {if(mlyr.toString().indexOf('Image')!=-1||mlyr.toString().indexOf('Anchor')!=-1){offsets[0]+=mlyr.x
offsets[1]+=mlyr.y
} else {offsets[0]+=mlyr.pageX
offsets[1]+=mlyr.pageY
}
}
} else {offsets[0]+=pageLocation(mlyr, 'Left')
offsets[1]+=pageLocation(mlyr, 'Top')
}
of=getAnchorOffsets(mkObj)
if (typeof o3_dragimg!='undefined'&& o3_dragimg) {olImgLeft=offsets[0];olImgTop=offsets[1];}
offsets[0]+=of[0]
offsets[1]+=of[1]
if (typeof o3_dragimg!='undefined'&& o3_dragimg) {olImgRight=offsets[0];olImgBottom=offsets[1];return;}
return offsets;}
function getAnchorOffsets(mkObj){var fx=fy=0,  mp, puc, mkAry, sx=sy=0, w=o3_anchoralign
var mW=mH=pW=pH=0
var off=[0, 0]
mkAry=w.split(',');
if (mkAry.length<3) {mp=mkAry[0].toUpperCase();puc=(mkAry.length==1)?mp:mkAry[1].toUpperCase();} else if (mkAry.length==3) {if (!isNaN(mkAry[0])) {mp=mkAry.slice(0, 2);puc=mkAry[2].toUpperCase();} else {mp=mkAry[0].toUpperCase();puc=mkAry.slice(1);}
} else {mp=mkAry.slice(0, 2);puc=mkAry.slice(2);}
var shdwPresent=typeof o3_shadow!='undefined'&& o3_shadow
if (shdwPresent) {sx=Math.abs(o3_shadowx);sy=Math.abs(o3_shadowy);}
pW=(shdwPresent?parseInt(o3_width):(olNs4?over.clip.width:over.offsetWidth))
pH=(shdwPresent?parseInt(o3_aboveheight):(olNs4?over.clip.height:over.offsetHeight))
if (olOp&& o3_wrap) {pW=(shdwPresent?parseInt(o3_width):(olNs4?over.clip.width:over.offsetWidth))
pH=(shdwPresent?parseInt(o3_aboveheight):(olNs4?over.clip.height:over.offsetHeight))
}
if (!olOp&& mkObj.toString().indexOf('Image')!=-1){mW=mkObj.width
mH=mkObj.height
} else if (!olOp&& mkObj.toString().indexOf('Anchor')!=-1) {mp='UL'
} else {mW=(olNs4)?mkObj.clip.width:mkObj.offsetWidth
mH=(olNs4)?mkObj.clip.height:mkObj.offsetHeight
}
if (!isNaN(mp)||typeof mp=='object') {if (typeof mp=='object') {fx=parseFloat(mp[0]);fy=parseFloat(mp[1]);} else
fx=fy=parseFloat(mp);off=[Math.round(fx*mW), Math.round(fy*mH)];} else {if (mp=='UR') off=[mW, 0]
else if (mp=='LL') off=[0, mH]
else if (mp=='LR') off=[mW, mH]
}
if (typeof o3_dragimg!='undefined'&& o3_dragimg) return off;else {if (!isNaN(puc)||typeof puc=='object' ) {if (typeof puc=='object') {fx=parseFloat(puc[0]);fy=parseFloat(puc[1]);} else
fx=fy=parseFloat(puc);off[0]-=Math.round(fx*(pW-sx));off[1]-=Math.round(fy*(pH-sy));} else {if (puc=='UR') {off[0]-=(pW-sx);off[1]-=sy
} else if (puc=='LL') {off[0]-=sx;off[1]-=(pH-sy)
} else if (puc=='LR') {off[0]-=(pW-sx);off[1]-=(pH-sy)
}
}
return off
}
}
function pageLocation(o, t){var x=0
while(o.offsetParent){x+=o['offset'+t]
o=o.offsetParent
}
x+=o['offset'+t]
return x
}
function getRefById(l, d){var r="", j
d=(d||document)
if (d.all) return d.all[l]
else if (d.getElementById) return d.getElementById(l)
else if (d.layers&& d.layers.length>0) {if (d.layers[l]) return d.layers[l]
for (j=0; j<d.layers.length; j++) {r=getRefById(l, d.layers[j].document)
if(r) return r
}
}
return false
}
function getRefByName(l, d) {var r=null, j
d=(d||document)
if (d.images[l]) return d.images[l]
else if (d.anchors[l]) return d.anchors[l];else if (d.layers&& d.layers.length>0) {for (j=0; j<d.layers.length; j++) {r=getRefByName(l, d.layers[j].document)
if (r&& r.length>0) return r
else if (r) return [r, d.layers[j]]
}
}
return null
}
registerHook("placeLayer", anchorPlaceLayer, FREPLACE);registerRunTimeFunction(setAnchorVariables);registerCmdLineFunction(parseAnchorExtras);registerPostParseFunction(checkAnchorObject);registerHook("createPopup", anchorPreface, FAFTER);
