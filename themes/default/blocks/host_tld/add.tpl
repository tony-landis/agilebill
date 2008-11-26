
<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="host_tld_add" name="host_tld_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=host_tld}
                title_add 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { if $VAR.host_tld_status != ""}
                    { $list->bool("host_tld_status", $VAR.host_tld_status, "form_menu") }
                    {else}
                    { $list->bool("host_tld_status", "1", "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_tld_name" value="{$VAR.host_tld_name}" {if $host_tld_name == true}class="form_field_error"{/if} size="12">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_whois_plugin 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_files("", "host_tld_whois_plugin", $VAR.host_tld_whois_plugin, "whois_plugin", "", ".php", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_registrar_plugin_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu("", "host_tld_registrar_plugin_id", "host_registrar_plugin", "name", $VAR.host_tld_registrar_plugin_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_taxable 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_tld_taxable", $VAR.host_tld_taxable, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_auto_search 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_tld_auto_search", $VAR.host_tld_auto_search, "form_menu") }
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_default_term_new 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_tld_default_term_new" value="{$VAR.host_tld_default_term_new}" {if $host_tld_default_term_new == true}class="form_field_error"{/if} size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%">&nbsp;</td>
                  <td width="50%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="host_tld:view">
                    <input type="hidden" name="_page_current" value="host_tld:add">
                    <input type="hidden" name="do[]" value="host_tld:add">
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
