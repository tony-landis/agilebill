var http=getHTTPObject();
var validatedDomain=false;
var domain=null;
var tld=null;

function cartAdd(type) { 
	if(validatedDomain) { 
		getUserDomainInfo();
		document.location = '?_page=cart:cart&do[]=cart:add&host_type='+type+'&domain_name='+domain+'&domain_tld='+tld;
	} else {
		alert('Invalid domain!');
	}
}
  
function showSuggest() {
	getUserDomainInfo();
	if(domain==null || domain=='')
		document.location = '?_page=host_tld:suggest&domain='+domain+'&tld='+tld;		
	else
		document.location = '?_page=host_tld:suggest';			
}
		 			
function getUserDomainInfo() {
	domain = document.getElementById("domainName").value; 
	tld = document.getElementById("domainTLD").value;  
}
					  
function domainSearchResponse(domain,tld) {   
  http.open("GET", 'ajax.php?do[]=host_tld:whois&type='+type+'&tld='+tld+'&domain='+domain, true); 
  http.onreadystatechange = function() {
   if (http.readyState == 4) {  
 	try { eval(http.responseText) } catch(e) {} 
   }
 } 
 http.send(null);
} 						
 
function domainSearch() { 
	getUserDomainInfo();
	validatedDomain=false;
	try{ window.parent.domainUpdate(0,0,0) } catch(e) {}
	if(domain!=null || domain!='') {
		document.getElementById("search").style.display='block';
		try{ document.getElementById("instructions").style.display='none' }catch(e){}
		document.getElementById("available").style.display='none';
		document.getElementById("unavailable").style.display='none'; 
		domainSearchResponse(domain,tld,type); 	
	} else {
		unavailable();
	}
}
			
function available(i) {
	validatedDomain=true;
	document.getElementById("available").style.display='block'; 
	document.getElementById("search").style.display='none';
	document.getElementById("unavailable").style.display='none'; 	
	if(i==1 || i==true ) try{ document.getElementById("park_available").style.display='block' } catch(e) {} 
	try{ window.parent.domainUpdate(domain,tld,type) } catch(e) {}
} 
  
function unavailable() {
	validatedDomain=false;
	document.getElementById("unavailable").style.display='block'; 
	document.getElementById("search").style.display='none';
	document.getElementById("available").style.display='none';
	try{ document.getElementById("park_available").style.display='none'; } catch(e) {} 
	try{ window.parent.domainUpdate(0,0,0) } catch(e) {}			
} 