

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="htaccess_exclude_add" name="htaccess_exclude_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=htaccess_exclude}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=htaccess_exclude}
                            field_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="htaccess_exclude_name" value="{$VAR.htaccess_exclude_name}" {if $htaccess_exclude_name == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=htaccess_exclude}
                            field_extension
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="htaccess_exclude_extension" value="{$VAR.htaccess_exclude_extension}" {if $htaccess_exclude_extension == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="htaccess_exclude:view">
                      <input type="hidden" name="_page_current" value="htaccess_exclude:add">
                      <input type="hidden" name="do[]" value="htaccess_exclude:add">
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
