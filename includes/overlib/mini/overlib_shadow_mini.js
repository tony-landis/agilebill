//\/////
//\  overLIB Shadow Plugin
//\  This file requires overLIB 4.00 or later.
//\
//\  overLIB 4.05 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2003. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//\/////
if (typeof olInfo=='undefined'||olInfo.simpleversion<400) alert('overLIB 4.00 or later is required for the Shadow Plugin.');registerCommands('shadow,shadowcolor,shadowimage,shadowopacity,shadowx,shadowy');
if (typeof ol_shadowadjust=='undefined') var ol_shadowadjust=2;if (typeof ol_shadow=='undefined') var ol_shadow=0;if (typeof ol_shadowcolor=='undefined') var ol_shadowcolor='#CCCCCC';if (typeof ol_shadowimage=='undefined') var  ol_shadowimage='';if (typeof ol_shadowopacity=='undefined') var  ol_shadowopacity=0;if (typeof ol_shadowx=='undefined') var ol_shadowx=5;if (typeof ol_shadowy=='undefined') var ol_shadowy=5;
var o3_shadow=0;var o3_shadowcolor="#cccccc";var o3_shadowimage='';var o3_shadowopacity=0;var o3_shadowx=5;var o3_shadowy=5;var bkSet=0;
function setShadowVariables() {o3_shadow=ol_shadow;o3_shadowcolor=ol_shadowcolor;o3_shadowimage=ol_shadowimage;o3_shadowopacity=ol_shadowopacity;o3_shadowx=ol_shadowx;o3_shadowy=ol_shadowy;}
function parseShadowExtras(pf,i,ar) {var k=i, v;
if (k<ar.length) {if (ar[k]==SHADOW) { eval(pf+'shadow=('+pf+'shadow==0)?1:0'); return k; }
if (ar[k]==SHADOWCOLOR) { eval(pf+'shadowcolor="'+ar[++k]+'"'); return k; }
if (ar[k]==SHADOWOPACITY) {v=ar[++k]; eval(pf+'shadowopacity='+(olOp?0:v)); return k; }
if (ar[k]==SHADOWIMAGE) { eval(pf+'shadowimage="'+ar[++k]+'"'); return k; }
if (ar[k]==SHADOWX) { eval(pf+'shadowx='+ar[++k]); return k; }
if (ar[k]==SHADOWY) { eval(pf+'shadowy='+ar[++k]); return k; }
}
return-1;}
function shadow_cursorOff() {var left=parseInt(over.style.left);var top=parseInt(over.style.top);var right=left+(o3_shadow?o3_width:over.offsetWidth);var bottom=top+(o3_shadow?o3_aboveheight:over.offsetHeight);
if (o3_x<left||o3_x>right||o3_y<top||o3_y>bottom) return true;return false;}
function checkShadowPreHide() {if (o3_shadow&&o3_shadowopacity) cleanUpShadowEffects();if (o3_shadow&&(olIe4&&isMac) ) over.style.pixelWidth=over.style.pixelHeight=0;}
function generateShadow(content) {var wd, ht, X=0, Y=0, zIdx=0, txt, dpObj, puObj, bS='', aPos, posStr=new Array();
if (!o3_shadow||(o3_shadowx==0&&o3_shadowy==0)) return;
X=Math.abs(o3_shadowx);Y=Math.abs(o3_shadowy);wd=parseInt(o3_width);if(olHideForm&&!olNs4) ht=over.offsetHeight;
if (o3_shadowx==0) {if (o3_shadowy<0) {posStr[0]=' left:0; top: 0';posStr[1]=' left:0; top: '+Y+'px';} else if (o3_shadowy>0) {posStr[0]=' left:0; top: '+Y+'px';posStr[1]=' left:0; top:0';}
} else if (o3_shadowy==0) {if (o3_shadowx<0) {posStr[0]=' left:0; top: 0';posStr[1]=' left: '+X+'px';} else if (o3_shadowx>0) {posStr[0]=' left: '+X+'px; top: 0';posStr[1]=' left:0; top:0';}
} else if (o3_shadowx>0) {if (o3_shadowy>0) {posStr[0]=' left:'+X+'px; top:'+Y+'px';posStr[1]=' left:0; top:0';} else if (o3_shadowy<0) {posStr[0]=' left:'+X+'px; top:0';posStr[1]=' left:0; top: '+Y+'px';}
} else if (o3_shadowx<0) {if (o3_shadowy>0) {posStr[0]=' left:0; top:'+Y+'px';posStr[1]=' left:'+X+'px; top:0';} else if (o3_shadowy<0) {posStr[0]=' left:0; top:0';posStr[1]=' left:'+X+'px; top:'+Y+'px';}
}
txt=(olNs4)?'<div id="backdrop"></div>':((olIe55&&olHideForm)?bckDropSrc(wd+X,ht+Y,zIdx++):'')+'<div id="backdrop" style="position: absolute;'+posStr[0]+'; width: '+wd+'px; z-index: '+(zIdx++)+'; ';
if (o3_shadowimage) {bS='background-image: url('+o3_shadowimage+');';if (olNs4) bkSet=1;} else {bS='background-color: '+o3_shadowcolor+';';if (olNs4) bkSet=2;}
if (olNs4) {txt+='<div id="PUContent">'+content+'</div>';} else {txt+=bS+'"></div><div id="PUContent" style="position: absolute;'+posStr[1]+'; width: '+wd+'px; z-index: '+(zIdx++)+';">'+content+'</div>';}
layerWrite(txt);
if (olNs4&&bkSet) {dpObj=over.document.layers['backdrop'];if (typeof dpObj=='undefined') return;
puObj=over.document.layers['PUContent'];wd=puObj.clip.width;ht=puObj.clip.height;aPos=posStr[0].split(';');
dpObj.clip.width=wd;dpObj.clip.height=ht;dpObj.left=parseInt(aPos[0].split(':')[1]);dpObj.top=parseInt(aPos[1].split(':')[1]);
dpObj.bgColor=(bkSet==1)?null:o3_shadowcolor;dpObj.background.src=(bkSet==2)?null:o3_shadowimage;dpObj.zIndex=0;
aPos=posStr[1].split(';');puObj.left=parseInt(aPos[0].split(':')[1]);puObj.top=parseInt(aPos[1].split(':')[1]);puObj.zIndex=1;
} else {puObj=(olIe4?o3_frame.document.all['PUContent']:o3_frame.document.getElementById('PUContent'));dpObj=(olIe4?o3_frame.document.all['backdrop']:o3_frame.document.getElementById('backdrop'));ht=puObj.offsetHeight;dpObj.style.height=ht+'px';
if (o3_shadowopacity) {var op=o3_shadowopacity;op=(op<=100?op:100);
if (olIe4&&!isMac) {dpObj.style.filter='Beta(Opacity='+op+')';dpObj.filters.Beta.enabled=true;} else {if (typeof(dpObj.style.MozOpacity)=='string') dpObj.style.MozOpacity=op/100;}
}
}
o3_width=wd+X;o3_aboveheight=ht+Y;}
function cleanUpShadowEffects() {if (olNs4) return;else {var dpObj=(olIe4?o3_frame.document.all['backdrop']:o3_frame.document.getElementById('backdrop'));if (olIe4&&!isMac&&dpObj.filters.Beta) dpObj.filters.Beta.enabled=false;else if (typeof(dpObj.style.MozOpacity)=='string') dpObj.style.MozOpacity=1.0;}
}
function shadowAdjust() {if (!olNs4) return;var fac=ol_shadowadjust;if (olNs4) {document.write('<style type="text/css">\n<!--\n');document.write('#backdrop, #PUContent {position: absolute; left: '+fac*o3_shadowx+'px; top: '+fac*o3_shadowy+'px; }\n');document.write('-->\n<'+'\/style>');}
}
var before=(typeof rmrkPreface!='undefined'?rmrkPreface:null);
registerRunTimeFunction(setShadowVariables);registerCmdLineFunction(parseShadowExtras);registerHook("cursorOff",shadow_cursorOff,FREPLACE);registerHook("hideObject",checkShadowPreHide,FBEFORE);registerHook("createPopup",generateShadow,FAFTER,before);
if (olNs4) shadowAdjust();
