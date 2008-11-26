{ $block->display("core:top_clean") }
 
<!-- Display each record -->
<form name="reconcile" method="post" action=""> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                <b> 
                {translate module=invoice}
                title_refund 
                {/translate}
                {$VAR.id}
                </b> 
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=invoice}
                    ref_amt 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input id="focus" type="text" name="amount" value="{$VAR.amount}"  size="5">
                    { $list->currency_iso('') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=invoice}
                    memo 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="memo"  cols="60" rows="2">{$VAR.memo}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    <div align="left">
                      <input type="hidden" name="_page" value="invoice:refund">
                      <input type="hidden" name="id" value="{$VAR.id}">
                      <input type="hidden" name="do[]" value="invoice:refund">
                      <input type="hidden" name="id" value="{$VAR.id}">
                      <input type="hidden" name="_escape" value="1">
                      <input type="hidden" name="redirect" value="?_page=invoice:view&id={$VAR.id}">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    </div>
                  </td>
                  <td width="65%"> 
                    <div align="right"> </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table> 
  </form>
  
  <script language="JavaScript">
  	document.getElementById('focus').focus();
  </script>
