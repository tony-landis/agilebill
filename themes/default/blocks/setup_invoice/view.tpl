{ $method->exe("setup_invoice","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'setup_invoice';
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
{foreach from=$setup_invoice item=setup_invoice} <a name="{$setup_invoice.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="setup_invoice_update" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=setup_invoice}
                title_view
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup_invoice}
                    field_bill_to_company 
                    {/translate}
                  </td>
                  <td width="50%"> 
				    { $list->bool("setup_invoice_bill_to_company", $setup_invoice.bill_to_company, "form_menu") }
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup_invoice}
                    field_invoice_currency 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_invoice_invoice_currency"  value="{$setup_invoice.invoice_currency}" size="32">
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup_invoice}
                    field_invoice_decimals
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_invoice_invoice_decimals"  value="{$setup_invoice.invoice_decimals}" size="32">
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup_invoice}
                    field_items_summary_max
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_invoice_items_summary_max"  value="{$setup_invoice.items_summary_max}" size="32">
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup_invoice}
                    field_contact_us_url
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_invoice_contact_us_url"  value="{$setup_invoice.contact_us_url}" size="32">
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup_invoice}
                    field_contact_us_phone
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_invoice_contact_us_phone"  value="{$setup_invoice.contact_us_phone}" size="32">
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup_invoice}
                    field_news
                    {/translate}
                  </td>
                  <td width="50%"> 
					<textarea name="setup_invoice_news" cols="50" rows="4" wrap="Yes" >{$setup_invoice.news}</textarea>
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup_invoice}
                    field_page_type
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_invoice_page_type"  value="{$setup_invoice.page_type}" size="32">
                  </td>
                </tr> 
                <tr class="row1" valign="middle" align="left">
                  <td width="50%">{translate module=setup_invoice} field_invoice_delivery {/translate} </td>
                  <td width="50%"><select name="setup_invoice_invoice_delivery">
                    <option value="0" {if $setup_invoice.invoice_delivery =="0"}selected{/if}>None</option>
                    <option value="1" {if $setup_invoice.invoice_delivery =="1"}selected{/if}>E-mail</option>
                    <option value="2" {if $setup_invoice.invoice_delivery =="2"}selected{/if}>Print</option>
                  </select>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td>{translate module=setup_invoice} field_invoice_show_itemized{/translate} </td>
                  <td>{ $list->bool("setup_invoice_invoice_show_itemized", $setup_invoice.invoice_show_itemized, "form_menu") }  </td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td>{translate module=setup_invoice} field_invoice_pdf_plugin{/translate} </td>
                  <td>{html_menu_files path=invoice_pdf field=setup_invoice_invoice_pdf_plugin default=$setup_invoice.invoice_pdf_plugin}  </td>
                </tr>
								
 
				
                <tr class="row1" valign="middle" align="left">
                  <td>{translate module=setup_invoice} field_invoice_show_service_dates{/translate} </td>
                  <td>{ $list->bool("setup_invoice_invoice_show_service_dates", $setup_invoice.invoice_show_service_dates, "form_menu") } </td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td>{translate module=setup_invoice} field_advance_notice{/translate} </td>
                  <td><input name="setup_invoice_advance_notice" type="text"  value="{$setup_invoice.advance_notice}" size="3" maxlength="2"></td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button"></td>
                  <td>&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="setup_invoice:view">
    <input type="hidden" name="setup_invoice_id" value="{$setup_invoice.id}">
    <input type="hidden" name="do[]" value="setup_invoice:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}    
{/if}
