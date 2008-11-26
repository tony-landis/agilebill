

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="asset_add" name="asset_add" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=asset}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_pool_id
                        {/translate}</td>
                    <td width="65%"> 
						{ $list->menu("no", "asset_pool_id", "asset_pool", "name", $VAR.asset_pool_id, "") }                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_asset
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="asset_asset" value="{$VAR.asset_asset}" {if $asset_asset == true}class="form_field_error"{/if}>                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_misc
                        {/translate}</td>
                    <td width="65%">
                        <textarea name="asset_misc" cols="40" rows="5" {if $asset_misc == true}class="form_field_error"{/if}>{$VAR.asset_misc}</textarea>                    </td>
                </tr>
           <tr valign="top">
                    <td width="35%"></td>
                  <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="asset:view">
                      <input type="hidden" name="_page_current" value="asset:add">
                      <input type="hidden" name="do[]" value="asset:add">
                      <input type="hidden" name="asset_date_last" value="{$smarty.now}">
                    <input type="hidden" name="asset_date_orig" value="{$smarty.now}">
                    <input type="hidden" name="asset_status" value="0"></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
