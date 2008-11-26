
{ $method->exe("newsletter_subscriber","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="newsletter_subscriber_search" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=newsletter_subscriber}
                title_search
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
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("newsletter_subscriber_date_orig", $VAR.newsletter_subscriber_date_orig, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_newsletter_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "newsletter_subscriber_newsletter_id", "newsletter", "name", "all", "form_menu") }
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
                    &nbsp;&nbsp;
                    {translate}
                    search_partial
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_html 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("newsletter_subscriber_html", "all", "form_menu") }
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
                    &nbsp;&nbsp;
                    {translate}
                    search_partial
                    {/translate}
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
                    &nbsp;&nbsp;
                    {translate}
                    search_partial
                    {/translate}
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
				
								
                <!-- Define the results per page -->
                <tr class="row1" valign="top"> 
                  <td width="35%">
                    {translate}
                    search_results_per
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text"  name="limit" size="5" value="{$newsletter_subscriber_limit}">
                  </td>
                </tr>
                <!-- Define the order by field per page -->
                <tr class="row1" valign="top"> 
                  <td width="35%">
                    {translate}
                    search_order_by
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select  name="order_by">
                      {foreach from=$newsletter_subscriber item=record}
                      <option value="{$record.field}">
                      {$record.translate}
                      </option>
                      {/foreach}
                    </select>
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="core:search">
                    <input type="hidden" name="_escape" value="Y">
                    <input type="hidden" name="module" value="newsletter_subscriber">
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
{ $block->display("core:saved_searches") }
{ $block->display("core:recent_searches") }
{/if}
