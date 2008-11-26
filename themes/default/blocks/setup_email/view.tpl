{ $method->exe("setup_email","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else} 

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'setup_email';
    	var locations 	= '{/literal}{$VAR.module_id}{literal}';		
		var id 			= '{/literal}{$VAR.id}{literal}';
		var ids 		= '{/literal}{$VAR.ids}{literal}';    	 
		var array_id    = id.split(",");
		var array_ids   = ids.split(",");		
		var num=0;
		if(array_id.length > 2) {				 
			document.location = '?_page='+module+':view&id='+array_id[0]+'&ids='+id;				 		
		}else if (array_ids.length > 2) {
			document.write(view_nav_top(array_ids,id,ids));
		}
		
    	function delete_record(id,ids)
    	{				
    		temp = window.confirm("{/literal}{translate}alert_delete{/translate}{literal}");
    		if(temp == false) return;
    		
    		var replace_id = id + ",";
    		ids = ids.replace(replace_id, '');		
    		if(ids == '') {
    			var url = '?_page=core:search&module=' + module + '&do[]=' + module + ':delete&delete_id=' + id + COOKIE_URL;
    			window.location = url;
    			return;
    		} else {
    			var page = 'view&id=' +ids;
    		}		
    		
    		var doit = 'delete';
    		var url = '?_page='+ module +':'+ page +'&do[]=' + module + ':' + doit + '&delete_id=' + id + COOKIE_URL;
    		window.location = url;	
    	}
    //  END -->
    </script>
{/literal}

<!-- Loop through each record -->
{foreach from=$setup_email item=setup_email} <a name="{$setup_email.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="setup_email_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=setup_email}
                title_view
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_name" value="{$setup_email.name}"  size="32">
                  </td>
                </tr>
				
				{if $list->is_installed("email_queue")}
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_queue 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("setup_email_queue", $setup_email.queue, "onChange=\"submit()\"") }
                  </td>
                </tr>
				{/if}
				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_notes" value="{$setup_email.notes}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_from_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_from_name" value="{$setup_email.from_name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_from_email 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_from_email" value="{$setup_email.from_email}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_cc_list 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="setup_email_cc_list" cols="40" rows="5" >{$setup_email.cc_list}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_bcc_list 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="setup_email_bcc_list" cols="40" rows="5" >{$setup_email.bcc_list}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("setup_email_type", $setup_email.type, "form_menu") }
                    <br>
                    {translate module=setup_email}
                    smtp_help 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp; </td>
                  <td width="65%">&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_server 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_server" value="{$setup_email.server}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_username 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_username" value="{$setup_email.username}"  size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_password 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="password" name="setup_email_password" value="{$setup_email.password}"  size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"></td>
                  <td width="65%">&nbsp;</td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_piping 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="setup_email_piping">
                      <option value="0" {if $setup_email.piping == "0"}selected{/if}></option>
                      <option value="1" {if $setup_email.piping == "1"}selected{/if}>POP3</option>
                      <option value="2" {if $setup_email.piping == "2"}selected{/if}>IMAP</option>
                    </select>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_piping_host 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_piping_host" value="{$setup_email.piping_host}"  size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_piping_username 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="setup_email_piping_username" value="{$setup_email.piping_username}"  size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_piping_password 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="password" name="setup_email_piping_password" value="{$setup_email.piping_password}"  size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=setup_email}
                    field_piping_action 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="setup_email_piping_action">
                      <option value="0" {if $setup_email.piping_action == "0"}selected{/if}> 
                      {translate module=setup_email}
                      piping_action_leave 
                      {/translate}
                      </option>
                      <option value="1" {if $setup_email.piping_action == "1"}selected{/if}> 
                      {translate module=setup_email}
                      piping_action_delete 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="65%" align="right"> 
                    <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$setup_email.id}','{$VAR.id}');">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="setup_email:view">
    <input type="hidden" name="setup_email_id" value="{$setup_email.id}">
    <input type="hidden" name="do[]" value="setup_email:update">
  <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
