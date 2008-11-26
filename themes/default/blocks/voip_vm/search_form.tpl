
{ $method->exe("voip_vm","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="voip_vm_search" method="post" action="">
  {$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_vm}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                        {html_select_account name="voip_vm_account_id" default=$VAR.voip_vm_account_id}</td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_context
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_context" value="{$VAR.voip_vm_context}" {if $voip_vm_context == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_mailbox
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_mailbox" value="{$VAR.voip_vm_mailbox}" {if $voip_vm_mailbox == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_fullname
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_fullname" value="{$VAR.voip_vm_fullname}" {if $voip_vm_fullname == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_email
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_email" value="{$VAR.voip_vm_email}" {if $voip_vm_email == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_pager
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_pager" value="{$VAR.voip_vm_pager}" {if $voip_vm_pager == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_options
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_options" value="{$VAR.voip_vm_options}" {if $voip_vm_options == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" name="limit" size="5" value="{$voip_vm_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$voip_vm item=record}
                            <option value="{$record.field}">{$record.translate}</option>
        		          {/foreach}
                      </select>
                    </td>
                  </tr>

                  <tr class="row1" valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="core:search">
                      <input type="hidden" name="_escape" value="Y">
                      <input type="hidden" name="module" value="voip_vm">
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
{ $block->display("core:saved_searches") }
{ $block->display("core:recent_searches") }
{/if}
