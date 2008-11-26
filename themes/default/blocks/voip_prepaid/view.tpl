
{ $method->exe("voip_prepaid","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'voip_prepaid';
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
{foreach from=$voip_prepaid item=voip_prepaid} <a name="{$voip_prepaid.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="voip_prepaid_view" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_prepaid}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                        {html_select_account name="voip_prepaid_account_id" default=$voip_prepaid.account_id} </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_product_id
                        {/translate}</td>
                    <td width="65%">
                    { $list->menu('no', "voip_prepaid_product_id", "product", "sku", $voip_prepaid.product_id, "", "form_menu") }                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_pin
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_prepaid_pin" value="{$voip_prepaid.pin}" size="32"></td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_balance
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_prepaid_balance" value="{$voip_prepaid.balance}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_in_use
                        {/translate}</td>
                    <td width="65%">
                      {$voip_prepaid.in_use}
                 </td>
                  </tr>
           <tr valign="top">
                    <td width="35%">{translate module=voip_prepaid} field_date_expire {/translate}</td>
                    <td width="65%">{ $list->calender_view("voip_prepaid_date_expire", $voip_prepaid.date_expire,"") }
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
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$voip_prepaid.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="voip_prepaid:view">
    <input type="hidden" name="voip_prepaid_id" value="{$voip_prepaid.id}">
    <input type="hidden" name="do[]" value="voip_prepaid:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
