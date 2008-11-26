{if $smarty.const.SHOW_CAT_BLOCK}
<!-- View the category menu -->
	{ $block->display("product_cat:menu") }
{/if} 
 
{if $smarty.const.SHOW_STATIC_BLOCK}
<!-- View the account static page menu -->
	{ $block->display("static_page:menu") }
{/if}

{if $smarty.const.SHOW_FILE_BLOCK}
<!-- Display the file menu -->
	{ $block->display("file:menu") }
{/if}


<!-- View the account menu options -->
<table width="140" border="0" cellspacing="0" cellpadding="0" class="menu_background" align="center">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0" align="center">
        <tr> 
          <td class="menu_1"> 
            <center>
              {translate}
              main 
              {/translate}
            </center>
          </td>
        </tr>
		
		{if $smarty.const.SHOW_PRODUCT_LINK}
        <tr> 
          <td class="menu_2"> &nbsp;&nbsp; <a href="?_page=product:cat"> 
            {translate}
            products 
            {/translate}
            </a> </td>
        </tr>
		{/if}
		
		{if $smarty.const.SHOW_CART_LINK}
        <tr> 
          <td class="menu_2">&nbsp;&nbsp; <a href="?_page=cart:cart">
            {translate}
            cart 
            {/translate}
            </a> </td>
        </tr>
		{/if}


		{if $smarty.const.SHOW_CHECKOUT_LINK}
        <tr> 
          <td class="menu_2">&nbsp;&nbsp; <a href="{$SSL_URL}?_page=checkout:checkout&s={$SESS}">
            {translate}
            checkout 
            {/translate}
            </a> </td>
        </tr>
		{/if}

		{if $smarty.const.SHOW_CONTACT_LINK}
        <tr> 
          <td class="menu_2">&nbsp;&nbsp; <a href="?_page=staff:staff">
            {translate}
            contact 
            {/translate}
            </a> </td>
        </tr>
		{/if}
		
				
		{if $smarty.const.SHOW_AFFILIATE_LINK}
        <tr> 
          <td class="menu_2">&nbsp;&nbsp; <a href="?_page=affiliate:affiliate">
            {translate}
            affiliates 
            {/translate}
            </a> </td>
        </tr>
		{/if}
		
		{if $smarty.const.SHOW_NEWSLETTER_LINK}
        <tr> 
          <td class="menu_2">&nbsp;&nbsp; <a href="?_page=newsletter:newsletter">
            {translate}
            newsletter 
            {/translate}
            </a> </td>
        </tr>
		{/if}
		
		{if $smarty.const.SHOW_TICKET_LINK}
        <tr>
          <td class="menu_2">&nbsp;&nbsp; <a href="?_page=ticket:ticket"> 
            {translate}
            ticket 
            {/translate}
            </a> </td>
        </tr>
        {/if}
		
      </table>
    </td>
  </tr>
</table>

{if $SESS_LOGGED == "1" }
<br>
<table width="140" border="0" cellspacing="0" cellpadding="0" class="menu_background" align="center">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0" align="center">
        <tr> 
          <td class="menu_1"> 
            <center>
              {translate}
              account 
              {/translate}
            </center>
          </td>
        </tr>
        <tr> 
          <td class="menu_2">&nbsp;&nbsp; <a href="?_page=account:account">
            {translate}
            account 
            {/translate}
            </a> </td>
        </tr>
        <tr> 
          <td class="menu_2">&nbsp;&nbsp; <a href="?_page=account:account&_logout=Y">
            {translate}
            logout 
            {/translate}
            </a> </td>
        </tr>
        {if $list->auth_method_by_name("account_admin","view") &&  $SESS_LOGGED == "1" }
        <tr> 
          <td class="menu_2">&nbsp;&nbsp; <a href="admin.php">
            {translate}
            admin 
            {/translate}
            </a> </td>
        </tr>
        {/if}
      </table>
    </td>
  </tr>
</table>
<p> 
  {else}
  <br>
  <!-- Show the login menu -->
  { $block->display("account:login_small")}
  {/if}
  <br>
</p>
