 		{$list->unserial($invoice.checkout_plugin_data, "plugin_data")}
          <tr valign="top">
            <td class="row1">
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="middle">
                    <td width="33%"><b>
                      {translate module=account_billing}
                      title_view
                      {/translate}
                      </b></td>
                    <td width="66%">
                      { $list->menu_cc_admin("invoice_account_billing_id", $invoice.account_id, $invoice.account_billing_id, "form_menu", $cc_user) }
                    </td>
                  </tr>
                </table>
            </td>
          </tr>