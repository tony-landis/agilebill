{ $block->display("core:top_clean") }
<form name="domain" method="post" action="{$SSL_URL}"> 
  <table width="100%" border="0" cellspacing="0" cellpadding="3" class="body">
    <tr> 
      <td width="95%" class="body"> <b> <br>
        {translate module=host_tld}
        ns_transfer_instructions 
        {/translate}
        </b></td>
      <td width="5%">&nbsp;</td>
    </tr>
    <tr> 
      <td width="95%"> 
        <p>
          <input type="text" id="domain" name="domain"  maxlength="128" size="22" onchange="parent.document.getElementById('domain_name').value = this.value;">
          . 
          <input type="text" id="tld" name="tld" size="5" maxlength="7"    onchange="parent.document.getElementById('domain_tld').value = this.value;">
        </p> <br>
        </td>
      <td width="5%">&nbsp; </td>
    </tr>
  </table>
  </form> 
{literal}<script language="JavaScript"> try{ document.getElementById('domain').focus(); } catch(e) {} </script>{/literal}
</body>
</html>
