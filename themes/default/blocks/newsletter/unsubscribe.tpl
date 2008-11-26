<form name="newsletter_subscribe" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="1" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
          <tr valign="top"> 
            <td width="35%"> 
              {translate module=newsletter}
              unsubscribe_newsletter
              {/translate}
            </td>
            <td width="65%"> 
              { $method->exe("newsletter", "check_list") }
              {if ($method->result == FALSE)}
              {$block->display("core:method_error")}
              {/if}
            </td>
          </tr>
          <tr valign="top"> 
            <td width="35%"> 
              {translate module=account_admin}
              field_email 
              {/translate}
            </td>
            <td width="65%"> 
              <input type="text" name="newsletter_email" size="24"  value="{$VAR.newsletter_email}" maxlength="128">
            </td>
          </tr>
          <tr class="row1" valign="middle" align="left"> 
            <td width="35%"></td>
            <td width="65%"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td> 
                    <input type="submit" name="Submit2" value="{translate module="newsletter"}submit_unsubscribe{/translate}" class="form_button">
                  </td>
                  <td align="right">&nbsp; </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="newsletter:unsubscribe">
  <input type="hidden" name="do[]" value="newsletter:unsubscribe">
</form>
