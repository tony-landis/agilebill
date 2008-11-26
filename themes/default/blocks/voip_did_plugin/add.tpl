

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="voip_did_plugin_add" name="voip_did_plugin_add" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_did_plugin}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did_plugin}
                            field_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_plugin_name" value="{$VAR.voip_did_plugin_name}" {if $voip_did_plugin_name == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did_plugin}
                            field_plugin
                        {/translate}</td>
                    <td width="65%">{html_menu_files path=voip_did field=voip_did_plugin_plugin} </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did_plugin}
                            field_avail_countries
                        {/translate}</td>
                    <td width="65%">
                    	{html_menu_multi name=voip_did_plugin_avail_countries assoc_table="voip_iso_country_code" assoc_field="name" size=15} 
					</td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did_plugin}
                            field_release_minutes
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_plugin_release_minutes" value="{$VAR.voip_did_plugin_release_minutes}" {if $voip_did_plugin_release_minutes == true}class="form_field_error"{/if}>
                    </td>
                </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="voip_did_plugin:view">
                      <input type="hidden" name="_page_current" value="voip_did_plugin:add">
                      <input type="hidden" name="do[]" value="voip_did_plugin:add">
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
