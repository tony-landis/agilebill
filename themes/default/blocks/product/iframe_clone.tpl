{ $block->display("core:top_clean") }
 
{ $method->exe("product","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 

<!-- Loop through each record -->
{foreach from=$product item=product}

<form name="product_view" method="post" action=""> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="4">
          <tr valign="top"> 
            <td width="65%" class="row1"><b>
              {translate module=product}
              field_sku 
              {/translate}
              </b> </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <input type="text" name="product_sku" value="{$VAR.product_sku}" {if $product_sku == true}class="form_field_error"{/if}>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    
  <input type="hidden" name="do[]" value="product:cloner">
  <input type="hidden" name="_page" value="product:iframe_clone">
  <input type="hidden" name="_page_current" value="product:iframe_clone"> 
  <input type="hidden" name="id" value="{$product.id}"> 
</form>

 {/foreach}{/if}
