{$list->unserial($product.prod_plugin_data, "plugin_data")} 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">

  <tr valign="top">
    <td width="55%">Prepaid Type</td>
    <td width="45%"> 
	<select name="product_prod_plugin_data[type]" value="{$plugin_data.type}" onChange="submit()">
	  <option>-- Select One --</option>
	  <option value="pin" {if $plugin_data.type=='pin'}selected{/if}>PIN Authentication</option>
	  <option value="ani" {if $plugin_data.type=='ani'}selected{/if}>ANI Authentication</option>
	  <option value="did" {if $plugin_data.type=='did'}selected{/if}>SIP Prepaid</option>
	</select>   </td>
  </tr>
  
{if $plugin_data.type == 'pin' ||  $plugin_data.type == 'ani'} 

  <tr valign="top">
    <td width="55%"> Toll free number to sent to user in e-mail confirmation after provisioning </td>
    <td> <input type="text" name="product_prod_plugin_data[number]" value="{$plugin_data.number}">    </td>
  </tr> 
  <tr valign="top">
    <td>Set auto expire pin after first use? </td>
    <td>{ $list->bool("product_prod_plugin_data[expire]", $plugin_data.expire, "") } after
      <input name="product_prod_plugin_data[expire_days]" type="text" id="product_prod_plugin_data[expire_days]" value="{$plugin_data.expire_days}" size="5"> days
	</td>
  </tr>  
  
{elseif $plugin_data.type == 'did'}
  {$block->display("product_plugin:plugin_prod_VOIP")}
{/if} 

</table>
