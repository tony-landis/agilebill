

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="staff_add" name="staff_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=staff}
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
                    {translate module=staff}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="staff_account_id" default=$VAR.staff_account_id}
                  </td>
                </tr>
				 
				
				
                <tr valign="top"> 
                  <td width="35%" height="19"> 
                    {translate module=staff}
                    field_nickname 
                    {/translate}
                  </td>
                  <td width="65%" height="19">
                    <input type="text" name="staff_nickname" value="{$VAR.staff_nickname}" {if $staff_nickname == true}class="form_field_error"{/if}>
                  </td>
                </tr>

			   { if $list->is_installed('ticket') }
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=staff}
                    field_department_avail 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->check("", "staff_department_avail", "ticket_department", "name", $VAR.staff_department_avail, "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=staff}
                    field_notify_new 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("staff_notify_new", $VAR.staff_notify_new, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=staff}
                    field_notify_change 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("staff_notify_change", $VAR.staff_notify_change, "form_menu") }
                  </td>
                </tr> 
                <tr valign="top"> 
                  <td width="35%">{translate module=staff}field_signature{/translate}</td>
                  <td width="65%">{html_textarea name=staff_signature default=$VAR.staff_signature} </td>
                </tr>
		 		{else}
					<input type="hidden" name="staff_department_avail" value="false">				
				{/if}
				
				
                <tr valign="top">
                  <td></td>
                  <td><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="staff:view">
                    <input type="hidden" name="_page_current" value="staff:add">
                    <input type="hidden" name="do[]" value="staff:add">
                    <input type="hidden" name="staff_date_orig" value="{$smarty.now}"></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
