
<table width=100% border="0" cellspacing="1" cellpadding="0">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0" height="25">
              <tr valign="top"> 
                <td><h2>       
				  {translate}
				  products 
				  {/translate} 
				  </h2>    
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
    <td align="right" valign="top">
      {if $smarty.const.SHOW_CHECKOUT_LINK}<a href="{$SSL_URL}?_page=checkout:checkout&s={$SESS}">{translate}checkout{/translate}</a>{/if}
    </td>
  </tr>
</table>
<br>
{$method->exe_noauth("product_cat","user_menu")}

{if $product_cat != "" || $smarty.const.SHOW_DOMAIN_LINK }
<table width="100%" border="0" class="table_background" align="left">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0" align="left">
        <tr> 
          <td class="menu_1"> 
            <h2>
				{translate module=product_cat}menu{/translate}
            </h2>
          </td>
        </tr>
		
		{foreach from=$product_cat item=record key=key}
        <tr> 
          <td class="menu_2"> &nbsp;&nbsp; 
            <a href="{$URL}?_page=product_cat:t_{$record.template}&id={$record.id}">
			{if $list->translate("product_cat_translate","name,description","product_cat_id", $record.id, "cat_translate") }
            {$cat_translate.name}
            {else}
            {$record.name}
            {/if}
            </a>
		  </td>
        </tr>
        {/foreach}
		
		{ if $smarty.const.SHOW_DOMAIN_LINK }
        <tr> 
          <td class="menu_2">  &nbsp;&nbsp; 
		    <a href="{$URL}?_page=host_tld:search">
            {translate}
            domain_search 
            {/translate}
            </a>
		  </td>
        </tr>
        {/if}
      </table>
    </td>
  </tr>
</table>
<br><br><br>
{/if}
