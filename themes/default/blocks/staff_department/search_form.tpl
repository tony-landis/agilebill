
{ $method->exe("staff_department","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="staff_department_search" method="post" action="">
  
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=staff_department}title_search{/translate}
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
                        <input type="text" name="staff_department_name" value="{$VAR.staff_department_name}" {if $staff_department_name == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=staff_department}
                            field_description
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="staff_department_description" value="{$VAR.staff_department_description}" {if $staff_department_description == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=staff_department}
                            field_default_staff_id
                        {/translate}</td>
                    <td width="65%">
                        { $list->menu("", "staff_department_default_staff_id", "staff", "name", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=staff_department}
                            field_contact_display
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("staff_department_contact_display", "all", "form_menu") }
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text"  name="limit" size="5" value="{$staff_department_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select  name="order_by">
        		          {foreach from=$staff_department item=record}
                            <option value="{$record.field}">{$record.translate}</option>
        		          {/foreach}
                      </select>
                    </td>
                  </tr>

                  <tr class="row1" valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="core:search">
                      <input type="hidden" name="_escape" value="Y">
                      <input type="hidden" name="module" value="staff_department">
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
{ $block->display("core:saved_searches") }
{ $block->display("core:recent_searches") }
{/if}
