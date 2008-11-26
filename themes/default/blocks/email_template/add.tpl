

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="email_template_add" name="email_template_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=email_template}
                title_add
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=email_template}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="email_template_name" value="{$VAR.email_template_name}" {if $email_template_name == true}class="form_field_error"{/if} size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=email_template}
                    field_setup_email_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu("", "email_template_setup_email_id", "setup_email", "name", $VAR.email_template_setup_email_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=email_template}
                    field_priority 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("email_template_priority", $VAR.email_template_priority, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%">&nbsp; </td>
                  <td width="50%">&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=email_template}
                    field_sql_1 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {translate module=email_template}
                    field_sql_2 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <textarea name="email_template_sql_1" cols="40" rows="2" {if $email_template_sql_1 == true}class="form_field_error"{/if}>{$VAR.email_template_sql_1}</textarea>
                  </td>
                  <td width="50%"> 
                    <textarea name="email_template_sql_2" cols="40" rows="2" {if $email_template_sql_2 == true}class="form_field_error"{/if}>{$VAR.email_template_sql_2}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=email_template}
                    field_sql_3 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {translate module=email_template}
                    field_notes 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <textarea name="email_template_sql_3" cols="40" rows="2" {if $email_template_sql_3 == true}class="form_field_error"{/if}>{$VAR.email_template_sql_3}</textarea>
                  </td>
                  <td width="50%"> 
                    <textarea name="email_template_notes" cols="40" rows="2" {if $email_template_notes == true}class="form_field_error"{/if}>{$VAR.email_template_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="50%"> 
                    <input type="hidden" name="_page" value="email_template_translate:add">
                    <input type="hidden" name="_page_current" value="email_template:add">
                    <input type="hidden" name="do[]" value="email_template:add">
                    <input type="hidden" name="email_template_status" value="1">
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
