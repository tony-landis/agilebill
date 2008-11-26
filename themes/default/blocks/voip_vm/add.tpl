

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="voip_vm_add" name="voip_vm_add" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_vm}title_add{/translate}
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
                    {html_select_account name="voip_vm_account_id" default=$VAR.voip_vm_account_id} 
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_context
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_context" value="{$VAR.voip_vm_context}" {if $voip_vm_context == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_mailbox
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_mailbox" value="{$VAR.voip_vm_mailbox}" {if $voip_vm_mailbox == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_password
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_password" value="{$VAR.voip_vm_password}" {if $voip_vm_password == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_fullname
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_fullname" value="{$VAR.voip_vm_fullname}" {if $voip_vm_fullname == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_email
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_email" value="{$VAR.voip_vm_email}" {if $voip_vm_email == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_pager
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_pager" value="{$VAR.voip_vm_pager}" {if $voip_vm_pager == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_options
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_options" value="{$VAR.voip_vm_options}" {if $voip_vm_options == true}class="form_field_error"{/if}>
                    </td>
                </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="voip_vm:view">
                      <input type="hidden" name="_page_current" value="voip_vm:add">
                      <input type="hidden" name="do[]" value="voip_vm:add">
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
