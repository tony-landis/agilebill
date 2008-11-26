


 
<form name="newsletter_subscribe" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=newsletter}
                submit_subscribe
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    subscribe_newsletters 
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
                    field_first_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_first_name" size="24"  value="{$VAR.newsletter_first_name}" maxlength="128">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_last_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_last_name" size="24"  value="{$VAR.newsletter_last_name}" maxlength="128">
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
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_email_html 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="checkbox" name="newsletter_html" value="1">
                  </td>
                </tr>
				{ $method->exe("newsletter_subscriber","static_var")} 
                {foreach from=$static_var item=record}
                <tr valign="top"> 
                  <td width="35%"> 
                    {$record.name}
                  </td>
                  <td width="65%"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}				
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td> 
                          <input type="submit" name="Submit22" value="{translate module="newsletter"}submit_subscribe{/translate}" class="form_button">
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
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="core:blank">
  <input type="hidden" name="do[]" value="newsletter:subscribe">
  <input type="hidden" name="newsletter_type" value="1">
</form>
<br>
<form name="newsletter_subscribe" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="1" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
          <tr valign="top"> 
            <td width="35%"> 
              <table width="100%" border="0" cellpadding="3" class="row1">
                <tr>
                  <td> 
                    {translate module=newsletter}unsubscribe_newsletters{/translate}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr class="row1" valign="middle" align="left"> 
            <td width="35%"> 
              <div align="center"> 
                {translate module=account_admin}
                field_email 
                {/translate}
				&nbsp;&nbsp; 
                <input type="text" name="newsletter_email" size="24"  value="{$VAR.newsletter_email}" maxlength="128">
                &nbsp;&nbsp; 
                <input type="submit" name="Submit2" value="{translate module="newsletter"}submit_unsubscribe{/translate}" class="form_button">
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="newsletter:unsubscribe">
</form>

