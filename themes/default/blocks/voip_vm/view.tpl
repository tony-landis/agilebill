
{ $method->exe("voip_vm","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'voip_vm';
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
{foreach from=$voip_vm item=voip_vm} <a name="{$voip_vm.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="voip_vm_view" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_vm}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                    {html_select_account name="voip_vm_account_id" default=$voip_vm.account_id} </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_context
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_context" value="{$voip_vm.context}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_mailbox
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_mailbox" value="{$voip_vm.mailbox}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_password
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_password" value="{$voip_vm.password}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_fullname
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_fullname" value="{$voip_vm.fullname}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_email
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_email" value="{$voip_vm.email}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_pager
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_pager" value="{$voip_vm.pager}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_vm}
                            field_options
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_vm_options" value="{$voip_vm.options}" size="32">
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
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$voip_vm.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="voip_vm:view">
    <input type="hidden" name="voip_vm_id" value="{$voip_vm.id}">
    <input type="hidden" name="do[]" value="voip_vm:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
