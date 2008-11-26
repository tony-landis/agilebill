{$method->exe_noauth("host_tld","suggest")}
<table width=100% border="0" cellspacing="1" cellpadding="0" class="table_background">
  <tr valign="middle" class="row2"> 
    <td width="96%">
       <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_heading_cart" height="25">
         <tr valign="top"> 
           
          <td> 
            {translate module=host_tld}
            suggest_heading
            {/translate}
          </td>
        </tr>
      </table>  
    </td>
  </tr>
  <tr valign="top" class="row2"> 
    <td width="96%"> 
      <form name="domain" method="post" action="javascript:void(0);" onSubmit="search();">        
       <script language="JavaScript">  
		{$javascript}
		{literal} 
		var tldDone = new Array();		 
		var available = '<b>{/literal}{translate module=host_tld}suggest_available{/translate}{literal}</b>';  
		var unavailable = '{/literal}{translate module=host_tld}suggest_reserved{/translate}{literal}';
		var searching 	= '<font color="#FF0000"><b>{/literal}{translate module=host_tld}suggest_searching{/translate}{literal}<b></font>';		 
		function domainSwitch(element)  {
			var domain = document.getElementById('domainName').value;
			var tld = document.getElementById('domainTLD').value;
			if(document.getElementById(element+"_check").checked == false)
			domainUnselect(domain, tld, element);
			else
			domainSelect(domain, tld, element); 
			checkCart();
		} 
		function domainSelect(domain, tld, element) {
			document.getElementById(element+"_check").checked = true; 
			document.getElementById(element+"_area").className  = 'row_mouse_over_select';			
		} 
		function domainUnselect(domain, tld, element) {
			document.getElementById(element+"_check").checked 	= false;
			document.getElementById(element+"_area").className  = 'row_select';
		}  	 
		function domainUpdate(domain,tld,type,element,status) {	
			if(element == 'default') for(i=0; i<tldCount; i++) tldDone[tldArr[i]] = false; 
			tldDone[tld] = true;			 
			if (status == 1) {
				document.getElementById(element+"_status").innerHTML=available;  
				document.getElementById(element+"_check").disabled = false;
				if(element == 'default') {
					domainSelect(domain, tld, element) ;
				} else {
					domainUnselect(domain, tld, element);
				}
			} else {
				document.getElementById(element+"_status").innerHTML=unavailable; 
				document.getElementById(element+"_name").innerHTML= domain+'.'+tld; 
				document.getElementById(element+"_area").className = 'row1';
				document.getElementById(element+"_check").disabled = true;
				document.getElementById(element+"_check").checked = false; 				
			}    
			for(i=0; i<tldCount; i++)
			{   
				if(tldDone[tldArr[i]] != true) 
				{    
					document.getElementById(tldArr[i]+"_area").className = 'row_mouse_over';
					document.getElementById(tldArr[i]+"_area").style.display='block'; 
					document.getElementById(tldArr[i]+"_name").innerHTML = '<b>'+domain+'.'+tldArr[i]+'</b>';
					document.getElementById(tldArr[i]+"_status").innerHTML = searching; 					 					
					domainSearch(domain,tldArr[i],tldArr[i]); 
					break;
				}				 
			}
			checkCart(); 
		}  
	
	   var http = getHTTPObject();				  
		 function domainSearch(domain,tld,element) {  
		  var url = 'ajax.php?do[]=host_tld:whois&type=suggest&element='+element+'&tld='+tld+'&domain='+domain;
		  http.open("GET", url, true); 
		  http.onreadystatechange = function() {
			if (http.readyState == 4) {  
			  try { eval(http.responseText) } catch(e) {} 
			}
		  } 
		 http.send(null);
		}
			  		
		function search() { 
			var domain = document.getElementById('domainName').value;
			var tld = document.getElementById('domainTLD').value;
			for(i=0; i<tldCount; i++)
			{  
				if(tldArr[i] != tld) 
				{ 
					document.getElementById(tldArr[i]+"_area").style.display='block';  
					document.getElementById(tldArr[i]+"_name").innerHTML = '<b>'+domain+'.'+tldArr[i]+'</b>';					
					document.getElementById(tldArr[i]+"_status").innerHTML = '...'; 
					document.getElementById(tldArr[i]+"_area").className = 'row2';
					document.getElementById(tldArr[i]+"_check").disabled = true;
					document.getElementById(tldArr[i]+"_check").checked = false;					
				} else {
					document.getElementById(tldArr[i]+"_area").style.display='none';  
				}			 
			}  	   			
			domainSearch(domain,tld,'default'); 
						
			document.getElementById("default_area").style.display='block';   
			document.getElementById("default_name").innerHTML = '<b>'+domain+'.'+tld+'</b>';
			document.getElementById("default_area").className = 'row1';
			document.getElementById("default_area").className  = 'row_mouse_over';
			document.getElementById("default_status").innerHTML = searching;  
			document.getElementById("default_check").disabled = true;
			document.getElementById("default_check").checked = false;												
		} 
		function cartUrl() { 
			var domain = document.getElementById('domainName').value;
			var tld = document.getElementById('domainTLD').value;		
			var url 	= ''; 
			if(document.getElementById("default_check").checked == true) {
				url = url + '&domain_name[]='+domain;
				url = url + '&domain_tld[]='+tld;
				url = url + '&host_type[]=register';
			} 
			for(i=0; i<tldCount; i++) {  
				if(document.getElementById(tldArr[i]+"_check").checked == true) { 
					url = url + '&domain_name[]='+domain;
					url = url + '&domain_tld[]='+tldArr[i];
					url = url + '&host_type[]=register';		
				}
			} 
			
			if(url == '')
				return false;
			else
				return '?_page=cart:cart&do[]=cart:add'+url; 
			document.location = url;
		} 
		function cartAdd() {
			document.location = cartUrl();
		} 
		function checkCart() {
			var url = cartUrl(); 
			if(url == false)
			{ 
				document.getElementById("available").style.display='none'; 
				document.getElementById("unavailable").style.display='block'; 
			} else { 
				document.getElementById("available").style.display='block'; 
				document.getElementById("unavailable").style.display='none';
			}
		} 
		</script>
        {/literal} 
        <input type="hidden"  id="domain_name"    name="domain_name"    value="0">
        <input type="hidden"  id="domain_tld"     name="domain_tld"     value="0">
        <input type="hidden"  id="domain_option"  name="domain_option"  value="0">
        <input type="hidden"  name="_page"        value="cart:cart"> 
        <table width="100%" border="0" cellspacing="0" cellpadding="3" class="row1">
          <tr> 
            <td width="95%"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="3">
                <tr> 
                  <td width="15%"> 
                    <input type="text" id="domainName" name="domain"  maxlength="128" size="22" onChange="parent.document.getElementById('domain_name').value = this.value;" value="{$VAR.domain}">
                  </td>
                  <td width="6%"> 
                    <select id="domainTLD" name="tld" onChange="parent.document.getElementById('domain_tld').value = this.value;" >
                      { if $list->smarty_array("host_tld", "name", "", "tld") }
                      {foreach from=$tld item=tld}
                      <option value="{$tld.name}" {if $tld.name == $VAR.tld}selected{/if}> 
                      {$tld.name}
                      </option>
                      {/foreach}
                      {/if}
                    </select>
                  </td>
                  <td width="20"><a href="javascript:search();"><img src="themes/{$THEME_NAME}/images/icons/srch_16.gif" border="0" width="16" height="16"></a><b></b></td>
                  <td width="68%" class="body"><a href="javascript:search();"><b><u> 
                    {translate}
                    search 
                    {/translate}
                    </u></b></a></td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="3" class="row1">
                <tr> 
                  <td> 
                    <div id="search" style="display:none"><b> </b></div>
                    <div id="instructions"> <b> 
                      {translate module=host_tld}
                      register_instructions_s 
                      {/translate}
                      </b> </div>
                  </td>
                </tr>
                <tr> 
                  <td width="13%"> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr> 
            <td width="95%"> 
              <!-- USER SELECTED TLD RESULTS -->
              <div id="default_area" style="display:none"> 
                <table width="100%" border="0" cellspacing="0" cellpadding="3" class="body">
                  <tr> 
                    <td width="5%"> 
                      <input type="checkbox" id="default_check" name="default_check" value="checkbox" onClick="domainSwitch('default')">
                    </td>
                    <td width="40%"> 
                      <div id="default_name"></div>
                    </td>
                    <td width="52%"> 
                      <div id="default_status"></div>
                    </td>
                    <td width="8%">&nbsp;</td>
                  </tr>
                </table>
              </div>
              <!-- AUTO SEARCH TLD RESULTS -->
              {foreach from=$tlds item=tld}
              <div id="{$tld.name}_area" style="display:none"> 
                <table width="100%" border="0" cellspacing="0" cellpadding="3" class="body">
                  <tr> 
                    <td width="5%"> 
                      <input type="checkbox" id="{$tld.name}_check" name="{$tld.name}_check" value="checkbox" onClick="domainSwitch('{$tld.name}')">
                    </td>
                    <td width="40%"> 
                      <div id="{$tld.name}_name"></div>
                    </td>
                    <td width="52%">
                      <div id="{$tld.name}_status"></div>
                    </td>
                    <td width="8%">&nbsp;</td>
                  </tr>
                </table>
              </div>
              {/foreach}
            </td>
          </tr>
        </table> 
      </form>  
	  <div id="available" style="display:none">
	    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="row1">
          <tr> 
            <td width="95%" valign="middle" align="center"> <b> </b>
              <div id="search" style="display:none"><b> </b></div>
              <div id="instructions"> <b> 
                {translate module=host_tld}
                suggest_select
                {/translate}
                </b></div>
              <b><br>
              <br>
              <input type="submit" name="Submit" value="{translate module=host_tld}suggest_purchase{/translate}" onClick="cartAdd()">
              </b> </td>
        </tr>
      </table>
	  </div>
	  
	  <div id="unavailable" style="display:none">
	    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="row1">
          <tr> 
            <td width="95%" valign="middle" align="center"> <b> </b>
              <div id="search" style="display:none"><b> </b></div>
              <div id="instructions"> <b> 
                {translate module=host_tld}
                suggest_select
                {/translate}
                </b><b><br>
                <br>
              </b> </div>
            </td>
          </tr>
        </table>
	  </div> 
    </td>
  </tr>
</table>
 <script language="JavaScript"> 
   {if $VAR.domain != ""}
   	search();
   {else}
   	document.domain.domain.focus();
   {/if}   
</script>  
<br>
<p><a href="?_page=host_tld:search_transfer"><b>{translate module=host_tld}domain_transfer{/translate}</b></a></p>
 
