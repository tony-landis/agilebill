
{ $method->exe("realestate","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="realestate_search" method="post" action="">
  {$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=realestate}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                        {html_select_account name="realestate_account_id" default=$VAR.realestate_account_id} </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_service_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_service_id" value="{$VAR.realestate_service_id}" {if $realestate_service_id == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_active
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_active", "all", "form_menu") }
                    </td>
                  </tr>				  
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_service_parent_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_service_parent_id" value="{$VAR.realestate_service_parent_id}" {if $realestate_service_parent_id == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_did
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_did" value="{$VAR.realestate_did}" {if $realestate_did == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_callingrateid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_callingrateid" value="{$VAR.realestate_callingrateid}" {if $realestate_callingrateid == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_calledrateid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_calledrateid" value="{$VAR.realestate_calledrateid}" {if $realestate_calledrateid == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_planid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_planid" value="{$VAR.realestate_planid}" {if $realestate_planid == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_cnam
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_cnam", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_channel
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_channel" value="{$VAR.realestate_channel}" {if $realestate_channel == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_voicemailenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_voicemailenabled", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_callforwardingenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_callforwardingenabled", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_busycallforwardingenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_busycallforwardingenabled", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_voicemailafter
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_voicemailafter", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_cfringfor
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_cfringfor", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_cfnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_cfnumber" value="{$VAR.realestate_cfnumber}" {if $realestate_cfnumber == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_bcfnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_bcfnumber" value="{$VAR.realestate_bcfnumber}" {if $realestate_bcfnumber == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_rxfax
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_rxfax", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_faxemail
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_faxemail" value="{$VAR.realestate_faxemail}" {if $realestate_faxemail == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_conf
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_conf", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_conflimit
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_conflimit" value="{$VAR.realestate_conflimit}" {if $realestate_conflimit == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_failover
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_failover", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_failovernumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_failovernumber" value="{$VAR.realestate_failovernumber}" {if $realestate_failovernumber == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_remotecallforward
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_remotecallforward", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_remotecallforwardnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_remotecallforwardnumber" value="{$VAR.realestate_remotecallforwardnumber}" {if $realestate_remotecallforwardnumber == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" name="limit" size="5" value="{$realestate_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$realestate item=record}
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
                      <input type="hidden" name="module" value="realestate">
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
