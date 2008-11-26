

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="voip_did_add" name="voip_did_add" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_did}title_add{/translate}
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
						{html_select_account name="voip_did_account_id" default=$VAR.voip_did_account_id} 
					</td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_active
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_active", $VAR.voip_did_active, "form_menu") }
                    </td>
                </tr>				
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_service_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_service_id" value="{$VAR.voip_did_service_id}" {if $voip_did_service_id == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_service_parent_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_service_parent_id" value="{$VAR.voip_did_service_parent_id}" {if $voip_did_service_parent_id == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_did
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_did" value="{$VAR.voip_did_did}" {if $voip_did_did == true}class="form_field_error"{/if}>
                    </td>
                </tr>

                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_cnam
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_cnam", $VAR.voip_did_cnam, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_blacklist
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_blacklist", $VAR.voip_did_blacklist, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_anirouting
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_anirouting", $VAR.voip_did_anirouting, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_faxdetection
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_faxdetection", $VAR.voip_did_faxdetection, "form_menu") }
                    </td>
                </tr>                               
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_channel
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_channel" value="{$VAR.voip_did_channel}" {if $voip_did_channel == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_channelarg
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_channelarg" value="{$VAR.voip_did_channelarg}" {if $voip_did_channelarg == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_voicemailenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_voicemailenabled", $VAR.voip_did_voicemailenabled, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_callforwardingenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_callforwardingenabled", $VAR.voip_did_callforwardingenabled, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_busycallforwardingenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_busycallforwardingenabled", $VAR.voip_did_busycallforwardingenabled, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_voicemailafter
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_voicemailafter", $VAR.voip_did_voicemailafter, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_cfringfor
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_cfringfor", $VAR.voip_did_cfringfor, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_cfnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_cfnumber" value="{$VAR.voip_did_cfnumber}" {if $voip_did_cfnumber == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_bcfnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_bcfnumber" value="{$VAR.voip_did_bcfnumber}" {if $voip_did_bcfnumber == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_rxfax
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_rxfax", $VAR.voip_did_rxfax, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_faxemail
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_faxemail" value="{$VAR.voip_did_faxemail}" {if $voip_did_faxemail == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_conf
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_conf", $VAR.voip_did_conf, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_conflimit
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_conflimit" value="{$VAR.voip_did_conflimit}" {if $voip_did_conflimit == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_failover
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_failover", $VAR.voip_did_failover, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_failovernumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_failovernumber" value="{$VAR.voip_did_failovernumber}" {if $voip_did_failovernumber == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_remotecallforward
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_remotecallforward", $VAR.voip_did_remotecallforward, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_remotecallforwardnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_remotecallforwardnumber" value="{$VAR.voip_did_remotecallforwardnumber}" {if $voip_did_remotecallforwardnumber == true}class="form_field_error"{/if}>
                    </td>
                </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="voip_did:view">
                      <input type="hidden" name="_page_current" value="voip_did:add">
                      <input type="hidden" name="do[]" value="voip_did:add">
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
