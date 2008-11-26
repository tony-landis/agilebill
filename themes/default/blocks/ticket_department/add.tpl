

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="ticket_department_add" name="ticket_department_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=ticket_department}
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
                    {translate module=ticket_department}
                    field_group_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($VAR.ticket_department_group_id, 'ticket_department_group_id', 'group', 'name', '', '10', 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket_department}
                    field_setup_email_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "ticket_department_setup_email_id", "setup_email", "name", $VAR.ticket_department_setup_email_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket_department}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="ticket_department_name" value="{$VAR.ticket_department_name}" {if $ticket_department_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket_department}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("ticket_department_status", $VAR.ticket_department_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket_department}
                    field_piping 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->bool("ticket_department_piping", $VAR.ticket_department_piping, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket_department}
                    field_piping_setup_email_id 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->menu("", "ticket_department_piping_setup_email_id", "setup_email", "name", $VAR.ticket_department_piping_setup_email_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=ticket_department}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%">
                    <textarea name="ticket_department_description" cols="40" rows="10" {if $ticket_department_description == true}class="form_field_error"{/if}>{$VAR.ticket_department_description}</textarea>
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%"></td>
                  <td width="65%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="ticket_department:view">
                    <input type="hidden" name="_page_current" value="ticket_department:add">
                    <input type="hidden" name="do[]" value="ticket_department:add">
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
