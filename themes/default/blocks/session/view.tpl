{ $method->exe("session","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}  

{literal} 
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'session';
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
{foreach from=$session item=session} <a name="{$session.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="session_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=session}
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
                    {translate module=session}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($session.date_orig)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($session.date_last)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($session.date_expire)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_logged 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $session.logged == "1"}
                    {translate}
                    true 
                    {/translate}
                    {else}
                    {translate}
                    false 
                    {/translate}
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_ip 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$session.ip}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_theme_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_files("", "session_theme_id", $session.theme_id, "theme", "", ".user_theme", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_country_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "session_country_id", "country", "name", $session.country_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_files("no", "session_language_id", $session.language_id, "theme", "", "_core.xml", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_currency_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "session_currency_id", "currency", "name", $session.currency_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="session_account_id" default=$session.account_id}
                  </td>
                </tr>
				{if $list->is_installed('affiliate') }
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_affiliate_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_affiliate name="session_affiliate_id" default=$session.affiliate_id}
                  </td>
                </tr>
				{/if}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>&nbsp; </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$session.id}','{$VAR.id}');">
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
  <input type="hidden" name="_page" value="session:view">
    <input type="hidden" name="session_id" value="{$session.id}">
    <input type="hidden" name="do[]" value="session:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
