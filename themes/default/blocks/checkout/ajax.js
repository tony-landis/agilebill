var http=getHTTPObject();
var last_checkout_id = '{/literal}{$checkout.fields.id}{literal}';
function changeCheckoutOption(option,type,invoice_id,account_id)  {  
  var doRedirect=false;
  var noConfirm=false; 
  if(type=='invoice' || type=='multi') 
  	var url ='?_page=invoice:checkoutoption&option='+option+'&_escape=1&invoice_id='+invoice_id;
  else
    var url ='?_page=checkout:checkoutoption&option='+option+'&_escape=1';
  if(account_id) url += '&admin=1&account_id='+account_id;
  http.open("GET", url, true); 
  http.onreadystatechange = function() {
   if (http.readyState == 4) {  
    try { document.getElementById('checkout_options_show').style.display='block'; } catch(e) {} 
 	try { document.getElementById('checkout_confirm_div').innerHTML = http.responseText } catch(e) {} 
	try { document.getElementById('checkout_confirm_div').style.display='block'; } catch(e) {}
	try { document.getElementById('checkout_options').style.display='none'; } catch(e) {}
	try { doRedirect=document.getElementById('doredirect').value } catch(e) { doRedirect=false; }
	try { noConfirm=document.getElementById('noconf').value } catch(e) { noConfirm=false; }
	if(doRedirect=='true') if(noConfirm) {checkoutNow(0);} else {verifyCheckout();}
   }
 } 
 http.send(null);	
} 
function getCheckoutOption() {
	return getElementById("checkout_option").value;
} 
function checkoutNow(type) {    
	try { document.getElementById('submit_checkout_form').disabled=true; } catch(e) {}
	try { document.getElementById('checkout_form').submit(); } catch(e) { alert('Unable to submit checkout form for processing'); }
} 
function verifyCheckout() {
	if (confirm(confirmCheckoutMsg)) { 
		checkoutNow(0); 
	} else {
		document.getElementById('checkout_confirm_div').style.display='none';
		document.getElementById('checkout_options_show').style.display='none';
		document.getElementById('checkout_options').style.display='block';
	}
} 
function enter_new_card() {
	try { document.getElementById('new_card').value=1;  } catch(e) {}  
	document.getElementById('onfile').style.display='none';
	document.getElementById('newcard').style.display='block';
}
function editSavedCard(admin) {
	try{var id=document.getElementById('account_billing_id').value;}catch(e){var id=false;} 
	if(id) {
		if(admin) {
			var url = '?_page=account_billing:view&id='+id;
		} else {
			var url = '?_page=account_billing:user_view&id='+id;
		}
		document.location=url;
	}
}