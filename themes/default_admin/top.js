function get_cookie(name) {
  var search = name + "="
  var returnvalue = "";
  if (document.cookie.length > 0) {
    offset = document.cookie.indexOf(search)
    if (offset != -1) {
      offset += search.length
      end = document.cookie.indexOf(";", offset);
      if (end == -1) end = document.cookie.length;
      returnvalue=unescape(document.cookie.substring(offset, end))
      }
   }
  return returnvalue;
}

// check for cookies to determine if forms/urls need the session included.
var SESS_check = get_cookie(cookie_name);
if (SESS_check != SESS) {
	// cookies on
	var COOKIE = true;
	var COOKIE_URL = '';
	var COOKIE_FORM= '';
} else {
 	//cookies off
	var COOKIE = false;
	var COOKIE_URL = '&s=' + SESS;
	var COOKIE_FORM = '<input type="hidden" name="s" value="' + SESS + '">';
}

function refresh(delay,url) {
	document.write('<meta http-equiv="refresh" content="' + delay + ';URL=' + url + '">');
}

function NewWindow(win,settings,url) {
	var eval1;
	eval1 = win + '=window.open("' + url + '","' + win + '","' + settings + '");';
	eval(eval1);
}

if(sess_expires >= 120) {	
	sess_timeout = (sess_expires * 1000) - 60000;
	var url 		= URL+'?_page=core:sess_timeout&_escape=true';
	var win 		= 'SessTimeoutWin';
	var settings 	= 'toolbar=no,status=yes,width=200,height=200';
	ident=window.setTimeout("NewWindow(win,settings,url)",sess_timeout);	
}

// Change the class for an object
function class_change(obj,style) {
	if(navigator.userAgent.indexOf("Netscape") != -1) {
		var obj1 = document.getElementById(obj);
		obj1.setAttribute('class', style);
		return;
	} else if(navigator.userAgent.indexOf("MSIE") != -1) {
		eval(obj+'.className = "'+style+'";');	
		return;		
	} else if(navigator.userAgent.indexOf("Mozilla") != -1) {
		var obj1 = document.getElementById(obj);
		obj1.setAttribute('class', style);
		return;
	} else {
		eval(obj+'.className = "'+style+'";');	
		return;					
	}		
}

// View a record based on the item selected from the drop-down menu ($list->menu)
function menu_item_view(module,id) {
    var selected_id;
    eval ('selected_id = document.getElementById("'+id+'").value;');
    if(selected_id != '')
    document.location = '?_page='+module+':view&id='+selected_id+',&s='+SESS;
}

// Add a record based on the item selected from the drop-down menu ($list->menu)
function menu_item_add(module,id) {
    var selected_id2;
    eval ('selected_id2 = document.getElementById("'+id+'").value;');
    if(selected_id2 != '')
    document.location = '?_page='+module+':add&id='+selected_id2+',&s='+SESS;
}

// Display a Iframe
function showIFrame(element,width,height,src) {
    document.getElementById(element).style.width   = width+'px';
    document.getElementById(element).style.height  = height+'px';
    if(src != false)
		frames[element].location.href = src;
}

// Hide an IFrame
function hideIFrame(element) {
    document.getElementById(element).style.width  = '0px';
    document.getElementById(element).style.height = '0px';
    document.getElementById(element).style.align  = 'center';
 	return false;
}

// Get the page width
function getPageWidth(defaultWidth)  { 
	if (self.innerWidth) {
		return self.innerWidth * .96; 
		//return defaultWidth;
	} else if (document.documentElement && document.documentElement.clientWidth) {
		//return document.documentElement.clientWidth; 
		return defaultWidth * .96;
	} else if (document.body) { 
		return document.body.clientWidth *.96;
	} else {
		return defaultWidth;
	}
}

function getHTTPObject() {
  var xmlhttp;
  /*@cc_on
  @if (@_jscript_version >= 5)
	try {
	  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
	  try {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  } catch (E) {
		xmlhttp = false;
	  }
	}
  @else
  xmlhttp = false;
  @end @*/
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
	try {
	  xmlhttp = new XMLHttpRequest();
	} catch (e) {
	  xmlhttp = false;
	}
  }
  return xmlhttp;
}