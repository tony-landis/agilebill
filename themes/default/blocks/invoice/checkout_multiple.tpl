{$method->exe("invoice","checkout_multiple_preview")}
{if $total}
     <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="body">
        <tr> 
          <td valign="top" align="center" width="35%"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td> 
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background"> 
                      <tr> 
                        <td> 
                          <table width="100%" border="0" cellspacing="1" cellpadding="0">
                            <tr valign="top"> 
                              <td width="65%" class="row1"> 
                                <table width="100%" border="0" cellspacing="5" cellpadding="1" class="row1">
                                  <tr> 
                                    <td width="74%"><div align="center">{translate module=invoice total=$total}due_invoices_pay{/translate}</div></td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr> 
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
</table>
 <br><br>
{/if}

<!-- Display checkout optoins --> 
{ $block->display("invoice:checkoutoptions") }
<br>
<br>  
