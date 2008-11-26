

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="report_add" name="report_add" method="post" action="">
{$COOKIE_FORM}
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=report}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=report}
                    field_template 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="report_template" value="{$VAR.report_template}" {if $report_template == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=report}
                    field_nickname 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="report_nickname" value="{$VAR.report_nickname}" {if $report_nickname == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="report:view">
                    <input type="hidden" name="_page_current" value="report:add">
                    <input type="hidden" name="do[]" value="report:add">
                    <input type="hidden" name="report_date_last" value="{$smarty.now}">
                    <input type="hidden" name="report_date_last2" value="{$smarty.now}">
                    <input type="hidden" name="report_module" value="{$VAR.report_module}">
                    <input type="hidden" name="report_template" value="{$VAR.report_template}">
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
