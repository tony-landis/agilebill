{$method->exe("cart","changeqty")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else} 
 	{if $js}
	<script language="javascript"> 
		var base  = '{$list->format_currency_num($base, $smarty.const.SESS_CURRENCY)}';	
		var setup = '{$list->format_currency_num($setup, $smarty.const.SESS_CURRENCY)}';
		var qty   = '{$qty}';
		window.parent.updatePrice('{$VAR.id}',base,setup,qty);
	</script>
	javascript
	{else}
	no javascript
	{/if}
{/if} 
 