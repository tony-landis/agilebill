

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="newsletter_subscriber_add" name="newsletter_subscriber_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=newsletter_subscriber}
                title_add
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_newsletter_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "newsletter_subscriber_newsletter_id", "newsletter", "name", $VAR.newsletter_subscriber_newsletter_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_email 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_subscriber_email" value="{$VAR.newsletter_subscriber_email}" {if $newsletter_subscriber_email == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_html 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("newsletter_subscriber_html", $VAR.newsletter_subscriber_html, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_first_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_subscriber_first_name" value="{$VAR.newsletter_subscriber_first_name}" {if $newsletter_subscriber_first_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_last_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_subscriber_last_name" value="{$VAR.newsletter_subscriber_last_name}" {if $newsletter_subscriber_last_name == true}class="form_field_error"{/if}>
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
				
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="newsletter_subscriber:view">
                    <input type="hidden" name="_page_current" value="newsletter_subscriber:add">
                    <input type="hidden" name="do[]" value="newsletter_subscriber:add">
                    <input type="hidden" name="newsletter_subscriber_date_orig" value="{ $list->date_time($smarty.now) }">
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
