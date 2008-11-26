<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="voip_pool_add" name="voip_pool_add" method="post" action="" enctype="multipart/form-data">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_pool}title_import{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td colspan="2">
                        {translate module=voip_pool}
                            help_import
                        {/translate}</td>
                  </tr>
                <tr valign="top">
                    <td colspan="2">&nbsp;</td>
                  </tr>
                <tr valign="top">
                    <td width="33%">
                        {translate module=voip_pool}
                            field_filename
                        {/translate}</td>
                    <td width="66%">
                        <input name="datafile" type="file" {if $voip_pool_datafile == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
   				<tr valign="top">
   					<td width="33%">
   					{translate module=voip_pool}
   						field_did_plugin
   						{/translate}
   					</td>
   					<td width="66%">
   						{html_menu field=voip_did_plugin_id assoc_table=voip_did_plugin assoc_field=name default=1}
   					</td>
   				</tr>
           <tr valign="top">
                    <td width="33%"></td>
                    <td width="66%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="voip_pool:import">
                      <input type="hidden" name="_page_current" value="voip_pool:import">
                      <input type="hidden" name="do[]" value="voip_pool:import">
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
