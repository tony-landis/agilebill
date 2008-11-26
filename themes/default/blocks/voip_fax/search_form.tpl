
{ $method->exe("voip_fax","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="voip_fax_search" method="post" action="">
  {$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_fax}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                        {html_select_account name=&quot;voip_fax_account_id&quot; default=$VAR.voip_fax_account_id}</td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("voip_fax_date_orig", $VAR.voip_fax_date_orig, "form_field", "") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_clid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_fax_clid" value="{$VAR.voip_fax_clid}" {if $voip_fax_clid == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_src
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_fax_src" value="{$VAR.voip_fax_src}" {if $voip_fax_src == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_dst
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_fax_dst" value="{$VAR.voip_fax_dst}" {if $voip_fax_dst == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_pages
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_fax_pages" value="{$VAR.voip_fax_pages}" {if $voip_fax_pages == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_image_size
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_fax_image_size" value="{$VAR.voip_fax_image_size}" {if $voip_fax_image_size == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_image_resolution
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_fax_image_resolution" value="{$VAR.voip_fax_image_resolution}" {if $voip_fax_image_resolution == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_transfer_rate
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_fax_transfer_rate" value="{$VAR.voip_fax_transfer_rate}" {if $voip_fax_transfer_rate == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_image_bytes
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_fax_image_bytes" value="{$VAR.voip_fax_image_bytes}" {if $voip_fax_image_bytes == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_bad_rows
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_fax_bad_rows" value="{$VAR.voip_fax_bad_rows}" {if $voip_fax_bad_rows == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_fax}
                            field_mime_type
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_fax_mime_type" value="{$VAR.voip_fax_mime_type}" {if $voip_fax_mime_type == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" name="limit" size="5" value="{$voip_fax_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$voip_fax item=record}
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
                      <input type="hidden" name="module" value="voip_fax">
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
