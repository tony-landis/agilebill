{if $VAR.id == ""}	{$block->display("product:cat")} {else}
{$method->exe_noauth("product_cat", "user_view")}

<!-- Show the category drill-down -->
{ if $product_cat_arr }
{foreach from=$product_cat_arr item=record}
<table width=100% border="0" cellspacing="1" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_heading_cart" height="25">
              <tr valign="top"> 
                <td> <a href="{$URL}?_page=product:cat">{translate module=product_cat}menu{/translate}</a> 
                  {foreach from=$parent_cat item=cat}
                  > <a href="?_page=product_cat:t_{$cat.template}&id={$cat.id}"> 
                  {$cat.name}
                  </a> 
                  {/foreach}
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
{/foreach}
{/if}
{/if}



<!-- Show subcategories -->
{if $product_sub_cat != ""}
<br>
{foreach from=$product_sub_cat item=record}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr valign="top"> 
                <td  class="table_heading_cart"> <a href="{$URL}?_page=product_cat:t_{$record.template}&id={$record.id}"> 
                  <b> 
                  {if $list->translate("product_cat_translate","name,description","product_cat_id", $record.id, "cat_translate") }
                  {$cat_translate.name}
                  {else}
                  {$record.name}
                  {/if}
                  </b> </a> </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
              <tr valign="top"> 
                <td> 
                  <p> 
                    {if $record.thumbnail != "" }
                    <a href="{$URL}?_page=product_cat:t_{$record.template}&id={$record.id}"><img align="left" src="{$URL}{$smarty.const.URL_IMAGES}{$record.thumbnail}" hspace="5" border="0"></a> 
                    {/if}
                    {if $cat_translate.description!= "" }
                    {$cat_translate.description}
                    {else}
                    {translate module=product_cat}
                    desc_none 
                    {/translate}
                    {/if}
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table> 
{/foreach}
{/if}
<br> 



<!-- Show each product -->
{ if $product_arr } 
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_heading_cart">
              <tr valign="top"> 
                <td width="78%" > 
                  {translate module=product_cat}
                  plans_select 
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
                    <input type="hidden" name="_page" value="product:details_wizard"> 
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
<br>
{else}
{translate module=product_cat}plans_none{/translate} 
{/if}
