
{ $method->exe("email_log","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'email_log';
    	var locations = '{/literal}{$VAR.module_id}{literal}';
    	if (locations != "")
    	{
    		refresh(0,'#'+locations)
    	}
    	// Mass update, view, and delete controller
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
{foreach from=$email_log item=email_log} <a name="{$email_log.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="email_log_view" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=email_log}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="20%" valign="top">
                        {translate module=email_log}
                            field_account_id
                        {/translate}</td>
                    <td width="80%">{html_select_account name="email_log_account_id" default=$email_log.account_id} </td>
                  </tr>
          <tr class="row1" valign="middle" align="left">
                    <td width="20%" valign="top">{translate module=email_log}
                            field_date_orig
                    {/translate}</td>
                    <td width="80%">{$list->date_time($email_log.date_orig)}</td>
                </tr>
          <tr class="row1" valign="middle" align="left">
            <td valign="top">{translate module=email_log}
                            field_email
                  {/translate}</td>
            <td>{$email_log.email}</td>
          </tr>
          <tr class="row1" valign="middle" align="left">
            <td valign="top">{translate module=email_log}
                            field_subject
                  {/translate}</td>
            <td>
              {$email_log.subject}			</td>
          </tr>
          <tr class="row1" valign="middle" align="left">
            <td valign="top">{translate module=email_log}
                            field_message
                  {/translate}</td>
            <td> <textarea name="textarea2" cols="65" rows="12" disabled="disabled">{$email_log.message}</textarea></td>
          </tr>
          <tr class="row1" valign="middle" align="left">
            <td valign="top"><input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$email_log.id}','{$VAR.id}');"></td>
            <td>&nbsp;</td>
          </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="email_log:view">
    <input type="hidden" name="email_log_id" value="{$email_log.id}">
    <input type="hidden" name="do[]" value="email_log:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
