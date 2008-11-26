<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}
 
{ $method->exe("ticket","is_user_auth")} 
	{ if ($method->result == FALSE) } { $block->display("core:method_error") } {/if}
	
{if $display == true}
{if $VAR.ticket_department_id == ''}
{translate module=ticket}
user_department_required 
{/translate}
{else}
<form name="form1" method="post" action="">
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
                  <td width="29%"> 
                    {translate module=ticket}
                    field_priority 
                    {/translate}
                  </td>
                  <td width="71%"> 
                    <select name="ticket_priority">
                      <option value="0">
                      {translate module=ticket}
                      priority_standard
                      {/translate}
                      </option>
                      <option value="1">
                      {translate module=ticket}
                      priority_medium
                      {/translate}
                      </option>
                      <option value="2">
                      {translate module=ticket}
                      priority_high
                      {/translate}
                      </option>
                      <option value="3">
                      {translate module=ticket}
                      priority_emergency
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
				
				{if $smarty.const.SESS_LOGGED == false}
                <tr valign="top"> 
                  <td width="29%"> 
                    {translate module=ticket}
                    field_email 
                    {/translate}
                  </td>
                  <td width="71%"> 
                    <input type="text" name="ticket_email" value="{$VAR.ticket_email}" {if $ticket_email == true}class="form_field_error"{/if} size="43" maxlength="255">
                  </td>
                </tr>
				{/if}
				
								
                <tr valign="top"> 
                  <td width="29%"> 
                    {translate module=ticket}
                    field_subject 
                    {/translate}
                  </td>
                  <td width="71%"> 
                    <input type="text" name="ticket_subject" value="{$VAR.ticket_subject}" {if $ticket_subject == true}class="form_field_error"{/if} size="43" maxlength="255">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="29%"> 
                    {translate module=ticket}
                    field_body 
                    {/translate}
                  </td>
                  <td width="71%"> 
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
                  <td width="29%"></td>
                  <td width="71%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="ticket:ticket">
                    <input type="hidden" name="_page_current" value="ticket:user_add">
                    <input type="hidden" name="do[]" value="ticket:user_add">
                    <input type="hidden" name="ticket_department_id" value="{$VAR.ticket_department_id}">
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
{else}
<p> 
  {translate module=ticket}
  user_not_auth_add 
  {/translate}
<p> 
{ $block->display("ticket:auth")}
{/if}

