//\/////
//\  overLIB Exclusive Plugin
//\  This file requires overLIB 4.00 or later.
//\
//\  overLIB 4.05 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2004. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//\/////
if (typeof olInfo=='undefined'||olInfo.simpleversion<400) alert('overLIB 4.00 or later is required for the Debug Plugin.');registerCommands('exclusive,exclusivestatus,exclusiveoverride');var olOverrideIsSet;
if (typeof ol_exclusive=='undefined') var ol_exclusive=0;if (typeof ol_exclusivestatus=='undefined') var ol_exclusivestatus='Please close open popup first.';
var o3_exclusive=0;var o3_exclusivestatus='';
function setExclusiveVariables() {o3_exclusive=ol_exclusive;o3_exclusivestatus=ol_exclusivestatus;}
function parseExclusiveExtras(pf,i,ar) {var k=i,v;
olOverrideIsSet=false;
if (k<ar.length) {if (ar[k]==EXCLUSIVEOVERRIDE) { if(pf!='ol_') olOverrideIsSet=true; return k; }
if (ar[k]==EXCLUSIVE) { eval(pf+'exclusive=('+pf+'exclusive==0)?1:0'); return k; }
if (ar[k]==EXCLUSIVESTATUS) { eval(pf+"exclusivestatus='"+escSglQuote(ar[++k])+"'"); return k; }
}
return-1;}
function isExclusive(args) {var rtnVal=false;
if(args!=null) rtnVal=hasCommand(args, EXCLUSIVEOVERRIDE);
if(rtnVal) return false;else {self.status=(o3_exclusive)?o3_exclusivestatus:'';return o3_exclusive;}
}
function hasCommand(args, COMMAND) {var rtnFlag=false;
for (var i=0; i<args.length; i++) {if (typeof args[i]=='number'&& args[i]==COMMAND) {rtnFlag=true;break;}
}
return rtnFlag;}
function clearExclusive() {o3_exclusive=0;}
function setExclusive() {o3_exclusive=(o3_showingsticky&& o3_exclusive);}
function chkForExclusive() {if (olOverrideIsSet) o3_exclusive=0;
return true;}
registerRunTimeFunction(setExclusiveVariables);registerCmdLineFunction(parseExclusiveExtras);registerPostParseFunction(chkForExclusive);registerHook("createPopup",setExclusive,FBEFORE);registerHook("hideObject",clearExclusive,FAFTER);
