
{ $method->exe("ticket","is_user_auth")} { if ($method->result == FALSE) } { $block->display("core:method_error") } {/if}
{if $display == true} 
<form name="form1" method="post" action="">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=ticket}
                menu_add 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="1" cellpadding="0" align="center">
                <tr> 
                  <td class="row1"> 
                    <table width="100%" border="0" cellpadding="3" class=row1>
                      <tr> 
                        <td width="60%"> 
                          {translate module=ticket}
                          user_select_department 
                          {/translate}
                        </td>
                      </tr>
                      <tr> 
                        <td width="60%"> 
                          <p> 
                            {foreach from=$results item=record}
                          <table width="100%" border="0" cellpadding="1"  class="row1">
                            <tr> 
                              <td width="8%""> 
                                <input type="radio" name="ticket_department_id" value="{$record.id}" checked>
                              </td>
                              <td width="92%"> <b> 
                                {$record.name}
                                </b> </td>
                            </tr>
                            <tr class="row1"> 
                              <td width="8%">&nbsp;</td>
                              <td width="92%"> 
                                {$record.description}
                                <br>
                              </td>
                            </tr>
                            {/foreach}
                            <tr class="row1"> 
                              <td width="8%"></td>
                              <td width="92%"> 
                                <div align="right"> 
                                  <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                                  <input type="hidden" name="_page" value="ticket:user_add">						 							  
                                </div>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
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
<p>{else}</p>
<p> 
  {translate module=ticket}
  user_not_auth_add 
  {/translate}
</p>
<p> 
  {/if}
  { $method->exe("ticket","user_list")}
  { if ($method->result == FALSE) }
  { $block->display("core:method_error") }
  {/if}
  
	{$method->exe("ticket","is_key_match")}
	{if $ticket_key == true}   
  {if $ticket_results != false}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading"> 
            <div align="center">
              {translate module=ticket}
              menu_view 
              {/translate}
            </div>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1">
            <table width="100%" border="0" cellpadding="3" class=row1>
              <tr> 
                <td width="60%"> 
                  {translate module=ticket}
                  user_ticket_list 
                  {/translate}
                </td>
              </tr>
              <tr> 
                <td width="60%"> 
                  <p> 
                  <table width="100%" border="0" cellpadding="1" class="row2">
                    <tr> 
                      <td width="61%"> <u> <b> 
                        {translate}
                        field_subject 
                        {/translate}
                        </b></u></td>
                      <td width="29%"> <u> <b>
                        {translate}
                        field_date_last
                        {/translate}
                        </b></u></td>
                      <td width="10%"> <u> </u><u><b> 
                        {translate}
                        field_status 
                        {/translate}
                        </b></u></td>
                    </tr>
                    {foreach from=$ticket_results item=record}
                  </table>
                  <table width="100%" border="0" cellpadding="1" class="row2">
                    <tr> 
                      <td width="61%"> <a href="?_page=ticket:user_view&id={$record.id}{if $VAR.key != "" && $VAR.email != ""}&email={$VAR.email}&key={$VAR.key}{/if}"><font color="#000000"> 
                        <b> 
                        {$record.subject|truncate:40}
                        </b></font></a> </td>
                      <td width="29%"> 
                        { $list->date_time($record.date_orig)}
                      </td>
                      <td width="10%"> 
                        { if $record.status == '0' }
                        {translate module=ticket}
                        status_open 
                        {/translate}
                        { elseif $record.status == '1' }
                        {translate module=ticket}
                        status_hold 
                        {/translate}
                        { elseif $record.status == '2' }
                        {translate module=ticket}
                        status_close 
                        {/translate}
                        { elseif $record.status == '3' }
                        {translate module=ticket}
                        status_hold
                        {/translate}						
                        { /if }
                      </td>
                    </tr>
                    {/foreach}
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
{else}
{translate module=ticket}
user_no_existing_ticket 
{/translate}
{/if}

{else} 
{ $block->display("ticket:auth")}
{/if}
