{if $smarty.const.SESS_LOGGED == false}
{$block->display("account:login")}
{else}
{if $SESS_LOGGED == "1" }
{ $method->exe("invoice","user_view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

<!-- prototype  -->
<script src="includes/javascript/prototype.js" type="text/javascript"></script>
<script src="includes/javascript/effects.js" type="text/javascript"></script>
<script src="includes/javascript/dragdrop.js" type="text/javascript"></script> 
<script src="includes/javascript/controls.js" type="text/javascript"></script>

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'invoice';
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
{foreach from=$invoice item=invoice}

<!-- Display the field validation -->
{if $form_validation}
{ $block->display("core:alert_fields") }
{/if}

<!-- checkout options -->
{ if $invoice.billing_status == "0" }
	{ $block->display("invoice:checkoutoptions") }
{/if}

		   
<form name="invoice_view" method="post" action="">
 <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=invoice}
                title_view 
                {/translate}
                { $invoice.id }
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=invoice}
                    field_date_orig 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=invoice}
                    field_date_last 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=invoice}
                    field_due_date 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    {$list->date_time($invoice.date_orig)}
                  </td>
                  <td width="33%"> 
                    {$list->date_time($invoice.date_last)}
                    <input type="hidden" name="invoice_date_last" value="{$smarty.now}">
                  </td>
                  <td width="33%"> 
                    {$list->date($invoice.due_date)}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=invoice}
                    field_process_status 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> <font color="#990000">
                    {translate module=invoice}
                    field_billing_status 
                    {/translate}
                    </font></b></td>
                  <td width="33%"> <b> <a href="?_page=invoice:pdf&id={$invoice.id}&_escape=true" target="_blank"> 
                    {translate module=invoice}
                    print_invoice 
                    {/translate}
                    </a> </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
				  {if $invoice.process_status == 1}
				  	{translate}true{/translate}
				  {else}
				  	{translate}false{/translate}
				  {/if}  
                  </td>
                  <td width="33%"> <b> 
                    {if $invoice.balance == 0}
                    {translate module=invoice}
                    paid 
                    {/translate}
                    {else}
                    {$list->format_currency_num($invoice.balance,$invoice.actual_billed_currency_id)}
                    {/if}
                    </b> </td>
                  <td width="33%"> <a href="?_page=invoice:pdf&id={$invoice.id}&_escape=true" target="_blank"><img src="themes/{$THEME_NAME}/images/icons/print_16.gif" border="0" alt="E-mail User"></a></td>
                </tr>
              </table>
            </td>
          </tr>
          {* show discount details *}
          {if $invoice.discount_arr != '' && $invoice.discount_amt > 0}
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=invoice}
                    field_discount_amt 
                    {/translate}
                    </b></td>
                  <td width="33%"> 
                    {assign var=discount_details value=$invoice.discount_popup}
                    <a href="#" {popup capcolor="ffffff" textcolor="333333" bgcolor="506DC7" fgcolor="FFFFFF" sticky=false width="250" caption="Discount Details" text="$discount_details" snapx=1 snapy=1 sticky=1}> 
                    {$list->format_currency_num($invoice.discount_amt, $invoice.actual_billed_currency_id)}
                    </a> </td>
                  <td width="33%">&nbsp; </td>
                </tr>
              </table>
            </td>
          </tr> 
          {/if}
		  
		  <!-- billing details -->
		  {assign var=cc_user value=true}
		  {* show checkout/payment plugin details *}
          {if $invoice.checkout_plugin_id != '0'}
          {assign var=sql1 value=" AND id='"}
          {assign var=sql2 value="' "}
          {assign var=sql3 value=$invoice.checkout_plugin_id}
          {assign var=sql  value=$sql1$sql3$sql2}
          {if $list->smarty_array("checkout", "checkout_plugin", $sql, "checkout") }
          {assign var=checkout_plugin value=$checkout[0].checkout_plugin}           
              {assign var="ablock" 	value="checkout_plugin:plugin_inv_"}
              {assign var="blockfile" value="$ablock$checkout_plugin"}
              {$block->display($blockfile)} 
          {/if}
          {/if}          
		  <!-- end billing details -->
		  
          <tr valign="top"> 
            <td width="65%" class="row1" height="49"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=invoice}
                    field_tax_amt 
                    {/translate}
                    </b></td>
                  <td width="34%"><b>{translate module=invoice} field_discount_amt {/translate}  
                    </b></td>
                  <td width="33%"><b>{translate module=invoice} field_tax_id {/translate} </b></td>
                </tr>
                <tr valign="top" class="row2"> 
                  <td width="33%"> 
                    {$list->format_currency_num($invoice.tax_amt, $invoice.actual_billed_currency_id)}
                  </td>
                  <td width="34%">{if $invoice.discount_amt > 0}
                    <div id="taxpanel1" style="display:none"> {$invoice.discount_popup_user} </div>
                    <div id="taxpanel"> <a href="#" onclick="{literal} new Effect.Fade('taxpanel', {duration: 0} ); new Effect.Appear('taxpanel1', {duration: .5}); return false;" {/literal}>{$list->format_currency_num($invoice.discount_amt, $invoice.actual_billed_currency_id)}</a> </div>
                    {else}---{/if}  
                  </td>
                  <td width="33%">{if $invoice.tax_amt > 0} {foreach from=$invoice.tax_arr item=taxz} {$taxz.description} - {$list->format_currency_num($taxz.amount, $invoice.actual_billed_currency_id)}<br>
{/foreach} {else}---{/if} </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=invoice}
                    field_total_amt 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=invoice}
                    field_billed_amt 
                    {/translate}
                    </b></td>
                  <td width="33%"> </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    {$list->format_currency_num($invoice.total_amt, $invoice.actual_billed_currency_id)}
                  </td>
                  <td width="33%"> 
                    {$list->format_currency_num($invoice.billed_amt, $invoice.actual_billed_currency_id)}
                  </td>
                  <td width="33%">&nbsp; </td>
                </tr>
              </table>
            </td>
          </tr>
           
          <tr valign="top" class="table_background"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=invoice}
                products_ordered 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <!-- Loop through each invoice item record -->
              {foreach from=$cart item=cart}
              <br>
              {if $cart.item_type == "2"}
              <!-- Show domain -->
              <table width="97%" border="0" cellspacing="0" cellpadding="0" class="table_background" align="center">
                <tr> 
                  <td> 
                    <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
                      <tr> 
                        <td width="70%" class="row2" valign="top"> 
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row2">
                            <tr> 
                              <td width="67%" class="row2"><b> </b> 
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                                  <tr> 
                                    <td width="51%"><b> <u> 
                                      {$cart.domain_name|upper}
                                      . 
                                      {$cart.domain_tld|upper}
                                      </u> </b></td>
                                    <td width="37%">&nbsp;</td>
                                    <td width="12%" align="right"><a href="?_page=product:details&id={$cart_assoc.product_id}"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a><a href="?_page=product:details&id={$cart.product_id}"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a></td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            <tr> 
                              <td width="67%"> &nbsp;&nbsp;<b> </b> 
                                {if $cart.sku == "DOMAIN-REGISTER"}
                                {translate module=cart}
                                register 
                                {/translate}
                                {elseif $cart.sku == "DOMAIN-TRANSFER"}
                                {translate module=cart}
                                transfer 
                                {/translate}
                                {elseif $cart.sku == "DOMAIN-PARK"}
                                {translate module=cart}
                                park 
                                {/translate}
                                {elseif $cart.sku == "DOMAIN-RENEW"}
                                {translate module=cart}
                                renew
                                {/translate}
                                {/if} 
                              </td>
                            </tr>
                            {if $cart.cart_type == "1"}
                            {if $cart.host_type == "ns_transfer"}
                            {/if}
                            {if $cart.host_type == "ip"}
                            {/if}
                            {/if}
                          </table>
                        </td>
                        <td width="30%" class="row1" valign="top" align="right"> 
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                            <tr> 
                              <td width="43%">
                                {translate module=cart}
                                base_price 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                <div id="def_base_price_{$cart_assoc.id}"> 
                                  <div id="def_base_price_{$cart.id}"> 
                                    {$list->format_currency_num($cart.price_base, $invoice.actual_billed_currency_id)}
                                  </div>
                                  <div id="base_price_{$cart.id}"></div>
                                </div>
                                <div id="base_price_{$cart_assoc.id}"></div>
                              </td>
                            </tr>
                          </table>
                          {if $cart.sku != 'DOMAIN-PARK'}
                          <select id="quantity_{$cart.id}"  disabled name="select">
                            <option value="">
                            {$cart.domain_term}
                            Year</option>
                          </select>
                          {/if}
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              {elseif $cart.item_type == "3"}
              <!-- Show ad-hoc -->
              <table width="97%" border="0" cellspacing="0" cellpadding="0" class="table_background" align="center">
                <tr> 
                  <td> 
                    <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
                      <tr> 
                        <td width="70%" class="row2" valign="top"> 
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row2">
                            <tr> 
                              <td width="67%" class="row2"><b> </b> 
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                                  <tr> 
                                    <td width="44%"><b> <u> 
                                      {$cart.product_name}
                                      </u> </b></td>
                                    <td width="44%" align="right">( 
                                      {$cart.sku}
                                      )</td>
                                    <td width="12%" align="right"><a href="?_page=product:details&id={$cart_assoc.product_id}"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a><a href="?_page=product:details&id={$cart.product_id}"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a> 
                                      {if $cart.attribute_popup != ""}
									  <div id="attr_panell_1_{$cart.id}" style="display:none">
									  <a href="#" onclick=" new Effect.Fade('attr_panel_1_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Fade('attr_panell_1_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Appear('attr_panel_2_{$cart.id}', {literal}{duration: .5}{/literal}); 
															new Effect.Appear('attr_panell_2_{$cart.id}', {literal}{duration: .5}{/literal}); 
															return false;"> 
                                      <img src="themes/{$THEME_NAME}/images/icons/edit_16.gif" border="0" width="16" height="16"> 
                                      </a> 
									  </div>
									  
									  <div id="attr_panell_2_{$cart.id}" >
									  <a href="#" onclick=" new Effect.Fade('attr_panel_2_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									 						new Effect.Fade('attr_panell_2_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Appear('attr_panel_1_{$cart.id}', {literal}{duration: .5}{/literal}); 
															new Effect.Appear('attr_panell_1_{$cart.id}', {literal}{duration: .5}{/literal}); 
															return false;"> 
                                      <img src="themes/{$THEME_NAME}/images/icons/edit_16.gif" border="0" width="16" height="16"> 
                                      </a> 
									  </div>	
                                      {/if}
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            <tr> 
                              <td width="67%"> &nbsp;&nbsp;<b> </b> 
                                {translate module=cart}
                                price_type_one 
                                {/translate}
                              </td>
                            </tr>
                            {if $cart.cart_type == "1"}
                            {if $cart.host_type == "ns_transfer"}
                            {/if}
                            {if $cart.host_type == "ip"}
                            {/if}
                            {/if}
                          </table>
                        </td>
                        <td width="30%" class="row1" valign="top" align="right"> 
						  <div id="attr_panel_1_{$cart.id}" style="display:none">
						  {$cart.attribute_popup}
						  </div>  
						  <div id="attr_panel_2_{$cart.id}"> 						
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                            <tr> 
                              <td width="43%"> 
                                {translate module=cart}
                                base_price 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                <div id="def_base_price_{$cart_assoc.id}"> 
                                  <div id="def_base_price_{$cart.id}"> 
                                    {$list->format_currency_num($cart.price_base, $invoice.actual_billed_currency_id)}
                                  </div>
                                  <div id="base_price_{$cart.id}"></div>
                                </div>
                                <div id="base_price_{$cart_assoc.id}"></div>
                              </td>
                            </tr>
                            <tr> 
                              <td width="43%"> 
                                {translate module=cart}
                                quantity 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                {$cart.quantity}
                              </td>
                            </tr>
                          </table>
						  </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              {else}
              <!-- Show product -->
              <table width="97%" border="0" cellspacing="0" cellpadding="0" class="table_background" align="center">
                <tr> 
                  <td> 
                    <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
                      <tr> 
                        <td width="70%" class="row2" valign="top"> 
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row2">
                            <tr> 
                              <td width="67%" class="row2"><b> </b> 
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                                  <tr> 
                                    <td width="51%"><b> 
                                      {if $list->translate("product_translate","name", "product_id", $cart.product_id, "translate_product")}
                                      {/if}
                                      <u> 
                                      {$translate_product.name}
                                      </u> </b></td>
                                    <td width="35%"><b>&nbsp;</b>( <a href="?_page=product:details&id={$cart.product_id}"> 
                                      {$cart.sku}
                                      </a> ) &nbsp; </td>
                                    <td width="14%" align="right">  
                                      {if $cart.attribute_popup != ""}
									  <div id="attr_panell_1_{$cart.id}" style="display:none">
									  <a href="#" onclick=" new Effect.Fade('attr_panel_1_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Fade('attr_panell_1_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Appear('attr_panel_2_{$cart.id}', {literal}{duration: .5}{/literal}); 
															new Effect.Appear('attr_panell_2_{$cart.id}', {literal}{duration: .5}{/literal}); 
															return false;"> 
                                      <img src="themes/{$THEME_NAME}/images/icons/edit_16.gif" border="0" width="16" height="16"> 
                                      </a> 
									  </div>
									  
									  <div id="attr_panell_2_{$cart.id}" >
									  <a href="#" onclick=" new Effect.Fade('attr_panel_2_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									 						new Effect.Fade('attr_panell_2_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Appear('attr_panel_1_{$cart.id}', {literal}{duration: .5}{/literal}); 
															new Effect.Appear('attr_panell_1_{$cart.id}', {literal}{duration: .5}{/literal}); 
															return false;"> 
                                      <img src="themes/{$THEME_NAME}/images/icons/edit_16.gif" border="0" width="16" height="16"> 
                                      </a> 
									  </div>	
                                      {/if}
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            <tr> 
                              <td width="67%"> &nbsp;&nbsp;
							  {if $cart.range != ""}
								{$cart.range}   
							  {else}   							  
                                {if $cart.price_type == "0"}
                                {translate module=cart}
                                price_type_one 
                                {/translate}
                                {/if} 
                                {if $cart.price_type == "1"} 
								{translate module=cart}
                                price_type_recurr 
                                {/translate} 
                                {/if} 
                                {if $cart.price_type == "2"}
                                {translate module=cart}
                                price_type_trial 
                                {/translate}
                                {/if}
							   {/if}
                              </td>
                            </tr>
                            <tr> 
                              <td width="67%"> &nbsp;&nbsp; 
                                {if $cart.price_type == "1"}
                                {$list->format_currency_num($cart.price_base, $invoice.actual_billed_currency_id)}
                                {if $cart.recurring_schedule == "0" }
                                {translate module=cart}
                                recurr_week 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "1" }
                                {translate module=cart}
                                recurr_month 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "2" }
                                {translate module=cart}
                                recurr_quarter 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "3" }
                                {translate module=cart}
                                recurr_semianual 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "4" }
                                {translate module=cart}
                                recurr_anual 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "5" }
                                {translate module=cart}
                                recurr_twoyear 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "6" }
                                {translate module=cart}
                                recurr_threeyear 
                                {/translate}
                                {/if}
                                &nbsp;&nbsp; + &nbsp; 
                                {$list->format_currency_num($cart.price_setup, $invoice.actual_billed_currency_id)}
                                {translate module=cart}
                                setup 
                                {/translate}
                                {/if}
                              </td>
                            </tr>
							{if $invoice.type != "1" && $cart.service_id > 0}
							<tr>
							  <td width="67%">&nbsp;&nbsp; 
								{translate module=cart service=$cart.service_id}
								service_upgrade 
								{/translate}
							  </td>
							</tr>
							{/if}							
                            {if $cart.item_type == "1"}
                            {if $cart.domain_type == "ns_transfer"}
                            <tr> 
                              <td width="67%">&nbsp;&nbsp; 
                                {translate module=cart}
                                host_type_domain 
                                {/translate}
                                - <u> 
                                {$cart.domain_name}
                                .
                                {$cart.domain_tld}
                                </u> </td>
                            </tr>
                            {/if}
                            {if $cart.domain_type == "ip"}
                            <tr> 
                              <td width="67%">&nbsp;&nbsp; 
                                {translate module=cart}
                                host_type_ip 
                                {/translate}
                              </td>
                            </tr>
                            {/if}
                            {/if}
                          </table>
                        </td>
                        <td width="30%" class="row1" valign="top"> 
						  <div id="attr_panel_1_{$cart.id}" style="display:none">
						  {$cart.attribute_popup}
						  </div>  
						  <div id="attr_panel_2_{$cart.id}">                           
						  <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                            <tr> 
                              <td width="43%">
                                {translate module=cart}
                                base_price 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                <div id="def_base_price_{$cart.id}"> 
                                  {$list->format_currency_num($cart.price_base, $invoice.actual_billed_currency_id)}
                                </div>
                                <div id="base_price_{$cart.id}"></div>
                              </td>
                            </tr>
                            <tr> 
                              <td width="43%">
                                {translate module=cart}
                                setup_price 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                <div id="def_setup_price_{$cart.id}"> 
                                  {$list->format_currency_num($cart.price_setup, $invoice.actual_billed_currency_id)}
                                </div>
                                <div id="setup_price_{$cart.id}"></div>
                              </td>
                            </tr>
                            <tr> 
                              <td width="43%"> 
                                {translate module=cart}
                                quantity 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                {$cart.quantity}
                              </td>
                            </tr>
                          </table>
						  </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              {/if}
              {/foreach}
              <br>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    
  <center>
  </center>
</form>

{/foreach}
{/if}
{else}
{ $block->display("account:login") }
{/if}

<!-- custom tracking code -->
{ $method->exe("invoice","custom_tracking") }
{/if}
