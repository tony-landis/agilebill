{ $block->display("core:top_clean") }
{literal}
<script language="JavaScript">
var type='transfer';
</script>
<script src="themes/default/blocks/host_tld/ajax.js" type="text/javascript"></script>
{/literal}
<form name="domain" method="post" action="javascript:void(0);" onsubmit="domainSearch();"> 
  <table width="100%" border="0" cellspacing="0" cellpadding="3" class="body">
    <tr> 
      <td width="95%" class="body"> <b> <br>
        {translate module=host_tld}
        register_instructions 
        {/translate}
        </b></td>
    </tr>
    <tr> 
      <td width="95%">
		<table width="100%" border="0" cellspacing="0" cellpadding="3" class="body">
          <tr> 
            <td width="14%"> 
              <input type="text" id="domainName" name="domainName" maxlength="128" size="22" >
            </td>
            <td width="6%"> 
              <select id="domainTLD" name="domainTLD" >
                { if $list->smarty_array("host_tld", "name", "", "tld") }
                {foreach from=$tld item=tld}
                <option value="{$tld.name}"> 
                {$tld.name}
                </option>
                {/foreach}
                {/if}
              </select>
            </td>
            <td width="12%"><a href="javascript:domainSearch();"><b><u>{translate}search{/translate}</u></b></a><b></b></td>
            <td width="68%"> </td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="3">
          <tr> 
            <td class="body">  
              <DIV id="search" style="display:none"><b> 
                {translate module=host_tld}
                transfer_ searching 
                {/translate}
                </b></DIV>
              <DIV id="unavailable" style="display:none"><b> 
                {translate module=host_tld}
                transfer_unavail 
                {/translate}
                </b></DIV>
              <DIV id="available" style="display:none"> <b> 
                {translate module=host_tld}
                transfer_avail 
                {/translate}
                </b> </DIV>
			  <DIV id="none" style="display:block">&nbsp;</DIV>
            </td>
          </tr>
          <tr> 
            <td width="13%"> 
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
 </form> 
{literal}<script language="JavaScript"> try{ document.getElementById('domain').focus(); } catch(e) {} </script>{/literal}
</body>
</html>