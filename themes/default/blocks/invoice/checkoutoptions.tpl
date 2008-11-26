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
          <td width="65%"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr valign="top"> 
                <td class="table_heading">{translate module=invoice}pay_invoice{/translate}</td>
              </tr>
            </table>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="4" cellpadding="3" bgcolor="#FFFFFF"> 
      		{if $checkoutoptions}
      		{foreach from=$checkoutoptions item=checkout key=key} 			  
			  <tr valign="top"> 
                <td width="20%"><a href="javascript:void(0);" onClick="changeCheckoutOption({$checkout.fields.id},'{if $VAR._page=='invoice:checkout_multiple'}multi{else}invoice{/if}','{$invoice.id}',0)">{if $checkout.fields.graphic_url==''}{$checkout.fields.name}{else}<img src="{$checkout.fields.graphic_url}" alt="{$checkout.fields.name}" border="0">{/if}</a></td>
                <td width="80%">{$checkout.fields.description} </td>
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
  <script language=javascript>changeCheckoutOption(last_checkout_id,{$invoice.id});</script>
{elseif $VAR.option>0} 
  <script language=javascript>
    {literal}
    try { document.getElementById('checkout_options_show').style.display='block'; } catch(e) {}  
	try { document.getElementById('checkout_options').style.display='none'; } catch(e) {}   
	{/literal}
  </script>
{/if} 