<script src="themes/default/blocks/asset_invoice/ajax.js" type="text/javascript"></script>
{if !$invoices}
	<b>No invoices found</b>
{else}
{foreach from=$invoices item=i}

<form target="_blank">
<table width="100%" border="1" cellpadding="5" cellspacing="0" bordercolor="#666666">
  <tr>
    <th colspan="2" bgcolor="#999999" scope="col"><font color="#FFFFFF">Invoice # {$i.id} </font></th>
  </tr>
  <tr>
    <td width="10%"><strong>Customer</strong></td>
    <td width="90%">{$i.first_name} {$i.last_name}, {$i.address1} {$i.address2}, {$i.city}, {$i.state} {$i.zip} </td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#CCCCCC">
	  <div id="{$i.id}">
	  <table width="100%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
	  <tr>
		  <th scope="col">SKU</th>
		  <th scope="col">PRODUCT NAME </th>
	      <th scope="col">ASSIGN ASSET </th>
	    </tr>
		{foreach from=$i.items item=p}
	    <tr>
		  <td>{$p.sku}</td>
		  <td>{$p.name}</td>
	      <td>
		  	<select name="items" onchange="setItemValue(items_{$i.id}, {$p.id}, this.value)">
			  <option value="0">- SELECT ASSET -</option>
			 {foreach from=$assets item=a}
			  <option value="{$a.id}">{$a.name}</option>
			 {/foreach}
	        </select>
	      </td>
	    </tr>
		{/foreach}
	</table> 
	</div>
 	</td>
  </tr>
  <tr>
    <td colspan="2" align="right">
	  <input type="button" value="Assign Now" onclick="assignInvoice('{$i.id}', items_{$i.id});" />
	</td>
  </tr>  
</table>
<br>
<input type="hidden" name="_page" value="asset_invoice:assign" />
<input type="hidden" name="do[]" value="asset_invoice:assign" />
<input type="hidden" name="invoice_id" value="{$i.id}" />
<input type="hidden" name="pool_id" value="{$VAR.pool_id}" />
</form>
<script>
var items_{$i.id} = [];
{$i.itemsJs}
</script> 
{/foreach}
{/if}