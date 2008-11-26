
{ $method->exe("email_queue","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'email_queue';
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
{foreach from=$email_queue item=email_queue} <a name="{$email_queue.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="email_queue_view" method="post" action="">
{$COOKIE_FORM}
<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=email_queue}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($email_queue.date_orig)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_date_last
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($email_queue.date_last)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_status
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("email_queue_status", $email_queue.status, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_account_id
                        {/translate}</td>
                    
                  <td width="65%"> 
                    {html_select_account name="email_queue_account_id" default=$email_queue.account_id}
                  </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_email_template
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_email_template" value="{$email_queue.email_template}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_sql1
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_sql1" value="{$email_queue.sql1}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_sql2
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_sql2" value="{$email_queue.sql2}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_sql3
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_sql3" value="{$email_queue.sql3}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_sql4
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_sql4" value="{$email_queue.sql4}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_sql5
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_sql5" value="{$email_queue.sql5}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_var
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_var" value="{$email_queue.var}" size="32">
                    </td>
                  </tr>
          <tr class="row1" valign="middle" align="left">
                    <td width="35%"></td>
                    <td width="65%">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>
                            <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                          </td>
                          <td align="right">
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$email_queue.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="email_queue:view">
    <input type="hidden" name="email_queue_id" value="{$email_queue.id}">
    <input type="hidden" name="do[]" value="email_queue:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
