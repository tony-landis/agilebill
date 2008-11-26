
{ $method->exe("ticket","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="ticket_search" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=ticket}
                title_search
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_subject 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="ticket_subject" value="{$VAR.ticket_subject}" {if $ticket_subject == true}class="form_field_error"{/if}>
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_body 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="ticket_body" value="{$VAR.ticket_body}" {if $ticket_body == true}class="form_field_error"{/if}>
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_email 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="ticket_email" value="{$VAR.ticket_email}" {if $ticket_email == true}class="form_field_error"{/if}>
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="ticket_status" >
                      <option value=""></option>
                      <option value="0" {if $VAR.ticket_priority == "0"}selected{/if}> 
                      {translate module=ticket}
                      status_open 
                      {/translate}
                      </option>
                      <option value="1" {if $VAR.ticket_priority == "1"}selected{/if}> 
                      {translate module=ticket}
                      status_hold 
                      {/translate}
                      </option>
                      <option value="2" {if $VAR.ticket_priority == "2"}selected{/if}> 
                      {translate module=ticket}
                      status_close 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_priority 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="ticket_priority" >
                      <option value=""></option>
                      <option value="0" {if $VAR.ticket_priority == "0"}selected{/if}> 
                      {translate module=ticket}
                      priority_standard 
                      {/translate}
                      </option>
                      <option value="1" {if $VAR.ticket_priority == "1"}selected{/if}> 
                      {translate module=ticket}
                      priority_medium 
                      {/translate}
                      </option>
                      <option value="2" {if $VAR.ticket_priority == "2"}selected{/if}> 
                      {translate module=ticket}
                      priority_high 
                      {/translate}
                      </option>
                      <option value="3" {if $VAR.ticket_priority == "3"}selected{/if}> 
                      {translate module=ticket}
                      priority_emergency 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_department_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "ticket_department_id", "ticket_department", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_staff_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "ticket_staff_id", "staff", "nickname", "all", "form_menu") }
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="ticket_account_id" default=$VAR.ticket_account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("ticket_date_orig", $VAR.ticket_date_orig, "form_field", "") }
                  </td>
                </tr> 
				
				{ $method->exe("ticket","static_var")} 
                {foreach from=$static_var item=record}
                <tr valign="top"> 
                  <td width="35%"> 
                    {$record.name}
                  </td>
                  <td width="65%"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}
								
                <!-- Define the results per page -->
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate}
                    search_results_per 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text"  name="limit" size="5" value="{$ticket_limit}">
                  </td>
                </tr>
                <!-- Define the order by field per page -->
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate}
                    search_order_by 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select  name="order_by">
                      {foreach from=$ticket item=record}
                      <option value="{$record.field}"{if $record.field == $ticket_order_by} selected{/if}> 
                      {$record.translate}
                      </option>
                      {/foreach}
                    </select>
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="core:search">
					<input type="hidden" name="_next_page_one" value="view">
                    <input type="hidden" name="_escape" value="1">
                    <input type="hidden" name="module" value="ticket">
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
{/if}
