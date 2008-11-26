

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="voip_blacklist_add" name="voip_blacklist_add" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_blacklist}title_add{/translate}
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
                        { $list->menu("no", "voip_blacklist_voip_did_id", "voip_did", "did", $VAR.voip_blacklist_voip_did_id, "form_menu") }
                    </td>
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
                        <input type="text" name="voip_blacklist_src" value="{$VAR.voip_blacklist_src}" {if $voip_blacklist_src == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_blacklist}
                            field_dst
                        {/translate}</td>
                    <td width="65%">
                      <select name="voip_blacklist_dst">
                          <option value="Playback tt-monkeys">Screaming Monkeys</option>
                          <option value="Playback tt-somethingwrong">Something has gone terribly wrong</option>
                          <option value="Playback tt-weasels">Weasels have eaten the phone system</option>
                          <option value="Playback discon-or-out-of-service">Number disconnected or out of service</option>
                          <option value="Congestion">Fast Busy Signal</option>
                          <option value="Hangup">Hang up on caller</option>
                        </select>
                    </td>
                </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="voip_blacklist:view">
                      <input type="hidden" name="_page_current" value="voip_blacklist:add">
                      <input type="hidden" name="do[]" value="voip_blacklist:add">
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
