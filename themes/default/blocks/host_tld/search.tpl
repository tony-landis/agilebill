{literal}
<script language="JavaScript">
var type='register';
</script>
<script src="themes/default/blocks/host_tld/ajax.js" type="text/javascript"></script>
{/literal}
<table width=100% border="0" cellspacing="1" cellpadding="0" class="table_background">
  <tr valign="middle" class="row2"> 
    <td width="96%">
       <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_heading_cart" height="25">
         <tr valign="top"> 
           <td> 
		   {translate module=host_tld}
            domain_register 
          	{/translate}
          </td>
        </tr>
      </table>  
    </td>
  </tr>
  <tr valign="top" class="row2"> 
    <td width="96%"> 
      <form name="domain" method="post" action="javascript:void(0);" onSubmit="domainSearch();">    				 
		<table width="100%" border="0" cellspacing="0" cellpadding="3" class="row1">
		  <tr> 
			<td width="95%"> 
			 <table width="100%" border="0" cellspacing="0" cellpadding="3">
			   <tr> 
				 <td width="15%"> 
					<input type="text" id="domainName" name="domainName" maxlength="128" size="22" value="{$VAR.domain}">
				  </td>
				  <td width="6%"> 
					<select id="domainTLD" name="domainTLD">
					  { if $list->smarty_array("host_tld", "name", "", "tld") }
					  {foreach from=$tld item=tld}
					  <option value="{$tld.name}"{if $VAR.tld==$tld.name} selected{/if}> 
					  {$tld.name}
					  </option>
					  {/foreach}
					  {/if}
					</select>
				  </td>
				  <td width="20"><a href="javascript:domainSearch();"><img src="themes/{$THEME_NAME}/images/icons/srch_16.gif" border="0" width="16" height="16"></a><b></b></td>
				  <td width="68%" class="body"><a href="javascript:domainSearch();"><b><u>{translate}search{/translate}</u></b></a></td>
				  <td width="68%"></td>
				</tr>
			  </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="3" class="row1">
				<tr> 
				  <td> 
					<div id="search" style="display:none"><b> 
					  {translate module=host_tld}
					  searching 
					  {/translate}
					  </b></div>
					<div id="unavailable" style="display:none"><b> 
					  {translate module=host_tld}
					  domain_unavail 
					  {/translate}
					  </b></div>
					<div id="available" style="display:none"> 
					  <b> 
					  {translate module=host_tld}
					  domain_avail_s 
					  {/translate}
					  <br>
					  <br>
					  <a href="javascript:cartAdd('register')"><img src="themes/{$THEME_NAME}/images/icons/cart_16.gif" border="0"> 
					  {translate module=host_tld}
					  register_cart 
					  {/translate}
					  </a></b>
					</div>
					<div id="park_available" style="display:none">
					  <br><b>
					  <a href="javascript:cartAdd('park')"><img src="themes/{$THEME_NAME}/images/icons/cart_16.gif" border="0"> 
					  {translate module=host_tld}
					  park_cart 
					  {/translate}
					  </a></b>
					</div>
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
		</table>			
	  </form> 
	</td>
  </tr>	
 </table>
 
{if $VAR._page!='host_tld:iframe_register'}
<br>
<div class=select> 
  <p><a href="?_page=host_tld:search_transfer"><b> 
    {translate module=host_tld}
    domain_transfer
    {/translate}
    </b></a> </p>
  <p><a href="javascript:showSuggest();"><b>
    {translate module=host_tld}
    suggest_other
    {/translate}
    </b></a> </p>
  <p><a href="?_page=host_tld:search_mass"><b> 
    {translate module=host_tld}
    search_mass
    {/translate}
    </b></a> </p>
</div>
<script language="JavaScript"> 
{if $VAR.domain != ""} domainSearch('register'); {else} {literal} try{ document.getElementById('domain').focus(); } catch(e) {} {/literal} {/if}   
</script>
{/if}