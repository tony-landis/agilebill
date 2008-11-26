// JavaScript Document
var http=getHTTPObject();

function assignInvoice(id, items) { 
  var itemstring='';  
  for(var i=0; i<items.length; i++) { 
	  itemstring += '&items['+ items[i]['id'] + ']=' + items[i]['value'];
	  if(items[i]['value'] == '0') {
		  alert('All products must be assigned');
		  return void(0);
	  }
  } 
  var url = 'ajax.php?do[]=asset_invoice:assign&invoice_id='+id+itemstring; 
  http.open("GET", url, true); 
  http.onreadystatechange = function() {
   if (http.readyState == 4) {  
 	try { 
	  if(http.responseText=='true') {
		document.getElementById(id).innerHTML='<b><center>Success! Asset(s) assigned for invoice # ' + id + '</center></b>';
	  } else {
		alert(http.responseText);  
	  }
	} catch(e) {} 
   }
 } 
 http.send(null);
} 

function setItemValue(items, itemId, value) {
  for(var i=0; i<items.length; i++) { 
	  if(items[i]['id'] == itemId) {
		  items[i]['value'] = value;  
	  }  
  }
}