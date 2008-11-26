
{ $method->exe("voip_blacklist","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="voip_blacklist_search" method="post" action="">
  {$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_blacklist}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_blacklist}
                            field_voip_did_id
                        {/translate}</td>
                    <td width="65%">
                        { $list->menu("no", "voip_blacklist_voip_did_id", "voip_did", "did", $VAR.voip_blacklist_voip_did_id, "form_menu") } </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_blacklist}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                        {html_select_account name="voip_blacklist_account_id" default=$VAR.voip_blacklist_voip_did_id} </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_blacklist}
                            field_src
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_blacklist_src" value="{$VAR.voip_blacklist_src}" {if $voip_blacklist_src == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_blacklist}
                            field_dst
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_blacklist_dst" value="{$VAR.voip_blacklist_dst}" {if $voip_blacklist_dst == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" name="limit" size="5" value="{$voip_blacklist_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$voip_blacklist item=record}
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
                      <input type="hidden" name="module" value="voip_blacklist">
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
