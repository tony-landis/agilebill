<!-- Display the form to collect the input values -->
{if $VAR.invoice_account_id == "" }
<form id="invoice_add" name="invoice_add" method="post" action=""> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=invoice}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="invoice_account_id" default=$VAR.invoice_account_id}
                  </td>
                </tr>
				{ if $list->is_installed('affiliate') == 1  }
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_affiliate_id 
                    {/translate}
                  </td>
                  <td width="65%">  
                    {html_select_affiliate name="aid" default=$VAR.aid}
                  </td>
				  {/if}
                </tr>
                <tr valign="top">
                  <td width="35%"></td>
                  <td width="65%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="invoice:add">
                    <input type="hidden" name="_page_current" value="invoice:add">
                  </td>
                </tr>
              </table>
              </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
 
{else}
<form id="invoice_add" name="invoice_add" method="post" action="">
  
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=invoice}
                title_add 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="invoice_account_id" default=$VAR.invoice_account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    add_product 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "invoice_product_id", "product", "sku", "all", "\" onchange=\"showProduct(this.value)\"") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top">
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
                <tr>
                  <td align="center"> <a href="javascript:addAdHoc();"> 
                    {translate module=invoice}
                    add_ad_hoc 
                    {/translate}</a> | 
                    {if $list->is_installed('host_tld')}
                    <a href="javascript:domainSearch();"> 
                    {translate module=invoice}
                    add_domain 
                    {/translate}
                    </a> 
                    <input type="hidden" name="domain_name" id="domain_name">
					<input type="hidden" name="domain_tld" id="domain_tld"> 
                    {/if} | 			  
				    <a href="javascript:showDiscount()"> 
                    {translate module=invoice}
                    add_discount 
                    {/translate}
                    </a> | <a href="javascript:showCart()"> 
                    {translate module=invoice}
                    add_view_items 
                    {/translate}
                    </a> | <a href="javascript:showCheckout()"> 
                    {translate module=invoice}
                    add_finalize 
                    {/translate}
                    </a>  
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
{literal}
  <center> 
    <BR>
	<iframe name="iframeInvoice" id="iframeInvoice" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{/literal}{$THEME_NAME}{literal}/IEFrameWarningBypass.htm"></iframe> 
  </center>
<script language="JavaScript">
<!-- START
  
var account_id 	= '{/literal}{$VAR.invoice_account_id}{literal}';  
 
function showCart() {
	showIFrame('iframeInvoice',getPageWidth(600),500,'?_page=cart:admin_view&_escape=1&account_id='+account_id);
}

function showCheckout() {
	showIFrame('iframeInvoice',getPageWidth(600),500,'{/literal}{$SSL_URL}{literal}admin.php?_page=checkout:admin_checkout&_escape=1&account_id='+account_id+'&s='+SESS);
}
 
function showProduct(product_id) {
	showIFrame('iframeInvoice',getPageWidth(600),500,'?_page=product:admin_details&_escape=1&id='+product_id+'&account_id='+account_id);
}

function showDiscount() { 
	showIFrame('iframeInvoice',getPageWidth(600),500,'?_page=discount:add&discount_avail_account_id={/literal}{$VAR.invoice_account_id}{literal}');
}

function domainSearch() {
	var domain = document.getElementById('domain_name').value;
	showIFrame('iframeInvoice',getPageWidth(600),500,'?_page=host_tld:admin_search&domain='+domain+'&account_id='+account_id);
}

function addAdHoc() { 
	showIFrame('iframeInvoice',getPageWidth(600),500,'?_page=cart:ad_hoc&account_id='+account_id);
}

function domainUpdate() { 
}
 
showCart();

//  END -->
</script>
{/literal}
{/if}