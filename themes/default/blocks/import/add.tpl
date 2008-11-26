

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="import_add" name="import_add" method="post" action="">
{$COOKIE_FORM}
<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=import}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_date_orig" value="{$VAR.import_date_orig}" {if $import_date_orig == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_plugin
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_plugin" value="{$VAR.import_plugin}" {if $import_plugin == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_module
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_module" value="{$VAR.import_module}" {if $import_module == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_local_table
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_local_table" value="{$VAR.import_local_table}" {if $import_local_table == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_ab_table
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_ab_table" value="{$VAR.import_ab_table}" {if $import_ab_table == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_remote_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_remote_id" value="{$VAR.import_remote_id}" {if $import_remote_id == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_ab_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_ab_id" value="{$VAR.import_ab_id}" {if $import_ab_id == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="import:view">
                      <input type="hidden" name="_page_current" value="import:add">
                      <input type="hidden" name="do[]" value="import:add">
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
