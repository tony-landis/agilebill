
<table width="200" border="0" cellspacing="0" cellpadding="0" class="body">
  <tr> 
    <td valign="top" align="center" width="35%"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
              <form name="form1" method="post" action="">
                <tr> 
                  <td> 
                    <table width="100%" border="0" cellspacing="1" cellpadding="0">
                      <tr valign="top"> 
                        <td width="65%" class="table_heading"> 
                          <div align="center"> 
                            {translate module=invoice}
                            menu 
                            {/translate}
                          </div>
                        </td>
                      </tr>
                      <tr valign="top"> 
                        <td width="65%" class="row1"> 
                          <table width="100%" border="0" cellspacing="5" cellpadding="1" class="row1">
                            <tr> 
                              <td width="74%"> 
                                {translate module=invoice}
                                {/translate}
                                Due Invoices</td>
                              <td width="26%" align="right"> <a href="?_page=core:user_search&module=invoice&_next_page=user_search_show&invoice_billing_status=0"> 
                                {translate module=invoice}
                                menu_view 
                                {/translate}
                                </a> </td>
                            </tr>
                            <tr> 
                              <td width="74%"> 
                                {translate module=invoice}
                                {/translate}
                                Paid Invoices</td>
                              <td width="26%" align="right"> <a href="?_page=core:user_search&module=invoice&_next_page=user_search_show&invoice_billing_status=1"> 
                                {translate module=invoice}
                                menu_view 
                                {/translate}
                                </a></td>
                            </tr>
                            <tr>
                              <td width="74%"> 
                                {translate module=invoice}
                                {/translate}
                                All Invoices</td>
                              <td width="26%" align="right"> <a href="?_page=core:user_search&module=invoice&_next_page=user_search_show"> 
                                {translate module=invoice}
                                menu_view 
                                {/translate}
                                </a></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </form>
            </table>
          </td>
        </tr>
      </table>
      
    </td>
     
  </tr>
</table>