

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="ticket_add" name="ticket_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=ticket}
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
                    {translate module=ticket}
                    field_priority 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="ticket_priority" >
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
                    { $list->menu("no", "ticket_department_id", "ticket_department", "name", $VAR.ticket_department_id, "form_menu") }
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
                    field_email 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="ticket_email" value="{$VAR.ticket_email}" {if $ticket_subject == true}class="form_field_error"{/if} size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_subject 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="ticket_subject" value="{$VAR.ticket_subject}" {if $ticket_subject == true}class="form_field_error"{/if} size="48">
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket}
                    field_body 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="ticket_body" cols="50" rows="10" {if $ticket_body == true}class="form_field_error"{/if}>{$VAR.ticket_body}</textarea>
                  </td>
                </tr>
                { $method->exe("ticket","static_var")}
                { if ($method->result == FALSE) }
                { $block->display("core:method_error") }
                {/if}
                {foreach from=$static_var item=record}
                <tr valign="top"> 
                  <td width="29%"> 
                    {$record.name}
                  </td>
                  <td width="71%"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="ticket:view">
                    <input type="hidden" name="_page_current" value="ticket:add">
                    <input type="hidden" name="do[]" value="ticket:add">
                    <input type="hidden" name="ticket_status" value="0">
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
