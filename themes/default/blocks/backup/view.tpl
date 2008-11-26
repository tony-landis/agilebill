
{ $method->exe("backup","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'backup';
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
{foreach from=$backup item=backup} <a name="{$backup.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="backup_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=backup}
                title_view 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=backup}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($backup.date_orig)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=backup}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_view("backup_date_expire", $backup.date_expire, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=backup}
                    field_modules 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($backup.modules, 'backup_modules', 'module', 'name', '', '12', 'form_menu') }
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=backup}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="backup_notes" cols="40" rows="5" >{$backup.notes}</textarea>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"><a href="?_page=core:blank&do[]=backup:download&id={$backup.id}&_escape=1" target="_blank"> 
                    </a></td>
                  <td width="65%">&nbsp; </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="row1">
                <tr> 
                  <td> 
                    <div align="center"><a href="?_page=core:blank&do[]=backup:download&id={$backup.id}&_escape=1" target="_blank"> 
                      </a>
                      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                        <tr> 
                          <td><a href="?_page=core:blank&do[]=backup:download&id={$backup.id}&_escape=1" target="_blank"> 
                            <b>
                            {translate module=backup}
                            download 
                            {/translate}
                            </b></a> </td>
                          <td align="right">
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$backup.id}','{$VAR.id}');">
                          </td>
                        </tr>
                      </table>
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top">
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="row1">
                <tr> 
                  <td> 
                    <div align="center"> <b>
                      {translate module=backup}
                      restore_instructions 
                      {/translate}
                      </b><br>
                      <a href="?_page=core:admin&do[]=backup:restore&id={$backup.id}&_page={$VAR._page}"> 
                      <b> <br>
                      {translate module=backup}
                      restore 
                      {/translate}
                      </b></a> </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="backup:view">
    <input type="hidden" name="backup_id" value="{$backup.id}">
    <input type="hidden" name="do[]" value="backup:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
