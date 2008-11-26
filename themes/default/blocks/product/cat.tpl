{$method->exe_noauth("product_cat","user_menu")}

{if $product_cat != "" || $smarty.const.SHOW_DOMAIN_LINK }
{foreach from=$product_cat item=record}
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
                    No Description 
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
