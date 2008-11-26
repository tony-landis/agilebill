<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=invoice}
              thank_you 
              {/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
              <tr valign="top"> 
                <td width="65%"> 
                  {translate module=invoice}
                  thank_you_text 
                  {/translate}
                </td>
              </tr>
              <tr valign="top"> 
                <td width="65%">
				{if $VAR.id != ""}
				  <a href="?id={$VAR.id}&_page={if $VAR._next_page != ""}{$VAR._next_page}{else}invoice:user_view{/if}"> 
                  {translate module="invoice"}
                  invoice_link 
                  {/translate}
                  </a>
				 {/if}
				 </td>
              </tr>
              <tr valign="top">
                <td width="65%"><a href="?_page=account:account"> 
                  {translate module="invoice"}
                  account_link 
                  {/translate}
                  </a></td>
              </tr>
            </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table> 
