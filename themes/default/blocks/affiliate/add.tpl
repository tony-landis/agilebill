<!-- Display the form to collect the input values -->
<form id="affiliate_add" name="affiliate_add" method="post" action="">
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=affiliate}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="affiliate_account_id" default=$VAR.affiliate_account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate}
                    affiliate_template
                    {/translate}
                  </td>
                  <td width="65%"> 
				  {if $VAR.affiliate_affiliate_plugin_id == "" }
                    { $list->menu("", "affiliate_template_id", "affiliate_template", "name", $smarty.const.DEFAULT_AFFILIATE_TEMPLATE, "form_menu") }
				  {else}
                    { $list->menu("", "affiliate_template_id", "affiliate_template", "name", $VAR.affiliate_affiliate_plugin_id, "form_menu") }
				  {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="affiliate:view">
                    <input type="hidden" name="_page_current" value="affiliate:add">
                    <input type="hidden" name="do[]" value="affiliate:add">
                    <input type="hidden" name="affiliate_date_orig" value="{$smarty.now}">
                    <input type="hidden" name="affiliate_date_last" value="{$smarty.now}">
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
