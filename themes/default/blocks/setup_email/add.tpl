

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="setup_email_add" name="setup_email_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=setup_email}
                title_add
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_name" value="{$VAR.setup_email_name}" {if $setup_email_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
				
				{if $list->is_installed("email_queue")}
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_queue 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("setup_email_queue", $VAR.setup_email_queue, "") }
                  </td>
                </tr>
				{/if}
								
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_notes" value="{$VAR.setup_email_notes}" {if $setup_email_notes == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_from_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_from_name" value="{$VAR.setup_email_from_name}" {if $setup_email_from_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_from_email 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_from_email" value="{$VAR.setup_email_from_email}" {if $setup_email_from_email == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_cc_list 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="setup_email_cc_list" cols="40" rows="5" {if $setup_email_cc_list == true}class="form_field_error"{/if}>{$VAR.setup_email_cc_list}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_bcc_list 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="setup_email_bcc_list" cols="40" rows="5" {if $setup_email_bcc_list == true}class="form_field_error"{/if}>{$VAR.setup_email_bcc_list}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("setup_email_type", $VAR.setup_email_type, "form_menu") }
                    <br>
                    {translate module=setup_email}
                    smtp_help 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp; </td>
                  <td width="65%">&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=setup_email}
                    field_server 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="setup_email_server" value="{$VAR.setup_email_server}" {if $setup_email_server == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=setup_email}
                    field_username 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="setup_email_username" value="{$VAR.setup_email_username}" {if $setup_email_username == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=setup_email}
                    field_password 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="setup_email_password" value="{$VAR.setup_email_password}" {if $setup_email_password == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> </td>
                  <td width="65%">&nbsp;</td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=setup_email}
                    field_piping 
                    {/translate}
                  </td>
                  <td width="65%">
                    <select name="setup_email_piping">
                      <option value="0" {if $VAR.setup_email_piping == "0"}selected{/if}></option>
                      <option value="1" {if $VAR.setup_email_piping == "1"}selected{/if}>POP3</option>
                      <option value="2" {if $VAR.setup_email_piping == "2"}selected{/if}>IMAP</option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=setup_email}
                    field_piping_host 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="setup_email_piping_host" value="{$VAR.setup_email_piping_host}" {if $setup_email_server == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=setup_email}
                    field_piping_username 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_piping_username" value="{$VAR.setup_email_piping_username}" {if $setup_email_server == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=setup_email}
                    field_piping_password 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_piping_password" value="{$VAR.setup_email_piping_password}" {if $setup_email_server == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=setup_email}
                    field_piping_action 
                    {/translate}
                  </td>
                  <td width="65%">
                    <select name="setup_email_piping_action">
                      <option value="0" {if $VAR.setup_email_piping_action == "0"}selected{/if}>{translate module=setup_email}piping_action_leave{/translate}</option>
                      <option value="1" {if $VAR.setup_email_piping_action == "1"}selected{/if}>{translate module=setup_email}piping_action_delete{/translate}</option> 
                    </select>
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%"></td>
                  <td width="65%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="setup_email:view">
                    <input type="hidden" name="_page_current" value="setup_email:add">
                    <input type="hidden" name="do[]" value="setup_email:add">
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
