
    <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
      <tr>
        <td>
          <table width=100% border="0" cellspacing="1" cellpadding="0" align="center">
            <tr>
              <td class="table_heading">
                <center>
                  {translate module=task}
                  menu
                  {/translate}
                </center>
              </td>
            </tr>
            <tr>
              <td class="row1">
                <table width="100%" border="0" cellpadding="5" class="row1">
                  <tr>
                    <td>{translate module=task}help_file{/translate}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
                 {translate module=voip_rate}
                            field_date_start
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("voip_rate_date_start", $VAR.voip_rate_date_start, "form_field", "") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_rate}
                            field_date_expire
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("voip_rate_date_expire", $VAR.task_date_expire, "form_field", "") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=task}
                            field_date_last
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("task_date_last", $VAR.task_date_last, "form_field", "") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=task}
                            field_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="task_name" value="{$VAR.task_name}" {if $task_name == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=task}
                            field_description
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="task_description" value="{$VAR.task_description}" {if $task_description == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=task}
                            field_log
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("task_log", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=task}
                            field_type
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("task_type", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=task}
                            field_command
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="task_command" value="{$VAR.task_command}" {if $task_command == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=task}
                            field_int_hour
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="task_int_hour" value="{$VAR.task_int_hour}" {if $task_int_hour == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=task}
                            field_int_month_day
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="task_int_month_day" value="{$VAR.task_int_month_day}" {if $task_int_month_day == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=task}
                            field_int_week_day
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="task_int_week_day" value="{$VAR.task_int_week_day}" {if $task_int_week_day == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text"  name="limit" size="5" value="{$task_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select  name="order_by">
        		          {foreach from=$task item=record}
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
                      <input type="hidden" name="module" value="voip_rate">
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
