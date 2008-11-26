
{ $method->exe("htaccess_dir","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'htaccess_dir';
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
{foreach from=$htaccess_dir item=htaccess_dir} <a name="{$htaccess_dir.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->


<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>        
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <form name="htaccess_dir_view" method="post" action="">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=htaccess_dir}
                title_view 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="25%"> 
                    {translate module=htaccess_dir}
                    field_htaccess_id 
                    {/translate}
                  </td>
                  <td width="75%"> 
                    { $list->menu("", "htaccess_dir_htaccess_id", "htaccess", "name", $htaccess_dir.htaccess_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="25%"> 
                    {translate module=htaccess_dir}
                    field_name 
                    {/translate}
                  </td>
                  <td width="75%"> 
                    <input type="text" name="htaccess_dir_name" value="{$htaccess_dir.name}" >
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="25%"> 
                    {translate module=htaccess_dir}
                    field_description 
                    {/translate}
                  </td>
                  <td width="75%"> 
                    <textarea name="htaccess_dir_description"  cols="65">{$htaccess_dir.description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="25%"> 
                    {translate module=htaccess_dir}
                    field_url 
                    {/translate}
                  </td>
                  <td width="75%"> 
                    <input type="text" name="htaccess_dir_url" value="{$htaccess_dir.url}"  size="55">
                    &nbsp;<a href="{$htaccess_dir.url}" target="_blank"><img src="themes/{$THEME_NAME}/images/icons/web_16.gif" alt="{$htaccess_dir.url}" border="0" width="16" height="16"></a></td>
                </tr>
                <tr valign="top"> 
                  <td width="25%"> 
                    {translate module=htaccess_dir}
                    field_path 
                    {/translate}
                  </td>
                  <td width="75%"> 
                    <input type="hidden" name="htaccess_dir_path" value="{$htaccess_dir.path}">
                    <textarea name="htaccess_dir_path"  cols="65" rows="2" disabled>{$htaccess_dir.path}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess_dir}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool('htaccess_dir_status', $htaccess_dir.status, 'form_menu') }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="25%">
                    {translate module=htaccess_dir}
                    field_recursive 
                    {/translate}
                  </td>
                  <td width="75%">
                    { $list->bool('htaccess_dir_recursive', $htaccess_dir.recursive, 'form_menu') }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td width="25%">&nbsp;</td>
                  <td width="75%">&nbsp;</td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="75%"> 
                    <table width="100%" border="0" cellpadding="2" class="row2">
                      <tr valign="top"> 
                        <td width="69%"> 
                          {translate module=htaccess_dir}
                          exclude_long
                          {/translate}
                        </td>
                        <td width="31%" valign="top" align="right"> 
                          { $list->menu_multi($htaccess_dir.exclude, 'htaccess_dir_exclude', 'htaccess_exclude', 'name', '', '12', 'form_menu') }
                        </td>
                      </tr>
                    </table>
                    <p align="center"><br>
                      <b>
                      {translate module=htaccess_dir}
                      field_htaccess 
                      {/translate}
                      </b><br>
                      <textarea name="_htaccess_dir_htaccess"  cols="90" rows="5" disabled>{$htaccess_dir.htaccess}</textarea>
                    </p>
                    <p align="center">
                      <input type="hidden" name="_page" value="htaccess_dir:view">
                      <input type="hidden" name="htaccess_dir_id" value="{$htaccess_dir.id}">
                      <input type="hidden" name="do[]" value="htaccess_dir:update">
                      <input type="hidden" name="id" value="{$VAR.id}">
                    </p>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="75%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td> 
                          <input type="submit" name="Submit3" value="{translate}submit{/translate}" class="form_button">
                        </td>
                        <td align="right"> 
                          <input type="button" name="delete2" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$htaccess_dir.id}','{$VAR.id}');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </form>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellpadding="3">
              <form name="search" method="post" action="">
                <tr> 
                  <td> 
                    <div align="center"> 
                      <input type="hidden" name="_page" value="core:search">
                      <input type="hidden" name="htaccess_htaccess_id" value="{$htaccess_dir.htaccess_id}">
                      <input type="hidden" name="module" value="htaccess_dir">
                      <input type="submit" name="Submit2" value="{translate module=htaccess}view_all{/translate}" class="form_button">
                    </div>
                  </td>
                </tr>
              </form>
            </table>
          </td>
        </tr>
      </table>
     </td>
    </tr>
  </table>
<br>
{/foreach}
{/if}
