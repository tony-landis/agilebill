{ $method->exe("checkout","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'checkout';
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
{foreach from=$checkout item=checkout} <a name="{$checkout.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form id="checkoutedit" name="checkout_view" method="post" action="">
  
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=checkout}
                title_add 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="checkout_name" value="{$checkout.name}" {if $checkout_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_description 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="checkout_description"   cols="40" rows="2">{$checkout.description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_active 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("checkout_active", $checkout.active, " onchange=\"submit()\"") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_graphic_url
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="checkout_graphic_url" value="{$checkout.graphic_url}" {if $checkout_grapic_url == true}class="form_field_error"{/if}>
				  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_checkout_plugin 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $checkout.checkout_plugin }
                    <input type="hidden" name="checkout_checkout_plugin" value="{ $checkout.checkout_plugin }">
                  </td>
                </tr>
              </table>
              {assign var="ablock" 	  value="checkout_plugin:plugin_cfg_"}
              {assign var="afile"     value=$checkout.checkout_plugin}
              {assign var="blockfile" value="$ablock$afile"}
              { $block->display($blockfile) }
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="1" cellpadding="3" class="row1">
                <tr valign="top"  class="row2"> 
                  <td width="33%"> 
                    {translate module=checkout}
                    field_allow_new 
                    {/translate}
                  </td>
                  <td width="33%"> 
                    {translate module=checkout}
                    field_allow_recurring 
                    {/translate}
                  </td>
                  <td width="33%"> 
                    {translate module=checkout}
                    field_allow_trial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    { $list->bool("checkout_allow_new", $checkout.allow_new, "form_menu") }
                  </td>
                  <td width="33%"> 
                    { $list->bool("checkout_allow_recurring", $checkout.allow_recurring, "form_menu") }
                  </td>
                  <td width="33%"> 
                    { $list->bool("checkout_allow_trial", $checkout.allow_trial, "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="1" cellpadding="3" class="row1">
                <tr valign="top"  class="row2"> 
                  <td width="33%"> 
                    {translate module=checkout}
                    field_total_minimum 
                    {/translate}
                  </td>
                  <td width="33%"> 
                    {translate module=checkout}
                    field_total_maximum 
                    {/translate}
                  </td>
                  <td width="33%"> 
                    {translate module=checkout}
                    field_max_decline_attempts 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    <input type="text" name="checkout_total_minimum" value="{$checkout.total_minimum}" {if $checkout_total_minimum == true}class="form_field_error"{/if} size="5">
                    { $list->currency_iso("") }
                  </td>
                  <td width="33%"> 
                    <input type="text" name="checkout_total_maximum" value="{$checkout.total_maximum}" {if $checkout_total_maximum == true}class="form_field_error"{/if} size="5">
                    { $list->currency_iso("") }
                  </td>
                  <td width="33%"> 
                    <input type="text" name="checkout_max_decline_attempts" value="{$checkout.max_decline_attempts}" {if $checkout_max_decline_attempts == true}class="form_field_error"{/if} size="5">
                    { $list->currency_iso("") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>		  
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="1" cellpadding="3" class="row1">
                <tr valign="top"  class="row2"> 
                  <td width="33%"> 
                    {translate module=checkout}
                    field_required_groups 
                    {/translate}
                  </td>
                  <td width="33%"> 
                    {translate module=checkout}
                    field_excluded_products 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    { $list->menu_multi($checkout.required_groups, "checkout_required_groups", "group", "name", "5", "5", "form_menu") }
                  </td>
                  <td width="33%"> 
                    { $list->menu_multi($checkout.excluded_products, "checkout_excluded_products", "product", "sku", "5", "5", "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_manual_approval_all 
                    {/translate}
                  </td>
                  <td width="50%">
                    {translate module=checkout}
                    field_manual_approval_recur 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top">
                  <td width="50%">
                    { $list->bool("checkout_manual_approval_all", $checkout.manual_approval_all, "form_menu") }
                  </td>
                  <td width="50%">
                    { $list->bool("checkout_manual_approval_recur", $checkout.manual_approval_recur, "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"  class="row2"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_manual_approval_amount 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {translate module=checkout}
                    field_manual_approval_currency 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <input type="text" name="checkout_manual_approval_amount" value="{$checkout.manual_approval_amount}" {if $checkout_manual_approval_amount == true}class="form_field_error"{/if} size="5">
                    { $list->currency_iso("") }
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($checkout.manual_approval_currency, "checkout_manual_approval_currency", "currency", "three_digit", "5", "5", "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_manual_approval_country 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {translate module=checkout}
                    field_manual_approval_group 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    { $list->menu_multi($checkout.manual_approval_country, "checkout_manual_approval_country", "country", "name", "5", "5", "form_menu") }
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($checkout.manual_approval_group, "checkout_manual_approval_group", "group", "name", "5", "5", "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_default_when_amount 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {translate module=checkout}
                    field_default_when_currency 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <input type="text" name="checkout_default_when_amount" value="{$checkout.default_when_amount}" {if $checkout_default_when_amount == true}class="form_field_error"{/if} size="5">
                    { $list->currency_iso("") }
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($checkout.default_when_currency, "checkout_default_when_currency", "currency", "three_digit", "5", "5", "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"  class="row2"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_default_when_country 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {translate module=checkout}
                    field_default_when_group 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    { $list->menu_multi($checkout.default_when_country, "checkout_default_when_country", "country", "name", "5", "5", "form_menu") }
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($checkout.default_when_group, "checkout_default_when_group", "group", "name", "5", "5", "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_allowed_currencies 
                    {/translate}
                  </td>
                  <td width="50%">
                    {translate module=checkout}
                    field_email_template 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%">
                    { $list->menu_multi($checkout.allowed_currencies, "checkout_allowed_currencies", "currency", "three_digit", "5", "5", "form_menu") }
                  </td>
                  <td width="50%">
                    <textarea name="checkout_email_template"  cols="40" rows="4">{$checkout.email_template}</textarea>
                  </td>
                </tr>
                <tr valign="top">
                  <td width="50%"></td>
                  <td width="50%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="checkout:view">
    <input type="hidden" name="checkout_id" value="{$checkout.id}">
    <input type="hidden" name="do[]" value="checkout:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
