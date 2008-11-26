
{ $method->exe("asset","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="asset_search" method="post" action="">
  {$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=asset}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("asset_date_orig", $VAR.asset_date_orig, "form_field", "") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_date_last
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("asset_date_last", $VAR.asset_date_last, "form_field", "") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_status
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("asset_status", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_service_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="asset_service_id" value="{$VAR.asset_service_id}" {if $asset_service_id == true}class="form_field_error"{/if}> &nbsp;&nbsp;			{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_pool_id
                        {/translate}</td>
                    <td width="65%">
                        { $list->menu("no", "asset_pool_id", "asset_pool", "name", "all", "") }
						 &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_asset
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="asset_asset" value="{$VAR.asset_asset}" {if $asset_asset == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_misc
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="asset_misc" value="{$VAR.asset_misc}" {if $asset_misc == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" name="limit" size="5" value="{$asset_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$asset item=record}
                            <option value="{$record.field}">{$record.translate}</option>
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
                      <input type="hidden" name="module" value="asset">
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
