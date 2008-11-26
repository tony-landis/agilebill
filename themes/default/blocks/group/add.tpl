
<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="group_add" name="group_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=group}
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
                    {translate module=group}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="group_name" value="{$VAR.group_name}" {if $group_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="group_notes" cols="40" rows="5" {if $group_notes == true}class="form_field_error"{/if}>{$VAR.group_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.group_date_start == ""}
                    {$list->calender_add("group_date_start", '', "form_field")}
                    {else}
                    {$list->calender_add("group_date_start", $VAR.group_date_start, "form_field")}
                    {/if}
                    {$VAR.group_date_start}</td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.group_date_expire == ""}
                    {$list->calender_add("group_date_expire", '', "form_field")}
                    {else}
                    {$list->calender_add("group_date_expire", $VAR.group_date_expire, "form_field")}
                    {/if}
                    {$VAR.group_date_expire} </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->bool("group_status", $VAR.group_status, "form_menu")}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_pricing 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->bool("group_pricing", $VAR.group_pricing, "form_menu")}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_parent_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "group_parent_id", "group", "name", $VAR.group_parent_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="group:view">
                    <input type="hidden" name="_page_current" value="group:add">
                    <input type="hidden" name="do[]" value="group:add">
                    <input type="hidden" name="group_date_orig" value="{$smarty.now}">
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
