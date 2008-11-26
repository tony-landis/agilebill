{ $block->display("core:top_clean") }

{$method->exe("cart","admin_view")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}
	{if $results <= 0}
			{translate module=cart}
			empty 
			{/translate} 
    {else}

	{literal}
		<script language="JavaScript">
		<!-- START 
		var http = getHTTPObject();
		
		var acct = '{/literal}{$VAR.account_id}{literal}';
		
		function changeDomainTerm(id,term)
		{
			showIFrame('iframeCart',0,0,'?_page=cart:changeqty&type=3&_escape=1&id='+id+'&term='+term); 			
		}
				
		function changeRecurring(id,schedule)
		{
			showIFrame('iframeCart',0,0,'?_page=cart:admin_changeqty&account_id='+acct+'&type=2&_escape=1&id='+id+'&schedule='+schedule); 			
		}
		
		function changeQuantity(id,qty)
		{ 
			if(qty == "0") qty = 1;					
			showIFrame('iframeCart',0,0,'?_page=cart:admin_changeqty&account_id='+acct+'&type=1&_escape=1&id='+id+'&qty='+qty); 			
		}
		
		function deleteCart(id)
		{
			document.getElementById(id).style.display = 'none';
			showIFrame('iframeCart',0,0,'?_page=cart:admin_changeqty&account_id='+acct+'&_escape=1&id='+id+'&qty=0'); 
		}
		
		function updatePrice(id,base,setup,qty)
		{ 
			document.getElementById("quantity_"+id).value = qty;
			document.getElementById("def_base_price_"+id).style.display='none';
			document.getElementById("base_price_"+id).innerHTML = base;		
			
			if(document.getElementById("def_setup_price_"+id))
			document.getElementById("def_setup_price_"+id).style.display='none';
			
			if(document.getElementById("setup_price_"+id))
			document.getElementById("setup_price_"+id).innerHTML = setup;
		}		
		
		function ad_hoc_discount(id) {
		 var amount = document.getElementById('ad_hoc_discount_'+id).value; 
		 var url = "ajax.php?do[]=checkout:admin_adddiscount&id="+id+"&amount="+amount;
		 http.open("GET", url, true); 
		 http.onreadystatechange = function() {
			if (http.readyState == 4) {  
			  try { eval(http.responseText) } catch(e) {} 
			}
		  } 
		  http.send(null);		
		}		
		//  END -->
		</script>
{/literal}
<!-- LOOP THROUGH EACH RECORD -->
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
                        <td width="37%">&nbsp;</td>
                        <td width="12%" align="right"><a href="?_page=product:details&id={$cart_assoc.product_id}"> 
                          </a><a href="javascript:deleteCart('{$cart.id}');"> 
                          </a><a href="javascript:deleteCart('{$cart.id}');"> 
                          </a><a href="?_page=product:details&id={$cart.product_id}"> 
                          </a><a href="javascript:deleteCart('{$cart.id}');"><img src="themes/{$THEME_NAME}/images/icons/trash_16.gif" border="0" alt="Remove from Cart"></a></td>
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
				<tr> 
					<td width="70%">Ad Hoc Discount</td>
					<td width="30%" valign="middle" align="right"><input onChange="ad_hoc_discount('{$cart.id}');" type="text" size="5" id="ad_hoc_discount_{$cart.id}" value="{$cart.ad_hoc_discount}"></td>
			 	</tr>				
              </table>
              {if $cart.host_type == 'register'}
              <select id="quantity_{$cart.id}"  onChange="changeDomainTerm('{$cart.id}',this.value);" name="select">
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
                        <td width="51%"> <b> 
                          {$cart.ad_hoc_name}
                          </b></td>
                        <td width="35%"><b> </b></td>
                        <td width="14%" align="right"> <a href="?_page=product:admin_details&id={$cart.product_id}&_escape=1"> 
                          </a> <a href="javascript:deleteCart('{$cart.id}');"> 
                          <img title=Delete src="themes/{$THEME_NAME}/images/icons/trash_16.gif" border="0"></a> 
                        </td>
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
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td width="76%" align="right"> 
                          <input type="text" id="quantity_{$cart.id}" name="quantity_{$cart.id}2" size="2"  value="{$cart.quantity}" onChange="changeQuantity('{$cart.id}',this.value);">
                        </td>
                        <td width="24%" valign="middle" align="right"><img src="themes/{$THEME_NAME}/images/icons/calc_16.gif" border="0"></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr> 
                  <td width="70%">Ad Hoc Discount</td>
                  <td width="30%" valign="middle" align="right"><input onChange="ad_hoc_discount('{$cart.id}');" type="text" size="5" id="ad_hoc_discount_{$cart.id}" value="{$cart.ad_hoc_discount}"></td>
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
                          </u></b></td>
                        <td width="35%"><b> </b></td>
                        <td width="14%" align="right"><a href="?_page=product:details&id={$cart.product_id}"> 
                          </a><a href="javascript:deleteCart('{$cart.id}');"><img src="themes/{$THEME_NAME}/images/icons/trash_16.gif" border="0" alt="Remove from Cart"></a> 
                        </td>
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
                    <select id="recurr_schedule_{$cart.id}" name="recurr_schedule_{$cart.id}" onChange="changeRecurring('{$cart.id}',this.value);" >
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
                      {if $price_recurr.setup > 0}
                      &nbsp; + &nbsp; 
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
                {if $cart.service_id > 0}
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
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td width="76%" align="right">&nbsp; 
                           <input type="text" id="quantity_{$cart.id}" name="quantity_{$cart.id}" size="2"  value="{$cart.quantity}" onChange="changeQuantity('{$cart.id}',this.value);" {if ($cart.host_type != "ip" && $cart.host_type != "") || $cart.service_id > 0 }disabled{/if} />
                        </td>
                        <td width="24%" valign="middle" align="right"><img src="themes/{$THEME_NAME}/images/icons/calc_16.gif" border="0"></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr> 
                  <td width="70%">Ad Hoc Discount</td>
                  <td width="30%" valign="middle" align="right"><input onChange="ad_hoc_discount('{$cart.id}');" type="text" size="5" id="ad_hoc_discount_{$cart.id}" value="{$cart.ad_hoc_discount}"></td>
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
                        <td width="37%">&nbsp;</td>
                        <td width="12%" align="right"><a href="?_page=product:details&id={$cart_assoc.product_id}"> 
                          </a><a href="javascript:deleteCart('{$cart.id}');"> 
                          </a></td>
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
              <select id="quantity_{$cart_assoc.id}"  onChange="changeDomainTerm('{$cart_assoc.id}',this.value);" name="select2">
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
<iframe name="iframeCart" id="iframeCart" style="border:0px; width:0px; height:0px;" scrolling="no" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
 
{/if}
{/if}
