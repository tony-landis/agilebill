{if $smarty.const.SESS_LOGGED != true }
	{ $block->display("account:login") }
{else}

{ $method->exe("radius","do_list") } 
{ if ($method->result == FALSE) } 
{ $block->display("core:method_error") } 
{else}  

{if !$old_login && !$old_wireless && !$new_login && !$new_wireless} 
<p>No available services to configure, please wait for any pending orders to be approved or if you have not ordered service yet, please do so now by <a href="?_page=product_cat:menu">clicking here</a>.</p>
{/if}
 
<form id="update_form" name="update_form" method="post" action=""> 
 {if $old_login || $old_wireless} 
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">{translate module=radius}old_logins{/translate}</div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <!-- Loop through each login record -->
			  {foreach from=$old_login item=record}
			   <table width="100%" border="0" cellspacing="3" cellpadding="2" class="row1">
                <tr class="row4" valign="top"> 
                  <td width="50%"> 
                    {translate module=radius}user{/translate}      &nbsp;   
                    <input type="text" name="username[{$record.id}]" value="{$record.username}"  size="16" maxlength="32">
                  </td>
                  <td width="50%"> 
                    {translate module=radius}pass{/translate}      &nbsp;   
                    <input type="text" name="password[{$record.id}]" value="{$record.password}"  size="16" maxlength="32">
                  </td>
                  <td width="5">
				  	<a href="?_page=service:user_view&id={$record.service_id}"><b>?</b></a>
				  </td>
                </tr>
              </table>
              {/foreach}
			  
              <!-- Loop through each wireless record -->
              {foreach from=$old_wireless item=record}
              <table width="100%" border="0" cellspacing="3" cellpadding="2" class="row1">
                <tr class="row4" valign="top"> 
                  <td width="98%"> 
                    {translate module=radius}mac{/translate}      &nbsp;   
                    <input type="text" name="username[{$record.id}]" value="{$record.username}"  size="24" maxlength="24">
                  </td>
                  <td width="5">
				  	<a href="?_page=service:user_view&id={$record.service_id}"><b>?</b></a></td>
                </tr>
              </table>
              {/foreach}
			  			  
              <table width="100%" border="0" cellspacing="3" cellpadding="5" class="row1">
                <tr class="row4" valign="top"> 
                  <td width="69%"> 
                    <div align="center">
                      <input type="submit" name="Submit" value="Save Changes" class="form_button">
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
  {/if}
  <br>
  <br>
  {if $new_login || $new_wireless} 
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">{translate module=radius}new_logins{/translate}</div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
			
              <!-- Loop through each login record -->
			  {foreach from=$new_login item=record}
		      <table width="100%" border="0" cellspacing="3" cellpadding="2" class="row1">
                <tr class="row4" valign="top"> 
                  <td width="33%"> 
                    {translate module=radius}user{/translate}      &nbsp;   
                    <input type="text" name="new_username[{$record.id}]" value="{$record.username}"  size="16" maxlength="32">
                  </td>
                  <td width="33%"> 
                    {translate module=radius}pass{/translate}      &nbsp;   
                    <input type="text" name="new_password[{$record.id}]" value="{$record.password}"  size="16" maxlength="32">
                  </td>
                </tr>
              </table>
              {/foreach}
			  
              <!-- Loop through each wireless record -->
              {foreach from=$new_wireless item=record}
              <table width="100%" border="0" cellspacing="3" cellpadding="2" class="row1">
                <tr class="row4" valign="top"> 
                  <td width="33%">                    
				  {translate module=radius}mac{/translate}     &nbsp;            
				    <input type="text" name="new_username[{$record.id}]" value="{$record.username}"  size="24" maxlength="24">
                  </td>
                </tr>
              </table>
              {/foreach}
			  			  
              <table width="100%" border="0" cellspacing="3" cellpadding="5" class="row1">
                <tr class="row4" valign="top"> 
                  <td width="69%"> 
                    <div align="center">
                      <input type="submit" name="Submit" value="Save Changes" class="form_button">
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
  {/if}
  <input type="hidden" name="_page" value="{$VAR._page}">
  <input type="hidden" name="_page_current" value="{$VAR._page}">
  <input type="hidden" name="do[]" value="radius:do_update">
</form>
<br><br>
{/if} 
{/if}
