
{ $method->exe("blocked_ip","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="blocked_ip_search" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=blocked_ip}
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
                    {translate module=blocked_ip}
                    field_ip 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="blocked_ip_ip" value="{$VAR.blocked_ip_ip}" {if $blocked_ip_ip == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp;
                    {translate}
                    search_partial
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=blocked_ip}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="blocked_ip_notes" value="{$VAR.blocked_ip_notes}" {if $blocked_ip_notes == true}class="form_field_error"{/if}>
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
                    <input type="text"  name="limit" size="5" value="{$blocked_ip_limit}">
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
                      {foreach from=$blocked_ip item=record}
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
                    <input type="hidden" name="module" value="blocked_ip">
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
