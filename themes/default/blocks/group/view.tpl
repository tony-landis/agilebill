{ $method->exe("group","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else} 
{literal}
<!-- Define the update delete function -->
<script language="JavaScript">
    <!-- START
        var module = 'group';
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
{foreach from=$group item=group} <a name="{$group.id}"></a>

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
                {translate module=group}
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
                    {translate module=group}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="group_name"  value="{$group.name}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="group_notes" cols="45" rows="2" >{$group.notes}</textarea>
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$group.date_orig|date_format:$smarty.const.DEFAULT_DATE_FORMAT}
                  </td>
                </tr>
                <tr class="row3" valign="top"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->calender_view("group_date_start", $group.date_start, "form_field", $group.id)}
                  </td>
                </tr>
                <tr class="row4" valign="top"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->calender_view("group_date_expire", $group.date_expire, "form_field", $group.id)}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->bool("group_status", $group.status, "\" onChange\"submit()\"")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_pricing
                    {/translate}
                  </td>
                  <td width="65%">
                    {$list->bool("group_pricing", $group.pricing, "form_menu")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=group}
                    field_parent_id
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->menu("", "group_parent_id", "group", "name", $group.parent_id, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td width="35%">
                    <input type="hidden" name="_page2" value="group:view">
                    <input type="hidden" name="group_id" value="{$group.id}">
                    <input type="hidden" name="do[]" value="group:update">
                    <input type="hidden" name="id" value="{$VAR.id}">
                    <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="65%"> 
                    <div align="right">
                      <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$group.id}','{$VAR.id}');">
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
  
<br>
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
                <td width="35%"> 
                  {translate module=group}
                  select_module 
                  {/translate}
                </td>
                <td width="65%">
                  { $list->menu("no", "module_method_module_id", "module", "name", "all", "\" onChange=\"showGroupPerm(this)\"") }
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
{literal}
<script language="JavaScript">
 
function showGroupPerm(obj) {
	showIFrame('iframeInvoice',getPageWidth(600),400,'?_page=module_method:view_methods'+
			'&module_method_group_id={/literal}{$group.id}{literal}'+
			'&module_method_module_id='+obj.value+
			'&_escape=1');
}
</script>
{/literal}


<iframe name="iframeInvoice" id="iframeInvoice" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe>

{/foreach}
{/if}
