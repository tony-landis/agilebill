{ $block->display("core:top_clean") }

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}


<!-- Display the form to collect the input values -->
<form id="static_relation_add" name="static_relation_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=static_relation}
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
                    {translate module=static_relation}
                    field_static_var_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "static_relation_static_var_id", "static_var", "name", $VAR.static_relation_static_var_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_module_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "static_relation_module_id", "module", "name", $VAR.static_relation_module_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_default_value 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="static_relation_default_value" cols="40" rows="2" {if $static_relation_default_value == true}class="form_field_error"{/if}>{$VAR.static_relation_default_value}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="static_relation_description" cols="40" rows="2" {if $static_relation_description == true}class="form_field_error"{/if}>{$VAR.static_relation_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_required 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("static_relation_required",$VAR.static_relation_required, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="static_relation_sort_order" value="{$VAR.static_relation_sort_order}" {if $static_relation_sort_order == true}class="form_field_error"{/if} size="3">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="static_relation:view">
                    <input type="hidden" name="_page_current" value="static_relation:add">
                    <input type="hidden" name="do[]" value="static_relation:add">
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
