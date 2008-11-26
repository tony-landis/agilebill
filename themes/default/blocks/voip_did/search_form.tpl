
{ $method->exe("voip_did","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="voip_did_search" method="post" action="">
  {$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_did}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                        {html_select_account name="voip_did_account_id" default=$VAR.voip_did_account_id} </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_service_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_service_id" value="{$VAR.voip_did_service_id}" {if $voip_did_service_id == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_active
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_active", "all", "form_menu") }
                    </td>
                  </tr>				  
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_service_parent_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_service_parent_id" value="{$VAR.voip_did_service_parent_id}" {if $voip_did_service_parent_id == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_did
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_did" value="{$VAR.voip_did_did}" {if $voip_did_did == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_callingrateid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_callingrateid" value="{$VAR.voip_did_callingrateid}" {if $voip_did_callingrateid == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_calledrateid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_calledrateid" value="{$VAR.voip_did_calledrateid}" {if $voip_did_calledrateid == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_planid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_planid" value="{$VAR.voip_did_planid}" {if $voip_did_planid == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_cnam
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_cnam", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_channel
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_channel" value="{$VAR.voip_did_channel}" {if $voip_did_channel == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_voicemailenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_voicemailenabled", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_callforwardingenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_callforwardingenabled", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_busycallforwardingenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_busycallforwardingenabled", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_voicemailafter
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_voicemailafter", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_cfringfor
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_cfringfor", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_cfnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_cfnumber" value="{$VAR.voip_did_cfnumber}" {if $voip_did_cfnumber == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_bcfnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_bcfnumber" value="{$VAR.voip_did_bcfnumber}" {if $voip_did_bcfnumber == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_rxfax
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_rxfax", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_faxemail
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_faxemail" value="{$VAR.voip_did_faxemail}" {if $voip_did_faxemail == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_conf
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_conf", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_conflimit
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_conflimit" value="{$VAR.voip_did_conflimit}" {if $voip_did_conflimit == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_failover
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_failover", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_failovernumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_failovernumber" value="{$VAR.voip_did_failovernumber}" {if $voip_did_failovernumber == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_remotecallforward
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_remotecallforward", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_remotecallforwardnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_remotecallforwardnumber" value="{$VAR.voip_did_remotecallforwardnumber}" {if $voip_did_remotecallforwardnumber == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" name="limit" size="5" value="{$voip_did_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$voip_did item=record}
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
                      <input type="hidden" name="module" value="voip_did">
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
