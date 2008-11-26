
{ $method->exe("campaign","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="campaign_search" method="post" action="">
  
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=campaign}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("campaign_date_orig", $VAR.campaign_date_orig, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("campaign_date_last", $VAR.campaign_date_last, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("campaign_date_start", $VAR.campaign_date_start, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("campaign_date_expire", $VAR.campaign_date_expire, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("campaign_status", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="campaign_name" value="{$VAR.campaign_name}" {if $campaign_name == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp;
                    {translate}
                    search_partial
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="campaign_description" value="{$VAR.campaign_description}" {if $campaign_description == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp;
                    {translate}
                    search_partial
                    {/translate}
                  </td>
                </tr>
                <!-- Define the results per page -->
                <tr class="row1" valign="top"> 
                  <td width="35%">
                    {translate}
                    search_results_per
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text"  name="limit" size="5" value="{$campaign_limit}">
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
                      {foreach from=$campaign item=record}
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
                    <input type="hidden" name="module" value="campaign">
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
