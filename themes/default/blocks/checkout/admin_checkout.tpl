{ $block->display("core:top_clean") }
{$method->exe("checkout","admin_preview")}
{if ($method->result == FALSE)}
	{$block->display("core:method_error")}
{else}
	{if $results == 0}
	{translate module=cart}
	empty 
	{/translate}  
{else}
{if $SESS_LOGGED != "1"}
{ $block->display("account:login")  }
{else}
  
<!-- LOOP THROUGH EACH RECORD -->
<div id="cart_items" {style_hide}>
{foreach from=$cart item=cart}
<DIV id="{$cart.id}"> 
  {if $cart.cart_type == "2"}
  <!-- Show domain -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
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
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr> 
                  <td width="67%"> &nbsp;&nbsp;<b> 
                    {if $cart.host_type == "register"}
                    {translate module=cart}
                    register 
                    {/translate}
                    {elseif $cart.host_type == "transfer"}
                    {translate module=cart}
                    transfer 
                    {/translate}
                    {elseif $cart.host_type == "park"}
                    {translate module=cart}
                    park 
                    {/translate}
                    {/if}
                    </b></td>
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
                  <td width="70%"> 
                    {translate module=cart}
                    base_price 
                    {/translate}
                  </td>
                  <td width="30%" valign="middle" align="right"> 
                    <div id="def_base_price_{$cart_assoc.id}"> 
                      <div id="def_base_price_{$cart.id}"> 
                        {$list->format_currency_num($cart.price, $smarty.const.SESS_CURRENCY)}
                      </div>
                      <div id="base_price_{$cart.id}"></div>
                    </div>
                    <div id="base_price_{$cart_assoc.id}"></div>
                  </td>
                </tr>
              </table>
              {if $cart.host_type == 'register'}
              <select id="quantity_{$cart.id}"  disabled name="select">
                {foreach from=$cart.tld_arr item=tld_price key=tld_term}
                <option value="{$tld_term}" {if $tld_term == $cart.domain_term}selected{/if}> 
                {$tld_term}
                Year 
                {$list->format_currency($tld_price, $smarty.const.SESS_CURRENCY)}
                </option>
                {/foreach}
              </select>
              {/if}
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  {elseif $cart.cart_type == "3"}
  <!-- Show ad-hoc item -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
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
                          {$cart.ad_hoc_name}
                          </u> </b></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr> 
                  <td width="67%"> &nbsp;&nbsp;<b> 
                    {translate module=cart}
                    price_type 
                    {/translate}
                    : </b> 
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
            <td width="30%" class="row1" valign="top"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                <tr> 
                  <td width="70%"> 
                    {translate module=cart}
                    base_price 
                    {/translate}
                  </td>
                  <td width="30%" valign="middle" align="right"> 
                    <div id="def_base_price_{$cart.id}"> 
                      {$list->format_currency_num($cart.price_base, $smarty.const.SESS_CURRENCY)}
                    </div>
                    <div id="base_price_{$cart.id}"></div>
                  </td>
                </tr>
                <tr> 
                  <td width="70%"> 
                    {translate module=cart}
                    quantity 
                    {/translate}
                  </td>
                  <td width="30%" valign="middle" align="right"> 
                    {$cart.quantity}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  {else}
  <!-- Show product -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
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
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr> 
                  <td width="67%"> &nbsp;&nbsp;<b> 
                    {translate module=cart}
                    price_type 
                    {/translate}
                    : </b> 
                    {if $cart.product.price_type == "0"}
                    {translate module=cart}
                    price_type_one 
                    {/translate}
                    {/if}
                    {if $cart.product.price_type == "1"}
                    {translate module=cart}
                    price_type_recurr 
                    {/translate}
                    {/if}
                    {if $cart.product.price_type == "2"}
                    {translate module=cart}
                    price_type_trial 
                    {/translate}
                    {/if}
                  </td>
                </tr>
                <tr> 
                  <td width="67%"> 
                    {if $cart.product.price_type == "1"}
                    &nbsp;&nbsp; 
                    <select id="recurr_schedule_{$cart.id}" name="recurr_schedule_{$cart.id}"  disabled>
                      {foreach from=$cart.price item=price_recurr key=key}
                      <option value="{$key}" {if $cart.recurr_schedule == $key} selected{/if}> 
                      {$list->format_currency_num($price_recurr.base,$smarty.const.SESS_CURRENCY)}
                      &nbsp; 
                      {if $key == "0" }
                      {translate module=cart}
                      recurr_week 
                      {/translate}
                      {/if}
                      {if $key == "1" }
                      {translate module=cart}
                      recurr_month 
                      {/translate}
                      {/if}
                      {if $key == "2" }
                      {translate module=cart}
                      recurr_quarter 
                      {/translate}
                      {/if}
                      {if $key == "3" }
                      {translate module=cart}
                      recurr_semianual 
                      {/translate}
                      {/if}
                      {if $key == "4" }
                      {translate module=cart}
                      recurr_anual 
                      {/translate}
                      {/if}
                      {if $key == "5" }
                      {translate module=cart}
                      recurr_twoyear 
                      {/translate}
                      {/if}
                      {if $key == "6" }
                      {translate module=cart}
                      recurr_threeyear 
                      {/translate}
                      {/if}
                      {if $price_recurr.setup > 0 }
                      &nbsp;&nbsp; + &nbsp; 
                      {$list->format_currency_num($price_recurr.setup,$smarty.const.SESS_CURRENCY)}
                      {translate module=cart}
                      setup 
                      {/translate}
                      {/if}
                      </option>
                      {/foreach}
                    </select>
                    {/if}
                  </td>
                </tr>
                {if $cart.service_id != "" && $cart.service_id > 0}
                <tr> 
                  <td width="67%">&nbsp;&nbsp; 
                    {translate module=cart service=$cart.service_id}
                    service_upgrade 
                    {/translate}
                  </td>
                </tr>
                {/if}
                {if $cart.cart_type == "1"}
                {if $cart.host_type == "ns_transfer"}
                <tr> 
                  <td width="67%">&nbsp;&nbsp; 
                    {translate module=cart}
                    host_type_domain 
                    {/translate}
                    - <u> 
                    {$cart.domain_name}
                    .
                    {$cart.domain_tld}
                    </u></td>
                </tr>
                {/if}
                {if $cart.host_type == "ip"}
                <tr> 
                  <td width="67%">&nbsp;&nbsp; 
                    {translate module=cart}
                    host_type_ip 
                    {/translate}
                  </td>
                </tr>
                {/if}
                {/if}
				{if $cart.attr}
				<tr>
					<td width="67%"> {$cart.attr} </td>
				</tr>
				{/if}					
              </table>
            </td>
            <td width="30%" class="row1" valign="top"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                <tr> 
                  <td width="70%"> 
                    {translate module=cart}
                    base_price 
                    {/translate}
                  </td>
                  <td width="30%" valign="middle" align="right"> 
                    <div id="def_base_price_{$cart.id}"> 
                      {$list->format_currency_num($cart.price_base, $smarty.const.SESS_CURRENCY)}
                    </div>
                    <div id="base_price_{$cart.id}"></div>
                  </td>
                </tr>
                <tr> 
                  <td width="70%"> 
                    {translate module=cart}
                    setup_price 
                    {/translate}
                  </td>
                  <td width="30%" valign="middle" align="right"> 
                    <div id="def_setup_price_{$cart.id}"> 
                      {$list->format_currency_num($cart.price_setup, $smarty.const.SESS_CURRENCY)}
                    </div>
                    <div id="setup_price_{$cart.id}"></div>
                  </td>
                </tr>
                <tr> 
                  <td width="70%"> 
                    {translate module=cart}
                    quantity 
                    {/translate}
                  </td>
                  <td width="30%" valign="middle" align="right"> 
                    {$cart.quantity}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  {/if}
  <br>
  {foreach from=$cart.assoc item=cart_assoc}
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
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
                          {$cart_assoc.domain_name|upper}
                          . 
                          {$cart_assoc.domain_tld|upper}
                          </u> </b></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr> 
                  <td width="67%"> &nbsp;&nbsp;<b> </b> 
                    {if $cart_assoc.host_type == "register"}
                    {translate module=cart}
                    host_type_register 
                    {/translate}
                    {elseif $cart_assoc.host_type == "transfer"}
                    {translate module=cart}
                    host_type_transfer 
                    {/translate}
                    {elseif $cart_assoc.host_type == "park"}
                    {translate module=cart}
                    host_type_park 
                    {/translate}
                    {/if}
                    {$cart.product.sku}
                  </td>
                </tr>
              </table>
            </td>
            <td width="30%" class="row1" valign="top" align="right"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                <tr> 
                  <td width="70%"> 
                    {translate module=cart}
                    base_price 
                    {/translate}
                  </td>
                  <td width="30%" valign="middle" align="right"> 
                    <div id="def_base_price_{$cart_assoc.id}"> 
                      {$list->format_currency_num($cart_assoc.price, $smarty.const.SESS_CURRENCY)}
                    </div>
                    <div id="base_price_{$cart_assoc.id}"></div>
                  </td>
                </tr>
              </table>
              {if $cart_assoc.host_type == 'register'}
              <select id="quantity_{$cart_assoc.id}"  disabled name="select">
                {foreach from=$cart_assoc.tld_arr item=tld_price key=tld_term}
                <option value="{$tld_term}" {if $tld_term == $cart_assoc.domain_term}selected{/if}> 
                {$tld_term}
                Year 
                {$list->format_currency($tld_price, $smarty.const.SESS_CURRENCY)}
                </option>
                {/foreach}
              </select>
              {/if}
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
<br> 
{/foreach}
</DIV>
{/foreach}
</div>
<!-- END OF RESULT LOOP -->

 <p id="cart_items_show"><a href="javascript:void(0);" onclick="javascript:getElementById('cart_items').style.display='block'; getElementById('cart_items_show').style.display='none';">{translate module=cart}view_items{/translate}</a></p>

<!-- CURRENCY OPTIONS & DISCOUNT CODE ENTRY -->
<table width="100%" border="0" cellspacing="0" cellpadding="3" class="body">
  <tr> 
    <td width="33%" align="left" valign="top">  
      <form name="adminadd" method="post" action="">
        <table width="200" border="0" cellspacing="3" cellpadding="1" class="row1">
          <tr valign="top" class="row2">
            <td width="45%">Send E-mails? </td>
            <td width="55%">
			<input name="email" id="email" type="checkbox" value="1" checked></td>
          </tr>
          <tr valign="top" class="row2">
            <td>Due Date </td>
            <td>{$list->calender_add("due_date", '', '', '') }</td>
          </tr>
          <tr valign="top" class="row2">
            <td>Grace Period</td>
            <td><input name="grace_period" id="grace_period" type="text" value="{$smarty.const.GRACE_PERIOD}" size="4">
              days</td>
          </tr>
          <tr valign="top" class="row2">
            <td>Max Notices</td>
            <td><input name="notice_max" id="notice_max" type="text"  value="{$smarty.const.MAX_BILLING_NOTICE}" size="4">
              days</td>
          </tr>			    
          <tr valign="top" class="row2">
            <td colspan="2"> {literal}
                <script language="JavaScript"> 
				function doAdminCheckout(option)  
				{  
					var email=document.getElementById('email').checked; 
					if(email) email='true';	 else email = 'false';				
					var due_date=document.forms[0].due_date.value; 
					var grace_period=document.getElementById('grace_period').value;
					var notice_max=document.getElementById('notice_max').value;  
					var adminurl ='?_page=core:blank&do[]=checkout:admin_checkoutnow&option='+option+'&account_id={/literal}{$VAR.account_id}{literal}&send_email='+email+'&due_date='+due_date+'&grace_period='+grace_period+'&notice_max='+notice_max;  
					document.location=adminurl;
				} 
			  </script>
      {/literal} <b><a href="javascript:doAdminCheckout('999');">{translate module=cart}admin_create_invoice{/translate}</a></b></td>
          </tr>
        </table>
      </form>
      <br>    </td>
    <td width="27%" valign="top" align="center">
     <table width="150" border="0" cellspacing="3" cellpadding="1" class="row1">
        <tr valign="top" class="row2"> 
          <td width="50%"><b> 
            {translate}
            currency 
            {/translate}
            </b></td>
        </tr>
        <tr valign="top" class="row1"> 
          <td width="50%" valign="middle"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="78%"> 
                  {literal}
                  <script language="JavaScript"> function CurrencyChange(obj) { document.location='?_page=checkout:admin_checkout&account_id={/literal}{$VAR.account_id}{literal}&_escape=1&cyid='+obj.value; } </script>
                  {/literal}
                  {$list->currency_list("cyid_arr")}
                  <select name="currency"  onChange="CurrencyChange(this);">
                    {foreach key=key item=item from=$cyid_arr}
                    <option value="{$key}" {if $key == $smarty.const.SESS_CURRENCY}{assign var=currency_thumbnail value=$item.iso}selected{/if}> 
                    {$item.iso}
                    </option>
                    {/foreach}
                  </select>
                </td>
                <td width="22%"> <img src="themes/{$THEME_NAME}/images/currency/{$currency_thumbnail}.gif" border="0"> 
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>	
	</td>
    <td width="40%" valign="top" align="right"> 
      <table width="225" border="0" cellspacing="3" cellpadding="1" class="row1" align="right">
        <tr valign="top" class="row2"> 
          <td width="50%"><b> 
            {translate module=checkout}
            totals 
            {/translate}
            </b></td>
        </tr>
        <tr valign="top" class="row1"> 
          <td width="50%" valign="middle"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
              <tr> 
                <td width="63%"> 
                  {translate module=cart}
                  subtotal 
                  {/translate}
                </td>
                <td width="37%" align="right"> 
                  {$list->format_currency_num($sub_total, $smarty.const.SESS_CURRENCY)}
                </td>
              </tr>
              {foreach from=$discount item=discount}
              {if $discount.total > 0}
              <tr> 
                <td width="63%"> 
                  {translate module=cart}
                  discount 
                  {/translate}
                  ( 
                  {$discount.name}
                  ) </td>
                <td width="37%" align="right"> 
                  {$list->format_currency_num($discount.total, $smarty.const.SESS_CURRENCY)}
                </td>
              </tr>
              {/if}
              {/foreach} 
              {if $tax != false}			  
			  {foreach from=$tax item=tax } 
              <tr> 
                <td width="63%"> 
                  {$tax.name}
                </td>
                <td width="37%" align="right"> 
                  {$list->format_currency_num($tax.rate, $smarty.const.SESS_CURRENCY)}
                </td>
              </tr>
			  {/foreach}
              {/if} 
              <tr> 
                <td width="63%"><b>
                  {translate module=cart}
                  total 
                  {/translate}
                  </b></td>
                <td width="37%" align="right"><b> 
                  {$list->format_currency_num($total, $smarty.const.SESS_CURRENCY)}
                  </b></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br> 


<script language="javascript">var confirmCheckoutMsg='{translate module=checkout}redirect{/translate}';</script>
<script src="themes/default/blocks/checkout/ajax.js" type="text/javascript"></script>

<!-- CHECKOUT CONFIRM -->
<div id="checkout_confirm_div">
	{if $VAR.option!=''}{$method->exe("checkout","checkoutoption")}{if $plugin_template != false}{$block->display($plugin_template)}{/if}{/if}
</div>

<p id="checkout_options_show" {style_hide}>
<a href="#" onClick="document.getElementById('checkout_confirm_div').style.display='none';document.getElementById('checkout_options_show').style.display='none';document.getElementById('checkout_options').style.display='block';">View More Payment Options</a>
</p> 

<!-- CHECKOUT OPTIONS --> 
<div id="checkout_options">
 <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="1" cellpadding="2" class="body"> 
      		{if $checkout}
      		{foreach from=$checkout item=checkout key=key} 			  
			  <tr valign="top"> 
                <td width="20%"><a href="javascript:void(0);" onClick="changeCheckoutOption({$checkout.fields.id},'checkout',false,{$VAR.account_id})">{$checkout.fields.name}</a></td>
                <td width="80%">{$checkout.fields.description}</td>
              </tr>
     		{/foreach}
      		{else} 
			  <tr valign="top">  
                <td>{translate module=cart}no_checkout_options{/translate}</td>
              </tr>				
      		{/if}	 
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table> 
<br>
</div>
  
<!-- SINGLE CHECKOUT OPTIONS -->
{if $VAR.option=='' && $checkout_c == 1}
  <script language=javascript>changeCheckoutOption(last_checkout_id);</script>
{elseif $VAR.option>0} 
  <script language=javascript>
    {literal}
    try { document.getElementById('checkout_options_show').style.display='block'; } catch(e) {}  
	try { document.getElementById('checkout_options').style.display='none'; } catch(e) {}   
	{/literal}
  </script>
{/if}

 
{/if}
{/if}
{/if}
