<!-- Display the form validation -->
{if $form_validation}
{ $block->display("core:alert_fields") }
{/if}

<form name="contact" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=staff}
                title_contact 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr>
                  <td>
                    {translate module=staff}
                    contact_options 
                    {/translate}
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=staff}
                    contact_staff 
                    {/translate}
                  </td>
                  <td width="65%">
				  {if $VAR.mail_staff_id == ""}
                    { $list->menu("no", "mail_staff_id", "staff", "nickname", "all", "form_menu") }
				  {else}
				    { $list->menu("no", "mail_staff_id", "staff", "nickname", $VAR.mail_staff_id, "form_menu") }
				  {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=staff}
                    contact_department 
                    {/translate}
                  </td>
                  <td width="65%"> 
				  {if $VAR.mail_department_id == ""}
                    { $list->menu("no", "mail_department_id", "staff_department", "name", "all", "form_menu")  }
				  {else}
				    { $list->menu("no", "mail_department_id", "staff_department", "name", $VAR.mail_department_id, "form_menu")  }
				  {/if}					
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=staff}
                    contact_name 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="mail_name" size="50"  value="{$VAR.mail_name}">
                  </td>
                </tr>								
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=staff}
                    contact_email 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="mail_email" size="50"  value="{$VAR.mail_email}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    mail_subject 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="mail_subject" value="{$VAR.mail_subject}"  size="50">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    mail_message 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="mail_message" {if $mail_message == true}class="form_field_error"  {/if} cols="55" rows="6">{$VAR.mail_message}</textarea>
                  </td>
                </tr>
				 
				<!-- static vars -->
			    { $method->exe("staff","static_var")} 
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
								
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    mail_priority 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="mail_priority">
                      <option value="0">{translate}false{/translate}</option> 
                      <option value="1">{translate}true{/translate}</option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp;</td>
                  <td width="65%"> 
                    <input type="image" border="0" name="imageField" src="themes/{$THEME_NAME}/images/icons/mail_32.gif" alt="{translate}submit{/translate}">
                    <input type="hidden" name="_page" value="staff:staff">
                    <input type="hidden" name="do[]" value="staff:contact">
                    <input type="hidden" name="_page_current" value="staff:staff">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
 
