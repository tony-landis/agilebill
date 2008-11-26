
{ $method->exe("invoice_item","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'invoice_item';
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
{foreach from=$invoice_item item=invoice_item} <a name="{$invoice_item.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="invoice_item_view" method="post" action="">

<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=invoice_item}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_sku
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($invoice_item.sku)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_item_type
                        {/translate}</td>
                    <td width="65%">
                        {if $invoice_item.item_type == "1"}{translate}true{/translate}{else}{translate}false{/translate}{/if}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_product_attr
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($invoice_item.product_attr)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_price_base
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($invoice_item.price_base)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_price_setup
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($invoice_item.price_setup)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_recurring_schedule
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($invoice_item.recurring_schedule)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_name
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($invoice_item.domain_name)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_tld
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($invoice_item.domain_tld)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_term
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($invoice_item.domain_term)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_type
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($invoice_item.domain_type)}
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
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$invoice_item.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="invoice_item:view">
    <input type="hidden" name="invoice_item_id" value="{$invoice_item.id}">
    <input type="hidden" name="do[]" value="invoice_item:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
