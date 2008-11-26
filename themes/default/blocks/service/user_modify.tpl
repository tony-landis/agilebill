{if $SESS_LOGGED == 1}
{$method->exe("service", "user_modify")}

<!-- Show the category drill-down -->
{ if $VAR.id == "" } 
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_heading_cart">
              <tr valign="top"> 
                <td width="78%" > 
                  {translate module=service}
                  modify_select 
                  {/translate}
                </td> 
            </table>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="5" class="row2" >
              <tr valign="top"> 
                <td width="23%" valign="top"> 
                  <form name="form1" method="get" action="">
                    <select name="id">
                      {foreach from=$product_arr item=product}
                      <option value="{$product.id}"> 
                      {if $list->translate("product_translate","name,description_short,description_full","product_id", $product.id, "prod_translate") }
                      {$prod_translate.name}
                      {else}
                      {$product.sku}
                      {/if}
                      - 
                      {$list->format_currency_num($product.price_base,$smarty.const.SESS_CURRENCY)}
                      </option>
                      {/foreach}
                    </select>
                    <input type="submit" name="Submit" value="{translate}submit{/translate}">
                    <input type="hidden" name="_page" value="service:user_modify">
                    <input type="hidden" name="service_id" value="{$VAR.service_id}">
                  </form>
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
{if $product_show}
{ $method->exe("product","details") }
{ if ($method->result == FALSE || !$product) }
{ $block->display("core:method_error") }
{else}
{if $product}
<form id="product_view" name="product_view" method="post" action="">
  <input type="hidden" name="service_id" value="{$VAR.service_id}">
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
							
							{if $waive_setup != 1}
							{if $price_recurr.setup > 0}						
                            &nbsp; + &nbsp; 
                            {$list->format_currency_num($price_recurr.setup,$smarty.const.SESS_CURRENCY)}
                            {translate module=product}
                            setup
                            {/translate}
							{/if}{/if}
                            </option>
                            {/foreach}
                          </select>
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
                      {if $attr_arr.description}
                      <tr valign="top"> 
                        <td width="96%">  
                          <input type="checkbox" name="attr[{$attr_id}]" value="Yes" {if $VAR.attr[$attr_id] || $attr_arr.default}checked{/if} >
                          &nbsp; 
                          {$attr_arr.description}
                        </td>
                      </tr>
                      {/if}
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
                      {if $attr_arr.description}
                      <tr valign="top"> 
                        <td width="96%"> 
                          <input type="text" id="attr_{$attr_id}" name="attr[{$attr_id}]" size="20" >
                          &nbsp;
                          {$attr_arr.description}
                        </td>
                      </tr>
                      {/if}
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
                      {if $attr_arr.description}
                      <tr valign="top"> 
                        <td width="96%">  
                          <textarea id="attr_{$attr_id}" name="attr[{$attr_id}]2" cols="50" rows="3"></textarea>
                          &nbsp; 
                          {$attr_arr.description}
                        </td>
                      </tr>
                      {/if}
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
                      {if $attr_arr.description}
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
                      {/if}
                    </table>
                    {/if}
                    {/foreach}
                    {/if}
                    {literal}
                    <!-- Define the update delete function -->
                    <script language="JavaScript">
					 
					function addCart(addtype)
					{ 
						var hosting = '{/literal}{$product.host}{literal}';
						if  (hosting == "1")
						{   			 
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
						doCart(addtype);
					} 
					function doCart(addtype)
					{
						if(addtype == 'checkout')
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
  <input type="submit" onClick="addCart('checkout')" name="modify" value="{translate module=service}modify_submit{/translate}" class="form_button">
</p>
{/if}
{/if}
{/if}
{else}
{$block->display("account:login")}
{/if}
