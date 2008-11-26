{ $method->exe("log_error","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else} 

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'log_error';
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
{foreach from=$log_error item=log_error} <a name="{$log_error.id}"></a>

    <!-- Display the field validation -->
    {if $log_error.id == $VAR.log_error_id}
        {foreach from=$form_validation item=record}
            {assign var=$record.field value="true"}
            <b>{$record.field_trans|upper}</b>: {$record.error} <br>
        {/foreach}
    {/if}

<!-- Display each record -->
<form name="update_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=log_error}
                title_view
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row0" valign="top"> 
                  <td width="35%"> 
                    {translate module=log_error}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($log_error.date_orig)}
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=log_error}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="log_error_account_id" default=$log_error.account_id}
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=log_error}
                    field_module 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$log_error.module}
                  </td>
                </tr>
                <tr class="row3" valign="top"> 
                  <td width="35%" height="23"> 
                    {translate module=log_error}
                    field_method 
                    {/translate}
                  </td>
                  <td width="65%" height="23"> 
                    {$log_error.method}
                  </td>
                </tr>
              </table>  <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row4" valign="top"> 
                  <td width="65%"> 
                    <div align="center">  
                      <textarea name="textarea" cols="70" rows="15" >{$log_error.message}</textarea>
                    </div>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>&nbsp; </td>
                        <td align="right"> 
                          <input type="button" name="delete2" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$log_error.id}','{$VAR.id}');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <br>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="log_error:view">
    <input type="hidden" name="log_error_id" value="{$log_error.id}">
    <input type="hidden" name="do[]" value="log_error:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}    
{/if}
