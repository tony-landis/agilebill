
{ $method->exe("charge","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript">
    <!-- START
        var module = 'charge';
    	var locations = '{/literal}{$VAR.module_id}{literal}';
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
{foreach from=$charge item=charge} <a name="{$charge.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form id="charge_view" name="charge_view" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=charge}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=charge}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($charge.date_orig)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=charge}
                            field_status
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("charge_status", $charge.status, "onChange=\"submit()\"") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=charge}
                            field_sweep_type
                        {/translate}</td>
                    
                  <td width="65%"> 
                    {if $charge.sweep_type == "0"}
                    Daily
                    {/if}
                    {if $charge.sweep_type == "1"}
                    Weekly
                    {/if}
                    {if $charge.sweep_type == "2"}
                    Monthly
                    {/if}
                    {if $charge.sweep_type == "3"}
                    Quarterly
                    {/if}
                    {if $charge.sweep_type == "4"}
                    Semi-Anually
                    {/if}
                    {if $charge.sweep_type == "5"}
                    Anually
                    {/if}
                    {if $charge.sweep_type == "6"}
                    On Service Rebill
                    {/if}
                  </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=charge}
                            field_account_id
                        {/translate}</td>
                    
                  <td width="65%"> 
                    {html_select_account name="charge_account_id" default=$charge.account_id}
                  </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=charge}
                            field_product_id
                        {/translate}</td>
                    <td width="65%">
                        {$charge.product_id}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=charge}
                            field_service_id
                        {/translate}</td>
                    <td width="65%">
                        {$charge.service_id}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=charge}
                            field_amount
                        {/translate}</td>
                    
                  <td width="65%"> 
                    <input type="text" name="charge_amount" value="{$charge.amount}"  size="16">
                    {$list->currency_iso("")}
                  </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=charge}
                            field_quantity
                        {/translate}</td>
                    <td width="65%">
                        
                    <input type="text" name="charge_quantity" value="{$charge.quantity}"  size="3">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=charge}
                            field_taxable
                        {/translate}</td>
                    <td width="65%">
                        {if $charge.taxable == "1"}{translate}true{/translate}{else}{translate}false{/translate}{/if}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=charge}
                            field_attributes
                        {/translate}</td>
                    <td width="65%">
                        {$charge.attributes|nl2br|replace:"==":" -> "}
                    </td>
                  </tr>
          <tr class="row1" valign="middle" align="left">
                    <td width="35%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="65%"> 
                    <div align="right">
                      <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$charge.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="charge:view">
    <input type="hidden" name="charge_id" value="{$charge.id}">
    <input type="hidden" name="do[]" value="charge:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
