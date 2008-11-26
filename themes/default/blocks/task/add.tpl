

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="task_add" name="task_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=task}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("task_date_start", $VAR.task_date_start, "form_field") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("task_date_expire", $VAR.task_date_expire, "form_field") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="task_name" value="{$VAR.task_name}" {if $task_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="task_description" cols="40" rows="5" {if $task_description == true}class="form_field_error"{/if}>{$VAR.task_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="task_type" >
                      <option value="0" {if $VAR.task_type == "0"}selected{/if}> 
                      {translate module="task"}
                      type_method
                      {/translate}
                      </option>
                      <option value="0" {if $VAR.task_type == "1"}selected{/if}> 
                      {translate module="task"}
                      type_system
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_command 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="task_command" value="{$VAR.task_command}" {if $task_command == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_int_min 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { if $VAR.task_int_min == "" }
                    <input type="text" name="task_int_min" value="*" {if $task_int_min == true}class="form_field_error"{/if}>
                    {else}
                    <input type="text" name="task_int_min" value="{$VAR.task_int_min}" {if $task_int_min == true}class="form_field_error"{/if}>
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_int_hour 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { if $VAR.task_int_hour == "" }
                    <input type="text" name="task_int_hour" value="*" {if $task_int_hour == true}class="form_field_error"{/if}>
                    {else}
                    <input type="text" name="task_int_hour" value="{$VAR.task_int_hour}" {if $task_int_hour == true}class="form_field_error"{/if}>
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_int_month_day 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { if $VAR.task_int_month_day == "" }
                    <input type="text" name="task_int_month_day" value="*" {if $task_int_month_day == true}class="form_field_error"{/if}>
                    {else}
                    <input type="text" name="task_int_month_day" value="{$VAR.task_int_month_day}" {if $task_int_month_day == true}class="form_field_error"{/if}>
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_int_month 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { if $VAR.task_int_month == "" }
                    <input type="text" name="task_int_month" value="*" {if $task_int_month == true}class="form_field_error"{/if}>
                    {else}
                    <input type="text" name="task_int_month" value="{$VAR.task_int_month}" {if $task_int_month == true}class="form_field_error"{/if}>
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=task}
                    field_int_week_day 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { if $VAR.task_int_week_day == "" }
                    <input type="text" name="task_int_week_day" value="*" {if $task_int_week_day == true}class="form_field_error"{/if}>
                    {else}
                    <input type="text" name="task_int_week_day" value="{$VAR.task_int_week_day}" {if $task_int_week_day == true}class="form_field_error"{/if}>
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="task:view">
                    <input type="hidden" name="_page_current" value="task:add">
                    <input type="hidden" name="do[]" value="task:add">
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
