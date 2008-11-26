

<table width=100% border="0" cellspacing="1" cellpadding="0" class="table_background">
  <tr valign="middle" class="row2"> 
    <td width="96%">
       <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_heading_cart" height="25">
         <tr valign="top"> 
           
          <td> 
            {translate module=host_tld}
            search_mass_header 
            {/translate}
          </td>
        </tr>
      </table>  
    </td>
  </tr>
  <tr valign="top" class="row2"> 
    <td width="96%"> 
      <form name="domain" method="post" action="">
        <table width="100%" border="0" cellspacing="0" cellpadding="3" class="row1">
			  <tr> 
				<td width="95%"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
                <tr> 
                  <td width="53%"> 
                    <textarea id="domain" name="domains" cols="40" rows="10">{$domains}</textarea>
                  </td>
                  <td width="47%" valign="top"> 
                    <p> 
                      {translate module=host_tld}
                      search_mass_instructions 
                      {/translate}
                    </p>
                    </td>
                </tr>
                <tr> 
                  <td width="53%"><a href="javascript:search();"><b><u> </u></b></a> 
                    <input type="submit" name="Submit" value="{translate}search{/translate}">
                    <input type="hidden" name="do[]" value="host_tld:whois_mass">
					<input type="hidden" name="_page" value="host_tld:search_mass">
                  </td>
                  <td width="47%" align="right">
                    {if $checkout}
                    <b> <a href="javascript:document.getElementById('cartadd').submit();"> 
                    <u> 
                    {translate module=host_tld}
                    suggest_purchase 
                    {/translate}
                    </u></a></b>
                    {/if}
                  </td>
                </tr>
              </table>
            </td>
			  </tr>
			</table>
		  </form> 
		</td>
	  </tr>
	</table> 
<form id="cartadd" name="cartadd" method="post" action="">

	{foreach from=$domainarr item=rs}
  	<input type="hidden" name="domain_name[]" value="{$rs.0}">
	<input type="hidden" name="domain_tld[]"  value="{$rs.1}">
	<input type="hidden" name="host_type[]" value="register">
	{/foreach}
	
	<input type="hidden" name="do[]" value="cart:add">
	<input type="hidden" name="_page" value="cart:cart">
</form>  

