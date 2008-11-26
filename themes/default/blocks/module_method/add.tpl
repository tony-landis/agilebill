{ $block->display("core:top_clean") }

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="module_method_add" name="module_method_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=module_method}
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
                    {translate module=module_method}
                    field_name
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="module_method_name" value="{$VAR.module_method_name}" {if $module_method_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=module_method}
                    field_notes
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="module_method_notes" cols="40" rows="5" {if $module_method_notes == true}class="form_field_error"{/if}>{$VAR.module_method_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=module_method}
                    field_module_id 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->menu("", "module_method_module_id", "module", "name", $VAR.module_method_module_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=module_method}
                    field_menu_display 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("module_method_menu_display", $VAR.module_method_menu_display, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=module_method}
                    field_page 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="module_method_page" value="{$VAR.module_method_page}" {if $module_method_page == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="module_method:view">
                    <input type="hidden" name="_page_current" value="module_method:add">
                    <input type="hidden" name="do[]" value="module_method:add">
					<input type="hidden" name="_escape" value="1">
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
