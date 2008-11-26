{literal}
<script language="JavaScript">
var type='transfer';
function cartAdminAdd(type) {  
	document.location = '?_page=cart:admin_view&do[]=cart:admin_add&host_type='+type+'&domain_name='+domain+'&domain_tld='+tld+'&account_id={/literal}{$VAR.account_id}{literal}';
} 
</script>
<script src="themes/default/blocks/host_tld/ajax.js" type="text/javascript"></script>
{/literal}
<table width=100% border="0" cellspacing="1" cellpadding="0" class="table_background">
  <tr valign="middle" class="row2"> 
    <td width="96%">
       <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_heading" height="25">
         <tr valign="top"> 
           <td> 
		   {translate module=host_tld}
            domain_transfer
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
                  <td width="13%"> 
                    <input type="text" id="domainName" name="domainName"  maxlength="128" size="22">
                  </td>
                  <td width="5%"> 
                    <select id="domainTLD" name="domainTLD">
                      { if $list->smarty_array("host_tld", "name", "", "tld") }
                      {foreach from=$tld item=tld}
                      <option value="{$tld.name}"> 
                      {$tld.name}
                      </option>
                      {/foreach}
                      {/if}
                    </select>
                  </td>
                  <td width="20"><a href="javascript:domainSearch();"><img src="themes/{$THEME_NAME}/images/icons/srch_16.gif" border="0" width="16" height="16"></a><b></b></td>
                  <td width="68%" class="body"><a href="javascript:domainSearch();"><b><u>{translate}search{/translate}</u></b></a></td>
                  <td width="77%"> </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="3">
                <tr> 
                  <td class="row1"> 
                    <div id="search" style="display:none"><b> 
                      {translate module=host_tld}
                      transfer_searching 
                      {/translate}
                      </b></div>
                    <div id="unavailable" style="display:none"><b> 
                      {translate module=host_tld}
                      transfer_unavail 
                      {/translate}
                      </b></div>
                    <div id="available" style="display:none"> <b> 
                      {translate module=host_tld}
                      transfer_avail_s 
                      {/translate}</b><br><br>
                      <a href="javascript:cartAdminAdd('transfer')"> 
					  <img src="themes/{$THEME_NAME}/images/icons/cart_16.gif" border="0">
                      {translate module=host_tld}
                      transfer_cart 
                      {/translate}
                      </a> </div> 
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
{literal}<script language="JavaScript"> try{ document.getElementById('domain').focus(); } catch(e) {} </script>{/literal}