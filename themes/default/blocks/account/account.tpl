{if $smarty.const.SESS_LOGGED != true }
	{ $block->display("account:login") }
{else}

{if $list->is_installed('radius') }    
  {$block->display('radius:user')} 
{/if}  
 
{$method->exe("invoice","has_unpaid")}
  {if $has_unpaid}
     <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="body">
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
                                    <td width="74%"> <div align="center">{translate module=invoice total=$has_unpaid}due_invoices_notice{/translate}</div></td>
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
     <p>&nbsp;</p>
      {/if}

    
     <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="body">
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
                                    <td width="74%">
									<div align="center">
									
									<a href="{$SSL_URL}?_page=account:view">{translate module=account}account_link{/translate}</a><br>
									<a href="{$SSL_URL}?_page=core:user_search&module=account_billing&_next_page=user_search_show&_next_page_one=user_view">{translate module=account}billing_link{/translate}</a><br>
									<a href="{$SSL_URL}?_page=core:user_search&module=discount&_next_page=user_search_show">{translate module=account}discount_link{/translate}</a> <br>
									{if $smarty.const.SHOW_CONTACT_LINK}
										<a href="?_page=staff:staff">{translate}contact{/translate}</a><br>
									{/if}
									{if $smarty.const.SHOW_NEWSLETTER_LINK}
										<a href="?_page=newsletter:newsletter">{translate module=account}newsletter_link{/translate}</a> <br>
									{/if}
									{if $list->is_installed('affiliate') && $smarty.const.SHOW_AFFILIATE_LINK == 1 }
										<a href="{$SSL_URL}?_page=affiliate:affiliate">{translate module=account}affiliate_link{/translate}</a> 
									{/if}		
																		
									</div>
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
          </td>
        </tr>
</table>
	

<p>&nbsp;</p>

<table width="600" border="0" cellpadding="0" class="body" cellspacing="0" align="center">
  <tr> 
    <td align="center" valign="top"> 
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
                                    <td width="26%" align="right"> <a href="{$SSL_URL}?_page=core:user_search&module=invoice&_next_page=user_search_show&invoice_billing_status=0&_next_page_one=user_view"> 
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
                                    <td width="26%" align="right"> <a href="{$SSL_URL}?_page=core:user_search&module=invoice&_next_page=user_search_show&invoice_billing_status=1&_next_page_one=user_view"> 
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
                                    <td width="26%" align="right"> <a href="{$SSL_URL}?_page=core:user_search&module=invoice&_next_page=user_search_show&_next_page_one=user_view"> 
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
    </td>
    <td align="center" valign="top"> 
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
                                  {translate module=service}
                                  menu 
                                  {/translate}
                                </div>
                              </td>
                            </tr>
                            <tr valign="top"> 
                              <td width="65%" class="row1"> 
                                <table width="100%" border="0" cellspacing="5" cellpadding="1" class="row1">
                                  <tr> 
                                    <td width="74%"> Active Services</td>
                                    <td width="26%" align="right"> <a href="{$SSL_URL}?_page=core:user_search&module=service&_next_page=user_search_show&service_active=1&_next_page_one=user_view"> 
                                      {translate module=invoice}
                                      menu_view 
                                      {/translate}
                                      </a> </td>
                                  </tr>
                                  <tr> 
                                    <td width="74%"> Inactive Services</td>
                                    <td width="26%" align="right"> <a href="{$SSL_URL}?_page=core:user_search&module=service&_next_page=user_search_show&service_active=0&_next_page_one=user_view"> 
                                      {translate module=invoice}
                                      menu_view 
                                      {/translate}
                                      </a></td>
                                  </tr>
                                  <tr> 
                                    <td width="74%"> All Services</td>
                                    <td width="26%" align="right"> <a href="{$SSL_URL}?_page=core:user_search&module=service&_next_page=user_search_show&_next_page_one=user_view"> 
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
    </td>
  </tr> 
</table>

<br>
<br>

<table width="600" align="center"> 
 <tr align="center">
 
  {if $list->is_installed('ticket') && $smarty.const.SHOW_TICKET_LINK == 1 } 
    <td valign="top">  
       {$block->display('ticket:user')}
	</td>  
  {/if} 
  
  {if $list->is_installed('file') }   
    <td valign="top"> 
	  {$block->display('file:file')}
	</td>    
  {/if}  
  
  {if $list->is_installed('htaccess') } 
    <td valign="top"> 
      {$block->display('htaccess:htaccess')} 
	</td>  
  {/if}
</table>

<!-- custom tracking code -->
{ $method->exe("invoice","custom_tracking") }

{/if}