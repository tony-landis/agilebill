

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="realestate_add" name="realestate_add" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=realestate}title_add{/translate}
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
						{html_select_account name="realestate_account_id" default=$VAR.realestate_account_id} 
					</td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_active
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_active", $VAR.realestate_active, "form_menu") }
                    </td>
                </tr>				
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_service_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_service_id" value="{$VAR.realestate_service_id}" {if $realestate_service_id == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_service_parent_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_service_parent_id" value="{$VAR.realestate_service_parent_id}" {if $realestate_service_parent_id == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_did
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_did" value="{$VAR.realestate_did}" {if $realestate_did == true}class="form_field_error"{/if}>
                    </td>
                </tr>

                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_cnam
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_cnam", $VAR.realestate_cnam, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_blacklist
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_blacklist", $VAR.realestate_blacklist, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_anirouting
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_anirouting", $VAR.realestate_anirouting, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_faxdetection
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_faxdetection", $VAR.realestate_faxdetection, "form_menu") }
                    </td>
                </tr>                               
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_channel
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_channel" value="{$VAR.realestate_channel}" {if $realestate_channel == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_channelarg
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_channelarg" value="{$VAR.realestate_channelarg}" {if $realestate_channelarg == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_voicemailenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_voicemailenabled", $VAR.realestate_voicemailenabled, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_callforwardingenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_callforwardingenabled", $VAR.realestate_callforwardingenabled, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_busycallforwardingenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_busycallforwardingenabled", $VAR.realestate_busycallforwardingenabled, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_voicemailafter
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_voicemailafter", $VAR.realestate_voicemailafter, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_cfringfor
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_cfringfor", $VAR.realestate_cfringfor, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_cfnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_cfnumber" value="{$VAR.realestate_cfnumber}" {if $realestate_cfnumber == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_bcfnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_bcfnumber" value="{$VAR.realestate_bcfnumber}" {if $realestate_bcfnumber == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_rxfax
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_rxfax", $VAR.realestate_rxfax, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_faxemail
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_faxemail" value="{$VAR.realestate_faxemail}" {if $realestate_faxemail == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_conf
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_conf", $VAR.realestate_conf, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_conflimit
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_conflimit" value="{$VAR.realestate_conflimit}" {if $realestate_conflimit == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_failover
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_failover", $VAR.realestate_failover, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_failovernumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_failovernumber" value="{$VAR.realestate_failovernumber}" {if $realestate_failovernumber == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_remotecallforward
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("realestate_remotecallforward", $VAR.realestate_remotecallforward, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=realestate}
                            field_remotecallforwardnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="realestate_remotecallforwardnumber" value="{$VAR.realestate_remotecallforwardnumber}" {if $realestate_remotecallforwardnumber == true}class="form_field_error"{/if}>
                    </td>
                </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="realestate:view">
                      <input type="hidden" name="_page_current" value="realestate:add">
                      <input type="hidden" name="do[]" value="realestate:add">
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
