{$list->unserial($product.prod_plugin_data, "plugin_data")} 
 
{if $plugin_data.type == 'pin'}  
<!-- PIN CODE AUTHENTICATION -->
{if $SESS_LOGGED}
<p> 
{$method->exe_noauth('voip_prepaid','menu_pins')}
{if $pins}
<p><b>You can select an existing Pin # below to replenish, or leave the selection as is and we will generate a new Pin number.</b></p>
<select name="attr[pin]">{html_options options=$pins}</select> 
{/if}
{else}You are not logged in. If you already have an account and have purchased our prepaid service in the past, please login and return to this page if you wish to select one of your existing pins to add minutes to. {/if} 
{literal}
<script language="javascript">
function product_plugin_validate() {return true;} 
</script>
{/literal}




{elseif $plugin_data.type == 'ani'} 
<!-- ANI AUTHENTICATION -->
<p>
<p><b>Enter the number you will be calling from, we will authenticate against this number.</b></p>
<input id="ani_new" name="attr[ani_new]" type="text">
Example: 18885551212 
{if $SESS_LOGGED}
{$method->exe_noauth('voip_prepaid','menu_ani')}
{if $ani}
<p><b>You can select an existing number below to replenish, or leave enter a new number above to create a separate balance for a new ANI.</b></p>
<select id="ani_old" name="attr[ani_old]">{html_options options=$ani}</select> 
{else} <input id="ani_old" type="hidden" value="0">{/if}
{else}
<p>You are not logged in. If you already have an account and have purchased our prepaid service in the past, please login and return to this page if you wish to select one of your existing pins to add minutes to. <input id="ani_old" type="hidden" value="0"></p>
{/if} 
{literal}
<script language="javascript">
var http = getHTTPObject();
function product_plugin_validate()
{  
	var ani_new = document.getElementById('ani_new').value;
	var ani_old = document.getElementById('ani_old').value; 
	if(ani_old != 0 && ani_old != '' && ani_new.length == 0) return true;  
	if(ani_new == '' || ani_new.length < 11) {
		alert( "Please enter a valid number to continue. " );
		return false;
	} 
	var charpos = ani_new.search("[^0-9]"); 
    if(ani_new.length > 0 &&  charpos >= 0)  {  
		alert('Only numeric digits allowed');         
    	return false; 
   	}  
	return true;  
}  
</script>
{/literal}

{elseif $plugin_data.type == 'did'}
<!-- SIP AUTHENTICATION -->

{literal}
<script>
function switchDidType() {
	var old=document.getElementById('newdid').checked; 
	if(old==false) {
		document.getElementById('olddidDiv').style.display='block';
		document.getElementById('newdidDiv').style.display='none';
		try{document.getElementById('voip_station').name='';} catch(e) {}
		try{document.getElementById('voip_station_old').name='attr[station]';} catch(e) {}
	} else {
		document.getElementById('olddidDiv').style.display='none';
		document.getElementById('newdidDiv').style.display='block'; 
		try{document.getElementById('voip_station_old').name='';} catch(e) {}
		try{document.getElementById('voip_station').name='attr[station]';} catch(e) {}
	}
} 
</script>
{/literal} 

<p><b><input id="newdid" name="didtype" type="radio" value="1" checked onClick="switchDidType()" /> 
Setup a new prepaid account, please select this option and select your location below.</b></p>


<div id="newdidDiv">
<p>Select your country: <br>
{$method->exe_noauth('voip','menu_countries')} </p>
<div id="div_state" {style_hide}>
<p>Select your State: <br>
  {$method->exe_noauth('voip','menu_states')}</p>
</div>
<div id="div_location" {style_hide}>
<p>Select your Location:<br> 
  <select id="voip_location" name="attr[location]" onChange="voipChangeLocation(this.value)"></select></p>
</div>
<div id="div_station" {style_hide}>
<p>Select your Number:<br> 
  <select id="voip_station" name="attr[station]"></select></p>
</div> 
</div>
   
<p><b><input id="olddid" name="didtype" type="radio" value="1" onClick="switchDidType()" />
Add a balance to an existing prepaid account, please select this option.</b></p>
 
<div id="olddidDiv" {style_hide}>
{if $SESS_LOGGED}  
{$method->exe_noauth('voip_prepaid','menu_did')}
{if $dids}<select id="voip_station_old" name="">{html_options options=$dids}</select>{/if}
{else}
You are not logged in. If you already have an account and have purchased our prepaid service in the past, please login and return to this page if you wish to select one of your existing pins to add minutes to. 
<input id="station_old" type="hidden" value="0">
{/if}    
   
{if $plugin.parent_enabled && $SESS_LOGGED}
<p>Associated Account: <br>
{$method->exe_noauth('voip','menu_parent_service')}</p> 
{/if}
  
{if $admin}
<p>Ported Number:<br>
<input type="text" id="voip_ported" name="attr[ported]"></p>
{/if} 
</div>

{literal}
<script language="javascript">
var http = getHTTPObject();

// change selected country
function voipChangeCountry(code) { 
	menuClearOptions('voip_location');
	menuClearOptions('voip_station');	
	if(code == '1' ) {
		document.getElementById('div_state').style.display='block';  
	} else if (code != '') {
		document.getElementById('div_state').style.display='none';
		document.getElementById('div_location').style.display='block';
		document.getElementById('div_station').style.display='none';
		voipGetLocationListByCountry(code); 
	} else {
		document.getElementById('div_state').style.display='none';
		document.getElementById('div_location').style.display='none';
		document.getElementById('div_station').style.display='none';	
	}
}

// change selected state
function voipChangeState(state) { 
	document.getElementById('div_location').style.display='block';
	if(state) {  
		voipGetLocationList(state);
	} else {  
		menuClearOptions('voip_location');
		menuClearOptions('voip_station');		 
	}
} 

// change selected location
function voipChangeLocation(location) {  
	document.getElementById('div_station').style.display='block';    
	if(location) {  
		voipGetStationList(location,'');
	} else {  	
		menuClearOptions('voip_station');
	}
} 

// update location list
function voipGetLocationList(state) { 
 var url = "ajax.php?do[]=voip:menu_location&state="+state+"&id="+document.product_view.product_id.value;
 http.open("GET", url, true); 
 http.onreadystatechange = function() {
    if (http.readyState == 4) {  
	  try { eval(http.responseText) } catch(e) {} 
    }
  } 
  http.send(null);
}  

function voipGetLocationListByCountry(country) { 
 var url = "ajax.php?do[]=voip:menu_location&country="+country+"&id="+document.product_view.product_id.value;
 http.open("GET", url, true); 
 http.onreadystatechange = function() {
    if (http.readyState == 4) {  
	  try { eval(http.responseText) } catch(e) {} 
    }
  }
  http.send(null);
} 

// update Station list
function voipGetStationList(location,country) { 
 var url = "ajax.php?do[]=voip:menu_station&location="+location+"&country="+country+"&id="+document.product_view.product_id.value;
 http.open("GET", url, true); 
 http.onreadystatechange = function() {
    if (http.readyState == 4) { 
      try { eval(http.responseText) } catch(e) {} 
    }
  } 
  http.send(null);
}
 
function product_plugin_validate() 
{	 
{/literal}{if $admin}{literal}
	if(document.getElementById('voip_ported').value!='') {
		if(document.getElementById('voip_station').value!='') {
			alert("You cannot select both a Phone Number and Ported Number");
			return false;
		}
		return true;
	} 
{/literal}{/if}{literal}   

	// validate old did
	var oldChecked=document.getElementById('newdid').checked;
	if(oldChecked==false && document.getElementById('voip_station_old').value < 1){
		alert( "Please select a number to continue. " );
		return false;
	} else if(oldChecked==false && document.getElementById('voip_station_old').value != "" ) {
		return true;
	}
	 
	
	if(document.getElementById('voip_country').value=='') {
		alert( "Please select a country to continue. ");
		return false;	
	} else if(document.getElementById('voip_country').value=='USA') {
		if(document.getElementById('voip_state').value=='') {
			alert( "Please select a state to continue. ");
			return false;
		} 
		if(document.getElementById('voip_location').value=='') {
			alert( "Please select a location and area code to continue." );
			return false;
		} 
	}
	if(document.getElementById('voip_station').value=='') {
		alert( "Please select a number to continue. " );
		return false;
	} 
	return true;
}

<!-- menu handlers -->
function menuClearOptions(element) {
  var elSel = document.getElementById(element);  var i; for (i = elSel.length - 1; i>=0; i--)  elSel.remove(i);
} 
function menuAppendOption(element,value,text) {
  var elOptNew = document.createElement('option');
  elOptNew.text = text;
  elOptNew.value = value;
  var elSel = document.getElementById(element); 
  try {  elSel.add(elOptNew, null); } catch(ex) { elSel.add(elOptNew); }
} 
</script>
{/literal}
 
{/if} 