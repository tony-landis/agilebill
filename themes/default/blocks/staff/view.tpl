
{ $method->exe("staff","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'staff';
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
{foreach from=$staff item=staff} <a name="{$staff.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="staff_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=staff}
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
                    {translate module=staff}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->date_time($staff.date_orig) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=staff}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="staff_account_id" default=$staff.account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=staff}
                    field_nickname 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="staff_nickname" value="{$staff.nickname}"  size="32">
                  </td>
                </tr>
				
				{ if $list->is_installed('ticket') }
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=staff}
                    field_department_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->check("", "staff_department_avail", "ticket_department", "name", $staff.department_avail, "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=staff}
                    field_notify_new 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("staff_notify_new", $staff.notify_new, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=staff}
                    field_notify_change 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("staff_notify_change", $staff.notify_change, "form_menu") }
                  </td>
                </tr> 
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%">{translate module=staff}field_signature{/translate}</td>
		    	{else}
					<input type="hidden" name="staff_department_avail" value="false">
				{/if}  
  
                  <td width="65%">{html_textarea name=staff_signature default=$staff.signature} 
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button"></td>
                  <td align="right"><input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$staff.id}','{$VAR.id}');"></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="staff:view">
    <input type="hidden" name="staff_id" value="{$staff.id}">
    <input type="hidden" name="do[]" value="staff:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>

{ if $list->is_installed('ticket') }  
<form name="staff_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="1" class="table_background">
    <tr> 
      <td class="table_heading"> 
        <center>
          {translate module=staff}
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
  <input type="hidden" name="staff_id" value="{$staff.id}">
  <input type="hidden" name="module" value="ticket">
  <input type="hidden" name="_escape" value="1">
</form>
{/if}

{/foreach}
{/if}
