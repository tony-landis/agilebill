<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
	{else}
	{$method->exe_noauth('voip','config_get')}
{/if}

<!-- Display the form to collect the input values -->
<form id="voip_add" name="voip_add" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip}title_config{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="40%">
                        {translate module=voip}
                            field_default_vm_passwd
                        {/translate}</td>
                    <td width="60%">
                        <input name="voip_vm_passwd" type="text" value="{$config.voip_vm_passwd}" size="4" maxlength="4" {if $voip_vm_passwd == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="40%">
                        {translate module=voip}
                            field_secret_generation
                        {/translate}</td>
                    <td width="60%">
                    <select name="voip_secret_gen" >
                      <option value="0" {if $config.voip_secret_gen == "0"}selected{/if}>
                      {translate module=voip}
                      type_random
                      {/translate}
                      </option>
                      <option value="1" {if $config.voip_secret_gen == "1"}selected{/if}>
                      {translate module=voip}
                      type_reverse
                      {/translate}
                      </option>
                      <option value="2" {if $config.voip_secret_gen == "2"}selected{/if}>
                      {translate module=voip}
                      type_same
                      {/translate}
                      </option>
                    </select>                        
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="40%">
                        {translate module=voip}
                            field_default_prefix
                        {/translate}</td>
                    <td width="60%">
                        <input name="voip_default_prefix" type="text" value="{$config.voip_default_prefix}" size="8" maxlength="8" {if $voip_default_prefix == true}class="form_field_error"{/if}>
                    </td>
                  </tr>                  
                <tr valign="top">
                    <td width="40%">
                        {translate module=voip}
                            field_intrastate_npas
                        {/translate}<br />
                        <i>{translate module=voip}
                            field_intrastate_npas_note
                        {/translate}</i></td>
                    <td width="60%">
                        <textarea id="voip_intrastate" name="voip_intrastate" {if $voip_intrastate == true}class="form_field_error"{/if} cols="50" rows="5">{$config.voip_intrastate}</textarea>
                    </td>
                  </tr>
                  
                <tr valign="top">
                    <td width="40%">
                        {translate module=voip}
                            field_perform_normalization
                        {/translate}</td>
                    <td width="60%">{ $list->bool("perform_normalization", $config.perform_normalization, "form_menu") }</td>
                  </tr>                  
                <tr valign="top">
                    <td width="40%">
                        {translate module=voip}
                            field_normalization_min_len
                        {/translate}</td>
                    <td width="60%">
                    	<input name="normalization_min_len" type="text" value="{$config.normalization_min_len}" size="3" maxlength="3" {if $normalization_min_len == true}class="form_field_error"{/if}>
                    </td>
                  </tr>     
                                    
                <tr valign="top">
                    <td width="40%">
                        {translate module=voip}
                            field_prepaid_low_balance
                        {/translate}</td>
                    <td width="60%">
                        <input name="prepaid_low_balance" type="text" value="{$config.prepaid_low_balance}" size="8" maxlength="8" {if $prepaid_low_balance == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="40%">
                        {translate module=voip}
                            field_auth_domain
                        {/translate}</td>
                    <td width="60%">
                        <input name="auth_domain" type="text" value="{$config.auth_domain}" size="32" maxlength="32" {if $auth_domain == true}class="form_field_error"{/if}>
                    </td>
                  </tr>  				  
           <tr valign="top">
                    <td width="40%"></td>
                    <td width="60%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="voip:config">
                      <input type="hidden" name="_page_current" value="voip:config">
                      <input type="hidden" name="do[]" value="voip:config">
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
