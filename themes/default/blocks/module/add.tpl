
<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}


<!-- Display the form to collect the input values -->
<form id="module_add" name="module_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=module}
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
                    {translate module=module}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input id="module_name" type="text" value="{$VAR.module_name}" name="module_name" {if $module_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=module}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="module_notes" cols="40" rows="5" {if $module_notes == true}class="form_field_error"{/if}>{$VAR.module_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.module_date_orig == ''}
                    { $list->calender_add("module_date_orig", 'now', "form_field") }
                    {else}
                    { $list->calender_add("module_date_orig", $VAR.module_date_orig, "form_field") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.module_date_last == ''}
                    { $list->calender_add("module_date_last", 'now', "form_field") }
                    {else}
                    { $list->calender_add("module_date_last", $VAR.module_date_last, "form_field") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=module}
                    field_parent_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "module_parent_id", "module", "name", $VAR.module, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=module}
                    field_menu_display 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("module_menu_display", $VAR.module_menu_display, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=module}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("module_status", $VAR.module_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="module:view">
                    <input type="hidden" name="_page_current" value="module:add">
                    <input type="hidden" name="do[]" value="module:add">
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
