
{ $method->exe("radius","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="radius_search" method="post" action="">
  {$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=radius}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_account_id
                        {/translate}</td>
                    <td width="67%">
                        {html_select_account name="radius_account_id" default=$VAR.service_account_id}</td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_service_id
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_service_id" value="{$VAR.radius_service_id}" {if $radius_service_id == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_active
                        {/translate}</td>
                    <td width="67%">
                        { $list->bool("radius_active", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_sku
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_sku" value="{$VAR.radius_sku}" {if $radius_sku == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_auth
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_auth" value="{$VAR.radius_auth}" {if $radius_auth == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_username
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_username" value="{$VAR.radius_username}" {if $radius_username == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_analog
                        {/translate}</td>
                    <td width="67%">
                        { $list->bool("radius_analog", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_digital
                        {/translate}</td>
                    <td width="67%">
                        { $list->bool("radius_digital", "all", "form_menu") }
                    </td>
                  </tr>				  
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_filter_id
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_filter_id" value="{$VAR.radius_filter_id}" {if $radius_filter_id == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_netmask
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_netmask" value="{$VAR.radius_netmask}" {if $radius_netmask == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_framed_route
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_framed_route" value="{$VAR.radius_framed_route}" {if $radius_framed_route == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_static_ip
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_static_ip" value="{$VAR.radius_static_ip}" {if $radius_static_ip == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_profiles
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_profiles" value="{$VAR.radius_profiles}" {if $radius_profiles == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="33%">{translate}search_results_per{/translate}</td>
                    <td width="67%">
                      <input type="text" name="limit" size="5" value="{$radius_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="33%">{translate}search_order_by{/translate}</td>
                    <td width="67%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$radius item=record}
                            <option value="{$record.field}">{$record.translate}</option>
        		          {/foreach}
                      </select>
                    </td>
                  </tr>

                  <tr class="row1" valign="top">
                    <td width="33%"></td>
                    <td width="67%">
                      <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="core:search">
                      <input type="hidden" name="_escape" value="Y">
                      <input type="hidden" name="module" value="radius">
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
