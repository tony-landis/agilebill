<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="task_add" name="task_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_rate_prod}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate_prod}
                    field_product_id 
                    {/translate}
                  </td>
                  <td width="65%"> { $list->menu("no", "voip_rate_prod_product_id", "product", "sku", 'all', "form_field") } 
                  </td>
                </tr>

                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="voip_rate_prod:view">
                    <input type="hidden" name="_page_current" value="voip_rate_prod:add">
                    <input type="hidden" name="do[]" value="voip_rate_prod:add">
                    <input type="hidden" name="voip_rate_prod_voip_rate_id" value="{$VAR.voip_rate_prod_voip_rate_id}" >
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
