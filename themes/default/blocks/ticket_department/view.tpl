
{ $method->exe("ticket_department","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'ticket_department';
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
{foreach from=$ticket_department item=ticket_department} <a name="{$ticket_department.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="ticket_department_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=ticket_department}
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
                    {translate module=ticket_department}
                    field_group_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($ticket_department.group_id, 'ticket_department_group_id', 'group', 'name', '', '10', 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket_department}
                    field_setup_email_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "ticket_department_setup_email_id", "setup_email", "name", $ticket_department.setup_email_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket_department}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="ticket_department_name" value="{$ticket_department.name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=ticket_department}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("ticket_department_status", $ticket_department.status, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%" valign="top">
                    {translate module=ticket_department}
                    field_piping 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->bool("ticket_department_piping", $ticket_department.piping, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%">
                    {translate module=ticket_department}
                    field_piping_setup_email_id 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->menu("", "ticket_department_piping_setup_email_id", "setup_email", "name", $ticket_department.piping_setup_email_id, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%" valign="top"> 
                    {translate module=ticket_department}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%">
                    <textarea name="ticket_department_description" cols="40" rows="10" >{$ticket_department.description}</textarea>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td width="35%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="65%" align="right"> 
                    <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$ticket_department.id}','{$VAR.id}');">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="ticket_department:view">
    <input type="hidden" name="ticket_department_id" value="{$ticket_department.id}">
    <input type="hidden" name="do[]" value="ticket_department:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  
<form name="ticket_department_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="1" class="table_background">
    <tr> 
      <td class="table_heading"> 
        <center>
          {translate module=ticket_department}
          view_tickets 
          {/translate}
        </center>
      </td>
    </tr>
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
          <tr class="row1" valign="middle" align="left"> 
            <td width="35%"></td>
            <td width="65%"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td> 
                    <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td align="right">&nbsp; </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="core:search">
  <input type="hidden" name="ticket_department_id" value="{$ticket_department.id}">
  <input type="hidden" name="module" value="ticket">
  <input type="hidden" name="_escape" value="1">
</form>
{/foreach}
{/if}
