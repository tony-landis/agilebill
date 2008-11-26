<h2>Define Product Rate Tables</h2>

<form>
<p>
Select a Product: 
{html_menu field=product assoc_table=product assoc_field=sku conditions="(prod_plugin_file='VOIP' OR prod_plugin_file='PREPAID')" default=$VAR.product}
<input type="submit" value="Update Rate Tables">
<input name="_page" type="hidden" value="voip_rate:products">
</p>
</form>

{if $VAR.product}
{$method->exe_noauth('voip_rate','product_rates')}
<hr>
<form>
<table width="100%"  border="0" cellpadding="5" cellspacing="2" class="body">
  <tr>
    <td width="34%"><strong>Available Rates </strong></td>
    <td width="66%"><strong>Applied Rates </strong></td>
  </tr>
  <tr>
    <td> 
    Select rates above to associate with this product.    </td>
	
    <td>
    Select rates above to remove from this product.	</td>
  </tr>
  <tr valign="top">
    <td>
	{if $avail}
	<select name="avail[]" size="30" multiple id="avail"> 
		{foreach from=$avail item=rate} 
	     <option value="{$rate.id}">{$rate.name}</option> 
		{/foreach}  
    </select>
	{else}
		No rates available that have not already been applied to this product.
	{/if}
	</td>
    <td>
	{if $assigned}
	<select name="assigned[]" size="30" multiple id="assigned">       
	{foreach from=$assigned item=rate}		
      <option value="{$rate.id}">{$rate.name}</option>      
	{/foreach}       
    </select>
	{else}
		No rates have been applied to this product.
	{/if}	
	</td>
  </tr>
  <tr>
    <td><input type="submit" name="Submit" value="Update Changes">
      <input name="product" type="hidden" value="{$VAR.product}">
      <input name="do[]" type="hidden" value="voip_rate:products">
      <input name="_page" type="hidden" value="voip_rate:products"></td>
    <td>&nbsp; </td>
  </tr>
</table>

<p>HINT: Hold downt the 'Ctrl' key to select or deselect multiple rates. </p>
<p>{/if}
</p>
</form>
