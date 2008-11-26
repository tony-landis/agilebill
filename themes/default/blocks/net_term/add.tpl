

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="net_term_add" name="net_term_add" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=net_term}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="net_term_name" value="{$VAR.net_term_name}" {if $net_term_name == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_sku
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="net_term_sku" value="{$VAR.net_term_sku}" {if $net_term_sku == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_terms
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="net_term_terms" value="{$VAR.net_term_terms}" size="5">
                    </td>						
                <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_status
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("net_term_status", 1, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_group_avail
                        {/translate}</td>
                    <td width="65%">
                      { $list->menu_multi($VAR.net_term_group_avail, "net_term_group_avail", "group", "name", "5", "5", "form_menu") } 
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_checkout_id
                        {/translate}</td>
                    <td width="65%">
                        { $list->menu("no", "net_term_checkout_id", "checkout", "name", $VAR.net_term_checkout_id, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_fee_type
                        {/translate}</td>
                    <td width="65%">
                        <select name="net_term_fee_type" size="2">
						  <option value="0" {if $VAR.net_term_fee_type==0}selected{/if}>Percentage of Invoice Total</option>
						  <option value="1" {if $VAR.net_term_fee_type==1}selected{/if}>Fixed Rate</option>
						</select>
					</td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_fee
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="net_term_fee" value="{$VAR.net_term_fee}" {if $net_term_fee == true}class="form_field_error"{/if} size="5">
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_suspend_intervals
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="net_term_suspend_intervals" value="{$VAR.net_term_suspend_intervals}" {if $net_term_suspend_intervals == true}class="form_field_error"{/if} size="5">
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_enable_emails
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("net_term_enable_emails", 1, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_sweep_type
                        {/translate}</td>
                    <td width="65%">
                      <select name="net_term_sweep_type" >
                          <option value="0" {if $VAR.net_term_sweep_type == "0"}selected{/if}>Daily</option>
                          <option value="1" {if $VAR.net_term_sweep_type == "1"}selected{/if}>Weekly</option>
                          <option value="2" {if $VAR.net_term_sweep_type == "2"}selected{/if}>Monthly</option>
                          <option value="3" {if $VAR.net_term_sweep_type == "3"}selected{/if}>Quarterly</option>
                          <option value="4" {if $VAR.net_term_sweep_type == "4"}selected{/if}>Semi-anually</option>
                          <option value="5" {if $VAR.net_term_sweep_type == "5"}selected{/if}>Anually</option>
                          <option value="6" {if $VAR.net_term_sweep_type == "6"}selected{/if}>On Service Rebill</option>
                        </select>
                    </td>
                </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="net_term:view">
                      <input type="hidden" name="_page_current" value="net_term:add">
                      <input type="hidden" name="do[]" value="net_term:add">
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
