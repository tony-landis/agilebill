{ $method->exe("email_log","user_list") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{if $email_log}
 <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=email_log}title_user_list{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
          <tr class="row1" valign="middle" align="left">
            <td width="249" valign="top"><strong>{translate module=email_log}
                            field_date_orig
                {/translate}</strong></td>
            <td width="204" valign="top"><strong>{translate module=email_log}
                            field_email
                {/translate}</strong></td>
            <td width="875" valign="top"><strong>{translate module=email_log}
                            field_subject
                {/translate}</strong></td>
          </tr>
		  {foreach from=$email_log item=log}
          <tr class="row1" valign="middle" align="left">
            <td valign="top">{$list->date_time($log.date_orig)}</td>
            <td valign="top">{$log.email}</td>
            <td valign="top">				
				<a href="?_page=email_log:user_view&id={$log.id}&do[]=email_log:user_view">
				{if $log.userread != 1 AND $log.urgent==1}<font color='#FF0000'>{/if}
				{$log.subject}
				{if $log.userread != 1 AND $log.urgent==1}</font>{/if}
				</a>
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
{/if}
{/if}