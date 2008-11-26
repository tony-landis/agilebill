

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="staff_department_add" name="staff_department_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=staff_department}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=staff_department}
                            field_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="staff_department_name" value="{$VAR.staff_department_name}" {if $staff_department_name == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=staff_department}
                            field_description
                        {/translate}</td>
                    <td width="65%">
                        <textarea name="staff_department_description" cols="40" rows="5" {if $staff_department_description == true}class="form_field_error"{/if}>{$VAR.staff_department_description}</textarea>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=staff_department}
                            field_default_staff_id
                        {/translate}</td>
                    <td width="65%">
                        { $list->menu("", "staff_department_default_staff_id", "staff", "nickname", $VAR.staff_department_default_staff_id, "form_menu") }
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=staff_department}
                            field_contact_display
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("staff_department_contact_display", $VAR.staff_department_contact_display, "form_menu") }
                    </td>
                  </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="staff_department:view">
                      <input type="hidden" name="_page_current" value="staff_department:add">
                      <input type="hidden" name="do[]" value="staff_department:add">
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
