{ $method->exe("ticket","user_view") } 
{ if ($method->result == FALSE) } { $block->display("core:method_error") } {/if}

<!-- Display the field validation -->
{if $ticket != false}

{ if $ticket.status == '3' && $smarty.const.SESS_LOGGED == false }
<form name="form1" method="post" action="">
  {translate module=ticket}
  user_pending_verify 
  {/translate}
  <br>
  <br>
  <table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr valign="top"> 
      <td width="65%" class="table_heading"> 
        <div align="center"> 
          {translate}
          account_login 
          {/translate}
        </div>
      </td>
    </tr>
    <tr valign="top"> 
      <td width="65%" class="row1"> 
        <table width="100%" border="0" cellspacing="5" cellpadding="1" class="row1">
          <tr> 
            <td width="25%"> 
              {translate}
              username 
              {/translate}
            </td>
            <td width="75%"> 
              <input type="text" name="_username" value="{$VAR._username}" size="12">
            </td>
          </tr>
          <tr> 
            <td width="25%"> 
              {translate}
              password 
              {/translate}
            </td>
            <td width="75%"> 
              <input type="password" name="_password" size="12">
            </td>
          </tr>
          <tr> 
            <td width="25%"> 
              <input type="hidden" name="_login" value="Y">
              <input type="hidden" name="_page" value="{$VAR._page}">
              <input type="hidden" name="id" value="{$VAR.id}"> 
            </td>
            <td width="75%"> 
              <input type="submit" name="submit" value="{translate}login{/translate}" class="form_button">
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
{elseif $ticket.status == '3' && $smarty.const.SESS_LOGGED == true}
{ $method->exe("ticket","pending_verify") } 
{$pending_status}
{else}
<!-- Display each record -->
<form name="form1" method="post" action="">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=ticket}
                title_view 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="4" cellpadding="1" class="row1">
                <tr valign="top" class="row1"> 
                  <td width="20%"> 
                    {translate module=ticket}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="80%"> 
                    {$list->date_time($ticket.date_last)}
                  </td>
                </tr>
                <tr valign="top"  class="row1"> 
                  <td width="20%"> 
                    {translate module=ticket}
                    field_status 
                    {/translate}
                  </td>
                  <td width="80%" class="row1"> 
                    { if $ticket.status == '0' }
                    {translate module=ticket}
                    status_open 
                    {/translate}
                    { elseif $ticket.status == '1' }
                    {translate module=ticket}
                    status_hold 
                    {/translate}
                    { elseif $ticket.status == '2' }
                    {translate module=ticket}
                    status_close 
                    {/translate}
                    { elseif $ticket.status == '3' }
                    <b> <font color="#990000">
                    {translate module=ticket}
                    status_pending 
                    {/translate}
                    </font></b> 
                    { /if }
                  </td>
                </tr>
                <tr valign="top" class="row1"> 
                  <td width="20%"> 
                    {translate module=ticket}
                    field_priority 
                    {/translate}
                  </td>
                  <td width="80%"> 
                    { if $ticket.priority == '0' }
                    {translate module=ticket}
                    priority_standard 
                    {/translate}
                    { elseif $ticket.priority == '1' }
                    {translate module=ticket}
                    priority_medium 
                    {/translate}
                    { elseif $ticket.priority == '2' }
                    {translate module=ticket}
                    priority_high 
                    {/translate}
                    { elseif $ticket.priority == '3' }
                    {translate module=ticket}
                    priority_emergency 
                    {/translate}
                    { /if }
                  </td>
                </tr>
                {if $show_static_var != false}
                {foreach from=$static_var item=record}
                <tr valign="top" class="row1"> 
                  <td width="20%"> 
                    {$record.name}
                  </td>
                  <td width="80%"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}
                {/if}
                { if $ticket.status != "2" }
                <tr valign="top"  class="row1"> 
                  <td width="20%"> </td>
                  <td width="80%"> 
                    <div align="right">
                      <input type="hidden" name="email" value="{$VAR.email}">
                      <input type="hidden" name="key" value="{$VAR.key}">
                      <input type="hidden" name="ticket_status" value="2">
                      <input type="hidden" name="_page" value="ticket:user_view">
                      <input type="hidden" name="id" value="{$ticket.id}">
                      <input type="hidden" name="do[]" value="ticket:user_update">
                      <input type="submit" name="Submit" value="{translate module=ticket}close_ticket{/translate}" class="form_button">
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
<table width=100% border="0" cellspacing="0" cellpadding="1" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="4" cellpadding="1" class="row1">
          <tr valign="top"> 
            <td width="20%">&nbsp; </td>
            <td width="80%" valign="top" align="right"> 
              {$list->date_time($ticket.date_orig)}
            </td>
          </tr>
          <tr valign="top" class="row1">
            <td width="20%"> 
              {translate module=ticket}
              field_subject
              {/translate}
            </td>
            <td width="80%"> 
              {$ticket.subject}
            </td>
          </tr>
          <tr valign="top"  class="row1"> 
            <td width="20%"> 
              {translate module=ticket}
              field_body
              {/translate}
            </td>
            <td width="80%">
              {$ticket.body|linkalize|replace:"
":"<br>"}
            </td>
          </tr>
          {if $show_static_var != false}
          {foreach from=$static_var item=record}
          {/foreach}
          {/if}
        </table>
      </td>
    </tr>
  </table>
  
<br>
{if $ticket_reply != false}
{foreach from=$ticket_reply item=reply}
<table width=100% border="0" cellspacing="0" cellpadding="1" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="4" cellpadding="1" class="row1">
        <tr valign="top"> 
          <td width="20%">&nbsp; </td>
          <td width="80%" valign="top" align="right"> 
            {if $reply.staff_id > 1 }
            <b> 
            {$list->date_time($reply.date_orig)}
            </b> 
            {else}
            {$list->date_time($reply.date_orig)}
            {/if}
          </td>
        </tr>
        <tr valign="top"  class="row1"> 
          <td width="20%"> 
            {if $reply.staff_id < 1 }
            {translate module=ticket}
            user_wrote 
            {/translate}
            {else}
            <b> 
            {translate module=ticket}
            staff_wrote 
            {/translate}
            </b> 
            {/if}
          </td>
          <td width="80%"> 
            {if $reply.staff_id > 1 }
            <b>
            	{$reply.message|linkalize|replace:"\r\n":"<br>"}</b>
			{else}
	            {$reply.message|linkalize|replace:"\r\n":"<br>"}		
			{/if}
          </td>
        </tr>
        {if $show_static_var != false}
        {foreach from=$static_var item=record}
        {/foreach}
        {/if}
      </table>
    </td>
  </tr>
</table>
<br>
{/foreach}
{/if}
<form name="ticket_view" method="post" action="">
  <table width=100% border="0" cellspacing="0" cellpadding="1" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="4" cellpadding="1" class="row1">
          <tr valign="top"  class="row1"> 
            <td width="80%"> 
              <div align="center"> 
                { if $ticket.status != "2" }
                {translate module=ticket}
                user_add_response 
                {/translate}
                {else}
                {translate module=ticket}
                user_reopen_response 
                {/translate}
                {/if}
              </div>
            </td>
          </tr>
          <tr valign="top"  class="row1"> 
            <td width="80%"> 
              <div align="center">
                <textarea name="ticket_reply" cols="70" rows="10"></textarea>
              </div>
            </td>
          </tr>
          <tr valign="top"  class="row1">
            <td width="80%"> 
              <div align="center"> 
				{html_button}
                  <input type="hidden" name="_page" value="ticket:user_view">
                <input type="hidden" name="id" value="{$ticket.id}">
                <input type="hidden" name="do[]" value="ticket:user_update">
                <input type="hidden" name="email" value="{$VAR.email}">
                <input type="hidden" name="key" value="{$VAR.key}">
              </div>
            </td>
          </tr>
          {if $show_static_var != false}
          {foreach from=$static_var item=record}
          {/foreach}
          {/if}
        </table>
      </td>
    </tr>
  </table>
  <br>
</form>
{/if}
 
{else}
{$block->display("ticket:auth")}
{/if}
