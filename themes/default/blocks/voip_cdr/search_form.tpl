
{ $method->exe("voip_cdr","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="voip_cdr_search" method="post" action="">
  {$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_cdr}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("voip_cdr_date_orig", $VAR.voip_cdr_date_orig, "form_field", "") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                        {html_select_account name=&quot;voip_cdr_account_id&quot; default=$VAR.voip_cdr_account_id}</td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_voip_rate_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_voip_rate_id" value="{$VAR.voip_cdr_voip_rate_id}" {if $voip_cdr_voip_rate_id == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_clid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_clid" value="{$VAR.voip_cdr_clid}" {if $voip_cdr_clid == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_src
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_src" value="{$VAR.voip_cdr_src}" {if $voip_cdr_src == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_dst
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_dst" value="{$VAR.voip_cdr_dst}" {if $voip_cdr_dst == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_dcontext
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_dcontext" value="{$VAR.voip_cdr_dcontext}" {if $voip_cdr_dcontext == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_channel
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_channel" value="{$VAR.voip_cdr_channel}" {if $voip_cdr_channel == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_dstchannel
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_dstchannel" value="{$VAR.voip_cdr_dstchannel}" {if $voip_cdr_dstchannel == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_lastapp
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_lastapp" value="{$VAR.voip_cdr_lastapp}" {if $voip_cdr_lastapp == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_lastdata
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_lastdata" value="{$VAR.voip_cdr_lastdata}" {if $voip_cdr_lastdata == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_duration
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_duration" value="{$VAR.voip_cdr_duration}" {if $voip_cdr_duration == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_billsec
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_billsec" value="{$VAR.voip_cdr_billsec}" {if $voip_cdr_billsec == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_disposition
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_disposition" value="{$VAR.voip_cdr_disposition}" {if $voip_cdr_disposition == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_amaflags
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_amaflags" value="{$VAR.voip_cdr_amaflags}" {if $voip_cdr_amaflags == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_accountcode
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_accountcode" value="{$VAR.voip_cdr_accountcode}" {if $voip_cdr_accountcode == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_uniqueid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_uniqueid" value="{$VAR.voip_cdr_uniqueid}" {if $voip_cdr_uniqueid == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_cdrid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_cdrid" value="{$VAR.voip_cdr_cdrid}" {if $voip_cdr_cdrid == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_amount
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_amount" value="{$VAR.voip_cdr_amount}" {if $voip_cdr_amount == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_calltype
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_calltype" value="{$VAR.voip_cdr_calltype}" {if $voip_cdr_calltype == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_realamount
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_cdr_realamount" value="{$VAR.voip_cdr_realamount}" {if $voip_cdr_realamount == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" name="limit" size="5" value="{$voip_cdr_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$voip_cdr item=record}
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
                      <input type="hidden" name="module" value="voip_cdr">
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
