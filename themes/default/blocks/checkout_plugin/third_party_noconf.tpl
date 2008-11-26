<form id="checkout_form" name="checkout_form" method="post" action="">  
<input type="hidden" name="option" value="{$VAR.option}">
{if $VAR.invoice_id == ""}
{if $VAR.admin != '' && $VAR.account_id != '' }
<input type="hidden" name="admin" value="1">
<input type="hidden" name="do[]" value="checkout:admin_checkoutnow">
<input type="hidden" name="_page" value="checkout:admin_checkout">
<input type="hidden" name="account_id" value="{$VAR.account_id}">
{else}
<input type="hidden" name="do[]" value="checkout:checkoutnow">
<input type="hidden" name="_page" value="checkout:checkout">
{/if}
{else}
{if $VAR.admin != '' && $VAR.account_id != '' }
<input type="hidden" name="admin" value="1">
<input type="hidden" name="do[]" value="checkout:admin_checkoutnow">
<input type="hidden" name="_page" value="checkout:admin_checkout">
<input type="hidden" name="account_id" value="{$VAR.account_id}">	
{else}
<input type="hidden" name="do[]" value="invoice:checkoutnow">
{if $VAR.invoice_id > 0}
<input type="hidden" name="_page" value="invoice:user_view"> 
{else}
<input type="hidden" name="_page" value="invoice:checkout_multiple"> 
{/if} 
{/if}
<input type="hidden" name="invoice_id" value="{$VAR.invoice_id}">
{/if}

<div>
<center><p>{translate module=checkout}redirect_html{/translate}</p></center>
<input type="hidden" id="doredirect" value="true">
<input type="hidden" id="noconf" value="true">
</div>

</form>