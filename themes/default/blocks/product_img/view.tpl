{ $block->display("core:top_clean") }

{ $method->exe("product_img","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 

<!-- Loop through each record -->
{foreach from=$product_img item=product_img}
<a name="{$product_img.id}"></a> 
<!-- Display the field validation -->
{if $form_validation}
{ $block->display("core:alert_fields") }
{/if}
<!-- Display each record -->
<form name="product_img_view" method="post" action="" enctype="multipart/form-data">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="65%" align="right"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                      <tr> 
                        <td width="28%"><a href="?_page=product_img:add&product_img_product_id={$product_img.product_id}&_escape=1&_escape_next=1"> 
                          {translate module=product_img}
                          title_add 
                          {/translate}
                          </a></td>
                        <td width="35%" align="right"> <a href="?_page=core:search_iframe&module=product_img&product_img_product_id={$product_img.product_id}&_next_page_none=add&name_id1=product_img_product_id&val_id1={$product_img.product_id}&_escape=1&_escape_next=1&do[]=product_img:delete&id={$product_img.id}"> 
                          {translate module=product_img}
                          title_delete 
                          {/translate}
                          </a> </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    
  <div align="center"><br>
    {if $product_img.type == "0" }
    <img src="{$URL}{$smarty.const.URL_IMAGES}prod_img_{$product_img.id}.{$product_img.url}"> 
    {else}
    <img src="{$product_img.url}"> 
    {/if}
    <input type="hidden" name="_page" value="product_img:view">
    <input type="hidden" name="product_img_id" value="{$product_img.id}">
    <input type="hidden" name="do[]" value="product_img:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </div>
</form>
  {/foreach}
{/if}
