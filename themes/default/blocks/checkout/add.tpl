

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="checkout_add" name="checkout_add" method="post" action="">

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
                    <input type="text" name="checkout_name" value="{$VAR.checkout_name}" {if $checkout_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_description 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="checkout_description"   cols="40" rows="2">{$VAR.checkout_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_active 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {if $VAR.checkout_active != ""}
                    { $list->bool("checkout_active", $VAR.checkout_active, "form_menu") }
                    {else}
                    { $list->bool("checkout_active", "1", "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_graphic_url
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="checkout_graphic_url" value="{$VAR.checkout_graphic_url}" {if $checkout_grapic_url == true}class="form_field_error"{/if}>
				  </td>
                </tr>						
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=checkout}
                    field_checkout_plugin 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_files("", "checkout_checkout_plugin", $VAR.checkout_checkout_plugin, "checkout_plugin", "", ".php", "form_menu") }
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
                    { $list->bool("checkout_allow_new", $VAR.checkout_allow_new, "form_menu") }
                  </td>
                  <td width="33%"> 
                    { $list->bool("checkout_allow_recurring", $VAR.checkout_allow_recurring, "form_menu") }
                  </td>
                  <td width="33%"> 
                    { $list->bool("checkout_allow_trial", $VAR.checkout_allow_trial, "form_menu") }
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
                    <input type="text" name="checkout_total_minimum" value="{$VAR.checkout_total_minimum}" {if $checkout_total_minimum == true}class="form_field_error"{/if} size="5">
                    { $list->currency_iso("") }
                  </td>
                  <td width="33%"> 
                    <input type="text" name="checkout_total_maximum" value="{$VAR.checkout_total_maximum}" {if $checkout_total_maximum == true}class="form_field_error"{/if} size="5">
                    { $list->currency_iso("") }
                  </td>
                  <td width="33%"> 
                    <input type="text" name="checkout_max_decline_attempts" value="{$VAR.checkout_max_decline_attempts}" {if $checkout_max_decline_attempts == true}class="form_field_error"{/if} size="5">
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
                    { $list->menu_multi($VAR.checkout_required_groups, "checkout_required_groups", "group", "name", "5", "5", "form_menu") }
                  </td>
                  <td width="33%"> 
                    { $list->menu_multi($VAR.checkout_excluded_products, "checkout_excluded_products", "product", "sku", "5", "5", "form_menu") }
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
                    { $list->bool("checkout_manual_approval_all", $VAR.checkout_manual_approval_all, "form_menu") }
                  </td>
                  <td width="50%">
                    { $list->bool("checkout_manual_approval_recur", $VAR.manual_approval_recur, "form_menu") }
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
                    <input type="text" name="checkout_manual_approval_amount" value="{$VAR.checkout_manual_approval_amount}" {if $checkout_manual_approval_amount == true}class="form_field_error"{/if} size="5">
                    { $list->currency_iso("") }
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($VAR.checkout_manual_approval_currency, "checkout_manual_approval_currency", "currency", "name", "5", "5", "form_menu") }
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
                    { $list->menu_multi($VAR.checkout_manual_approval_country, "checkout_manual_approval_country", "country", "name", "5", "5", "form_menu") }
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($VAR.checkout_manual_approval_group, "checkout_manual_approval_group", "group", "name", "5", "5", "form_menu") }
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
                    <input type="text" name="checkout_default_when_amount" value="{$VAR.checkout_default_when_amount}" {if $checkout_default_when_amount == true}class="form_field_error"{/if} size="5">
                    { $list->currency_iso("") }
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($VAR.checkout_default_when_currency, "checkout_default_when_currency", "currency", "name", "5", "5", "form_menu") }
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
                    { $list->menu_multi($VAR.checkout_default_when_country, "checkout_default_when_country", "country", "name", "5", "5", "form_menu") }
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($VAR.checkout_default_when_group, "checkout_default_when_group", "group", "name", "5", "5", "form_menu") }
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
                    { $list->menu_multi($VAR.checkout_allowed_currencies, "checkout_allowed_currencies", "currency", "name", "5", "5", "form_menu") }
                  </td>
                  <td width="50%">
                    <textarea name="checkout_email"  cols="40" rows="4">{$VAR.checkout_email_template}</textarea>
                  </td>
                </tr>
                <tr valign="top">
                  <td width="50%"></td>
                  <td width="50%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="checkout:view">
                    <input type="hidden" name="_page_current" value="checkout:add">
                    <input type="hidden" name="do[]" value="checkout:add">
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
