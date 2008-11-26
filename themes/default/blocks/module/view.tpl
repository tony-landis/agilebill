{ $method->exe("module","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else} 

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'module';
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
{foreach from=$module item=module}  

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form id="update_form" name="update_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=module}
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
                    {translate module=module}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="module_name"  value="{$module.name}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=module}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="module_notes" cols="45" rows="5" >{$module.notes}</textarea>
                  </td>
                </tr>
                <tr class="row3" valign="top"> 
                  <td width="35%"> 
                    {translate module=module}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$module.date_orig|date_format:$smarty.const.DEFAULT_DATE_FORMAT}
                  </td>
                </tr>
                <tr class="row4" valign="top"> 
                  <td width="35%"> 
                    {translate module=module}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$module.date_last|date_format:$smarty.const.DEFAULT_DATE_FORMAT}
                    <input type="hidden" name="module_date_last" value="{$smarty.now|date_format:$smarty.const.DEFAULT_DATE_FORMAT}">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=module}
                    field_parent_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "module_parent_id", "module", "name", $module.parent_id, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=module}
                    field_menu_display 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("module_menu_display", $module.menu_display, "form_menu") 
                }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=module}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("module_status", $module.status, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td> 
                          <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                        </td>
                        <td align="right"> 
                          <input type="button" name="delete2" value="{translate module=module}uninstall{/translate}" class="form_button" onClick="delete_record('{$module.id}','{$VAR.id}');">
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
  <input type="hidden" name="_page" value="module:view">
    <input type="hidden" name="module_id" value="{$module.id}">
  <input type="hidden" name="do[]" value="module:update">
  <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  
<p align="center"><a href="javascript:showMethods()"> 
  {translate module=module}
  view_methods 
  {/translate}
  </a> &nbsp;|&nbsp; <a href="javascript:showAdd()"> 
  {translate module=module}
  add_methods 
  {/translate}
  </a><br>
</p>
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading"> 
            <div align="center"> 
              {translate module=group}
              title_relation 
              {/translate}
            </div>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
              <tr class="row1" valign="middle" align="left"> 
                <td width="28%"> 
                  {translate module=group}
                  select_module 
                  {/translate}
                </td>
                <td width="72%"> 
                  { $list->menu("no", "module_method_module_id", "group", "name", "all", "\" onChange=\"showGroupPerm(this)\"") }
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<div align="center"> 
  <p> 
    {literal}
    <script language="JavaScript">  
	var module_id 	= {/literal}{$module.id}{literal};  
   
	function showMethods() {
		showIFrame('iframeModule',550,400,'?_page=core:search_iframe&module=module_method&_escape=1&module_method_module_id='+module_id+
				   '&_escape_next=1&_next_page_one=view&_next_page_none=add&name_id1=module_method_module_id&val_id1='+module_id);
	}
	
	function showAdd() {
		showIFrame('iframeModule',550,300,'?_page=module_method:add&module_method_module_id='+module_id+'&_escape=1');
		   
	} 
		
	function showGroupPerm(obj) {
		showIFrame('iframeModule',550,400,'?_page=module_method:view_methods'+
				'&module_method_group_id='+obj.value+
				'&module_method_module_id={/literal}{$module.id}{literal}&_escape=1');
	}	
</script>
    {/literal}
  </p>
  <br>
  <iframe name="iframeModule" id="iframeModule" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe>
</div>
<form id="update_form" name="update_form" method="post" action="">
  <input type="hidden" name="_page" value="core:search">
  <input type="hidden" name="module_method_module_id" value="{$module.id}">
  <input type="hidden" name="module" value="module_method">
  <input type="hidden" name="_escape" value="1">
</form>
{/foreach}    
{/if}
