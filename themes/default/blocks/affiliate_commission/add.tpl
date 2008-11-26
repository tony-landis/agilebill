

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="affiliate_commission_add" name="affiliate_commission_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=affiliate_commission}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate_commission}
                    field_date_begin 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("affiliate_commission_start_date", $VAR.affiliate_commission_start_date, "form_field") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate_commission}
                    field_date_end 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("affiliate_commission_end_date", $VAR.affiliate_commission_end_date, "form_field") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="core:blank">
                    <input type="hidden" name="_page_current" value="affiliate_commission:add">
                    <input type="hidden" name="do[]" value="affiliate_commission:add">
                    <input type="hidden" name="GenID" value="{$smarty.now}">
                    <input type="hidden" name="page" value="1">
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
