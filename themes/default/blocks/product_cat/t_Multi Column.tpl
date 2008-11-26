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
<br>
{/foreach} 
{/if}
<br> 

<!-- Show each product, 3 columns per row -->
{ if $product_arr } 
{assign var=entries value=$product_arr}
{assign var=columns value=3}
{if count($entries) > 0} 
<table width="100%" cellpadding="0" cellspacing="5"> 
  {math assign="lastcol" equation="x-1" x=$columns} 
  {math assign="width" equation="100 / x" x=$columns} 
  {section name="idx" loop=$entries} 
  {math assign="col" equation="x % y" x=$smarty.section.idx.rownum y=$columns}
  {if $col == 0}  {php}
  <tr>{/if} 
      <td width="{$width}%">  
      <table width=100% border="0" cellspacing="1" cellpadding="0" class="table_background">
        <tr> 
          <td height="17"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_heading_cart">
              <tr valign="top"> 
                <td width="78%" >  
				{math equation="x-1" x=`$smarty.section.idx.rownum` assign=i}
				{assign var=product value=$product_arr.$i} 
                  <p><a href="{$URL}?_page=product:details&id={$product.id}"> 
                    {if $list->translate("product_translate","name,description_short,description_full","product_id", $product.id, "prod_translate") }
                    {$prod_translate.name}
                    {else}
                    {$product.sku}
                    {/if}  
                    </a></p>
                </td>
                <td width="22%" valign="middle" align="right"> <b> 
                  {$list->format_currency_num($product.price_base,$smarty.const.SESS_CURRENCY)}
                  </b> </td>
              </tr>
            </table> 
          </td>
        </tr>
        <tr> 
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body" bgcolor="#FFFFFF">
              <tr valign="top"> 
                {if $product.thumbnail != ""}
                <td width="8%" valign="top"> 
                  <p> <a href="{$URL}?_page=product:details&id={$product.id}"><img  src="{$URL}{$smarty.const.URL_IMAGES}{$product.thumbnail}" hspace="5" border="0"></a> 
                  </p>
                </td>
                {/if}
                <td width="92%" align="left"> 
                  <p> 
                    {if $product.thumbnail != "" }
                    <a href="{$URL}?_page=product:details&id={$product.id}"> </a> 
                    {/if}
                    {if $prod_translate.description_short != "" }
                    {$prod_translate.description_short}
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
    {if $col == $lastcol || $smarty.section.idx.index_last == true }</tr>{/if} {* end of row *}
    {/section} 
  </table>
{/if}
 
 

<!-- Show product paging -->
{if $page_pages > 1}
{translate module=product_cat}
pages 
{/translate}
{foreach from=$page_arr item=page}
<a href="?_page={$VAR._page}&id={$VAR.id}&page={$page}"> 
{if $page == $page_page}
<b> 
{$page}
</b> 
{else}
{$page}
{/if}
</a> 
{if $page != $page_pages}
| 
{/if}
{/foreach}
{/if}
{else}
{translate module=product_cat}
products_none 
{/translate}
{/if}
