{ $method->exe("product","details") } { if ($method->result == FALSE || !$product) } { $block->display("core:method_error") } {else}
{if $product}
 
<form id="product_view" name="product_view" method="post" action="">
  <input type="hidden"  id="page"  name="_page"  value="">
  <input type="hidden"  name="do[]"  value="cart:add">
  <input type="hidden"  name="product_id"  value="{$product.id}">
  <input type="hidden" name="s" value="{$SESS}">
  {if $list->translate("product_translate","name,description_full", "product_id", $product.id, "translate_product")}
  {/if} 
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_heading_cart">
                <tr valign="top"> 
                  <td width="78%" > 
                    <p> 
                      {if $list->translate("product_translate","name,description_short,description_full","product_id", $product.id, "prod_translate") }
                      {$prod_translate.name}
                      {else}
                      {$product.sku}
                      {/if}
                    </p>
                  </td>
                  <td width="22%" valign="middle" align="right"> <b> </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="body" bgcolor="#FFFFFF"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
                <tr valign="top"> 
                  <td width="92%" align="left" bgcolor="#FFFFFF"> 
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="top" class="body"> 
                        <td width="50%">
                          {if $product.thumbnail != ""}
                          <img  src="{$URL}{$smarty.const.URL_IMAGES}{$product.thumbnail}" hspace="5" border="0"> 
                          {/if}
                          {$translate_product.description_full}
                        </td>
                      </tr>
                    </table> 
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="92%" align="left">
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="top" class="body"> 
                        <td width="50%"> <b> 
                          {translate module=product}
                          field_sku 
                          {/translate}
                          </b> </td>
                        <td width="50%"><b> 
                          {translate}
                          currency 
                          {/translate}
                          </b></td>
                      </tr>
                      <tr valign="top" class="body"> 
                        <td width="50%"> 
                          {$product.sku}
                        </td>
                        <td width="50%" valign="middle"> 
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr> 
                              <td width="26%"> 
                                {literal}
                                <script language="JavaScript"> function CurrencyChange(obj) { document.location='?_page=product:details&id={/literal}{$VAR.id}{literal}&cyid='+obj.value; } </script>
                                {/literal}
                                {$list->currency_list("cyid_arr")}
                                <select name="cyid"  onChange="CurrencyChange(this);">
                                  {foreach key=key item=item from=$cyid_arr}
                                  <option value="{$key}" {if $key == $smarty.const.SESS_CURRENCY}{assign var=currency_thumbnail value=$item.iso}selected{/if}> 
                                  {$item.iso}
                                  </option>
                                  {/foreach}
                                </select>
                              </td>
                              <td width="74%"> <img src="themes/{$THEME_NAME}/images/currency/{$currency_thumbnail}.gif" border="0"> 
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="top" class="body"> 
                        <td width="50%"> <b> 
                          {translate module=product}
                          field_price_type 
                          {/translate}
                          </b><br>
                        </td>
                        <td width="50%"> <b> 
                          {translate module=product}
                          field_taxable 
                          {/translate}
                          </b> </td>
                      </tr>
                      <tr valign="top"> 
                        <td width="50%"> 
                          {if $product.price_type == "0"}
                          {translate module=product}
                          price_type_one 
                          {/translate}
                          {/if}
                          {if $product.price_type == "1"}
                          {translate module=product}
                          price_type_recurr 
                          {/translate}
                          {/if}
                          {if $product.price_type == "2"}
                          {translate module=product}
                          price_type_trial 
                          {/translate}
                          {/if}
                        </td>
                        <td width="50%"> 
                          {if $product.taxable}
                          {translate}
                          true 
                          {/translate}
                          {else}
                          {translate}
                          false 
                          {/translate}
                          {/if}
                        </td>
                      </tr>
                    </table>
                    {if $product.price_type == "1" }
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="top" class="body"> 
                        <td width="50%"><b> 
                          {translate module=product}
                          field_price_recurr_type 
                          {/translate}
                          </b></td>
                      </tr>
                      <tr valign="top" class="body"> 
                        <td width="50%"> 
                          <select name="recurr_schedule" >
                            {foreach from=$price item=price_recurr key=key}
                            <option value="{$key}" {if $product.price_recurr_default == $key} selected{/if}> 
                            {$list->format_currency_num($price_recurr.base,$smarty.const.SESS_CURRENCY)}
                            &nbsp; 
                            {if $key == "0" }
                            {translate module=product}
                            recurr_week 
                            {/translate}
                            {/if}
                            {if $key == "1" }
                            {translate module=product}
                            recurr_month 
                            {/translate}
                            {/if}
                            {if $key == "2" }
                            {translate module=product}
                            recurr_quarter 
                            {/translate}
                            {/if}
                            {if $key == "3" }
                            {translate module=product}
                            recurr_semianual 
                            {/translate}
                            {/if}
                            {if $key == "4" }
                            {translate module=product}
                            recurr_anual 
                            {/translate}
                            {/if}
                            {if $key == "5" }
                            {translate module=product}
                            recurr_twoyear 
                            {/translate}
                            {/if}
                            {if $key == "6" }
                            {translate module=product}
                            recurr_threeyear 
                            {/translate}
                            {/if}
							
							{if $price_recurr.setup > 0}						
                            &nbsp; + &nbsp; 
                            {$list->format_currency_num($price_recurr.setup,$smarty.const.SESS_CURRENCY)}
                            {translate module=product}
                            setup
                            {/translate}
							{/if}
                            </option>
                            {/foreach}
                          </select>
                        </td>
                      </tr>
                    </table>
                    {else}
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="top" class="body"> 
                        <td width="50%"> <b> 
                          {translate module=product}
                          field_price_base 
                          {/translate}
                          </b> </td>
                        <td width="50%"> <b> 
                          {translate module=product}
                          field_price_setup 
                          {/translate}
                          </b> </td>
                      </tr>
                      <tr valign="top"> 
                        <td width="50%"> 
                          {$list->format_currency($price.base, $smarty.const.SESS_CURRENCY)}
                        </td>
                        <td width="50%"> 
                          {$list->format_currency($price.setup, $smarty.const.SESS_CURRENCY)}
                        </td>
                      </tr>
                    </table>
                    {/if}
                    {if $product.price_type == "2"}
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="top"> 
                        <td width="76%"> 
                          {translate module=product sku=$trial.sku}
                          trial_desc 
                          {/translate}
                          <a href="?_page=product:details&id={$product.price_trial_prod}"> 
                          {translate}
                          view 
                          {/translate}
                          </a><br>
                          {if $product.price_trial_length_type == "0"}
                          {translate module=product sku1=$trial.sku length=$product.price_trial_length}
                          trial_length_days 
                          {/translate}
                          {/if}
                          {if $product.price_trial_length_type == "1"}
                          {translate module=product sku1=$trial.sku length=$product.price_trial_length}
                          trial_length_weeks 
                          {/translate}
                          {/if}
                          {if $product.price_trial_length_type == "2"}
                          {translate module=product sku1=$trial.sku length=$product.price_trial_length}
                          trial_length_months 
                          {/translate}
                          {/if}
                          <br>
                          {translate module=product  sku2=$trial.sku}
                          trial_bill_desc 
                          {/translate}
                        </td>
                      </tr>
                    </table>
                    {/if}
                    {if $attr}
                    <br>
                    {foreach from=$attr item=attr_arr key=key}
                    {assign var=attr_id value=$attr_arr.id}
                    {if $attr_arr.type == "0"}
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="middle" class="body"> 
                        <td width="96%"> <b> </b> 
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="body">
                            <tr> 
                              <td width="70%" valign="middle"><b> 
                                {$attr_arr.name}
                                </b></td>
                              <td width="30%" valign="middle" align="right"> 
                                {if $attr_arr.price_base != 0}
                                {$list->format_currency_num($attr_arr.price_base, $smarty.const.SESS_CURRENCY)}
                                {/if}
                                {if $attr_arr.price_setup != 0}
                                {if $attr_arr.price_base != 0}
                                + 
                                {/if}
                                {$list->format_currency_num($attr_arr.price_setup, $smarty.const.SESS_CURRENCY)}
                                {translate module=product}
                                setup 
                                {/translate}
                                {/if}
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr> 
                      <tr valign="top"> 
                        <td width="96%">  
                          <input type="checkbox" name="attr[{$attr_id}]" value="Yes" {if $VAR.attr[$attr_id] || $attr_arr.default}checked{/if} >
                          &nbsp; 
                          {$attr_arr.description}
                        </td>
                      </tr> 
                    </table>
                    {elseif $attr_arr.type == "1"}
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="middle" class="body"> 
                        <td width="96%"> <b> </b> 
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="body">
                            <tr> 
                              <td width="70%" valign="middle"><b> 
                                {$attr_arr.name}
                                </b></td>
                              <td width="30%" valign="middle" align="right"> 
                                {if $attr_arr.price_base != 0}
                                {$list->format_currency_num($attr_arr.price_base, $smarty.const.SESS_CURRENCY)}
                                {/if}
                                {if $attr_arr.price_setup != 0}
                                {if $attr_arr.price_base != 0}
                                + 
                                {/if}
                                {$list->format_currency_num($attr_arr.price_setup, $smarty.const.SESS_CURRENCY)}
                                {translate module=product}
                                setup 
                                {/translate}
                                {/if}
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr> 
                      <tr valign="top"> 
                        <td width="96%"> 
                          <input type="text" id="attr_{$attr_id}" name="attr[{$attr_id}]" size="20" >
                          &nbsp;
                          {$attr_arr.description}
                        </td>
                      </tr> 
                    </table>
                    {elseif $attr_arr.type == "3"}
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="middle" class="body"> 
                        <td width="96%"> <b> </b> 
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="body">
                            <tr> 
                              <td width="70%" valign="middle"><b> 
                                {$attr_arr.name}
                                </b></td>
                              <td width="30%" valign="middle" align="right"> 
                                {if $attr_arr.price_base != 0}
                                {$list->format_currency_num($attr_arr.price_base, $smarty.const.SESS_CURRENCY)}
                                {/if}
                                {if $attr_arr.price_setup != 0}
                                {if $attr_arr.price_base != 0}
                                + 
                                {/if}
                                {$list->format_currency_num($attr_arr.price_setup, $smarty.const.SESS_CURRENCY)}
                                {translate module=product}
                                setup 
                                {/translate}
                                {/if}
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr> 
                      <tr valign="top"> 
                        <td width="96%">  
                          <textarea id="attr_{$attr_id}" name="attr[{$attr_id}]2" cols="50" rows="3"></textarea>
                          &nbsp; 
                          {$attr_arr.description}
                        </td>
                      </tr> 
                    </table>
                    {elseif $attr_arr.type == "2"}
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="middle" class="body"> 
                        <td width="96%"> <b> </b> 
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="body">
                            <tr> 
                              <td width="70%" valign="middle"><b> 
                                {$attr_arr.name}
                                </b></td>
                              <td width="30%" valign="middle" align="right">  
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr> 
                      <tr valign="top"> 
                        <td width="96%"> 
						<select id="attr_{$attr_id}" name="attr[{$attr_id}]" >
                            {foreach from=$attr_arr.default item=attr_menu key=attr_key}
                         
                            <option value="{$attr_menu.name}">
                            {$attr_menu.name}
							{if $attr_menu.base > 0}: {$list->format_currency_num($attr_menu.base, $smarty.const.SESS_CURRENCY)}
							{/if}
							{if $attr_menu.base > 0 && $attr_menu.setup > 0} 
							 + 
							{elseif $attr_menu.base <= 0 && $attr_menu.setup > 0}: 
							{/if}
							{if $attr_menu.setup > 0}
							{$list->format_currency_num($attr_menu.setup, $smarty.const.SESS_CURRENCY)}
                            {translate module=product}
                                setup 
                            {/translate}							
							{/if}							                            
							</option> 
                            {/foreach}
                          </select>
                          &nbsp; 
                          {$attr_arr.description}
                        </td>
                      </tr> 
                    </table>
                    {/if}
                    {/foreach}
                    {/if}
                    {if $product.host}
                    <br>
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                      <tr valign="middle" class="body"> 
                        <td width="96%"> <b> </b> 
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="body">
                            <tr> 
                              <td width="70%" valign="middle"><b> 
                                {translate module=product}
                                domain_options 
                                {/translate}
                                </b></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr valign="top"> 
                        <td width="96%"> 
                          {if $product.host_allow_domain}
                          <input type="radio" id="register" name="domain_type" value="register" onClick="domainUpdate('0','0','register'); showIFrame('iframeDomainDetails',480,95,'?_page=host_tld:iframe_register&_escape=1');">
                          {translate module=product}
                          domain_register 
                          {/translate}
                          <br>
                          <input type="radio" id="transfer" name="domain_type" value="transfer" onClick="domainUpdate('0','0','transfer'); showIFrame('iframeDomainDetails',480,110,'?_page=host_tld:iframe_transfer&_escape=1');">
                          {translate module=product}
                          domain_transfer 
                          {/translate}
                          {/if}
                          <br>
                          <input type="radio" id="ns_transfer" name="domain_type" value="ns_transfer" onClick="domainUpdate('0','0','ns_transfer'); showIFrame('iframeDomainDetails',480,70,'?_page=host_tld:iframe_ns_transfer&_escape=1');">
                          {translate module=product}
                          domain_ns_transfer 
                          {/translate}
                          {if $product.host_allow_host_only}
                          <br>
                          <input type="radio" id="ip" name="domain_type" value="ip" onClick="domainUpdate('0','0','ip'); hideIFrame('iframeDomainDetails');">
                          {translate module=product}
                          domain_ip 
                          {/translate}
                          <br>
                          {/if}
                          <input type="hidden"  id="domain_name"    name="domain_name"    value="0">
                          <input type="hidden"  id="domain_tld"     name="domain_tld"     value="0">
                          <input type="hidden"  id="domain_option"  name="domain_option"  value="0">
                          <iframe name="iframeDomainDetails" id="iframeDomainDetails" style="border:0px; width:100%; height:0px;" scrolling="no" width="100%" allowtransparency="false" frameborder="0" class="body" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"><br></iframe>
						 </td>
                      </tr>
                    </table>
                    {/if}
                    {literal}
                    <!-- Define the update delete function -->
                    <script language="JavaScript">
					<!-- START 
					function domainUpdate(domain,tld,type)
					{
						document.getElementById("domain_name").value   = domain;
						document.getElementById("domain_tld").value    = tld;
						document.getElementById("domain_option").value = type;
					} 
					function addCart(addtype)
					{ 
						var hosting = '{/literal}{$product.host}{literal}';
						if  (hosting == "1")
						{  
							var domain_option=document.getElementById("domain_option").value; 
							var domain_name   =document.getElementById("domain_name").value;
							var domain_tld	  =document.getElementById("domain_tld").value;
				
							if(domain_option == "0")
							{
								alert("{/literal}{translate module=product}host_domain_opt{/translate}{literal}");
								return;
							} 
									 
							if(domain_name == "0" || domain_tld == "0")
							{
								if(domain_option != "ip")
								{
									alert("{/literal}{translate module=product}host_domain_inv{/translate}{literal}");
									return;
								}
							} 			 
						} 
						attrValidate(addtype); 
					} 
					function attrValidate(addtype)
					{ 

						var val_arr = new Array(2);
						var i=0;
						{/literal} {foreach from=$attr item=attr_arr key=key} {assign var=attr_id value=$attr_arr.id}{if $attr_arr.required == "1"}
						val_arr[i] = new Array ('attr_{$attr_id}','{$attr_arr.name}','{$attr_arr.type}'); 
						i++;
						{/if}{/foreach} {literal} 
						for(ii=0; ii < i; ii++)
						{
							if(!document.getElementById(val_arr[ii][0]).value)
							{ 
								document.getElementById(val_arr[ii][0]).focus();  				
								if(val_arr[ii][2] == "1") {
									alert("You must select an option for \""+val_arr[ii][1]+"\"");
								} else { 
									alert("You must enter a value for the product option \""+val_arr[ii][1]+"\"")  
								} 
								return false;
							}
						}

						// product plugin validation:
						var val_plugin=false;
						try{ val_plugin = product_plugin_validate(); } catch(e) { val_plugin = true;  } 
						if(val_plugin) doCart(addtype);
					} 
					function doCart(addtype)
					{
						if(addtype == 'cart')
						{
							document.getElementById('page').value = 'cart:cart';
							document.getElementById('product_view').action = '{/literal}{$URL}{literal}';
						}
						else if(addtype == 'checkout')
						{
							document.getElementById('page').value = 'checkout:checkout';
							document.getElementById('product_view').action = '{/literal}{$SSL_URL}{literal}';						
						} 
						document.product_view.submit();
					}  
					//  END -->
					</script>
                    {/literal}
					{/if}
                  </td>
                </tr>
				
				{if $product.cart_multiple}
                <tr valign="top">
                  <td align="left"><strong>Quantity:</strong> <input name="quantity" type="text" id="quantity" value="1" size="3" maxlength="3"></td>
                </tr>
				{/if}
				
				{if $product.prod_plugin}
                <tr valign="top">
                  <td align="left"> 
				  {plugin type=product name=$product.prod_plugin_file name_prefix=order_ data=$product.prod_plugin_data } 
				  </td>
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
</form> 
<p align="center"> 
  <input type="submit" onClick="addCart('cart')" name="cart2" value="{translate module=product}cart{/translate}" class="form_button">
  <input type="submit" onClick="addCart('checkout')" name="checkout2" value="{translate module=product}checkout{/translate}" class="form_button">
</p>
{/if}