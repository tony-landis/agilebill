{$list->unserial($product.prod_plugin_data, "plugin_data")} 
 
{if $plugin_data.virtual_number || $plugin_data.provision_enabled || $plugin_data.fax_account || $plugin_data.remote_call_forwarding}
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

{if $admin}
<p>Ported Number:<br>
<input type="text" id="voip_ported" name="attr[ported]"></p>
{/if} 

{/if}
   
{if $plugin.parent_enabled && $SESS_LOGGED}
<p><strong>Associated Account: </strong><br>
{$method->exe_noauth('voip','menu_parent_service')}</p> 
{/if}
 

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