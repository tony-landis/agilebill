<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="asset_add" name="asset_add" method="post" action="" enctype="multipart/form-data">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=asset}title_import{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td colspan="2">
                        {translate module=asset}
                            help_import
                        {/translate}</td>
                  </tr>
                <tr valign="top">
                    <td colspan="2">&nbsp;</td>
                  </tr>
                <tr valign="top">
                    <td width="33%">
                        {translate module=asset}
                            field_filename
                        {/translate}</td>
                    <td width="66%">
                        <input name="datafile" type="file" {if $asset_datafile == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
   				<tr valign="top">
   					<td width="33%">
   					{translate module=asset}
   						pool
   						{/translate}
   					</td>
   					<td width="66%">
   						{html_menu field=asset_pool_id assoc_table=asset_pool assoc_field=name default=1}
   					</td>
   				</tr>
           <tr valign="top">
                    <td width="33%"></td>
                    <td width="66%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="asset:import">
                      <input type="hidden" name="_page_current" value="asset:import">
                      <input type="hidden" name="do[]" value="asset:import">
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
