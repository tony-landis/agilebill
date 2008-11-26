

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="tax_add" name="tax_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=tax}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=tax}
                            field_country_id
                        {/translate}</td>
                    <td width="65%">
					{if $VAR.tax_country_id != ""}
                        { $list->menu("", "tax_country_id", "country", "name", $VAR.tax_country_id, "form_menu") }
					{else}
						{ $list->menu("", "tax_country_id", "country", "name", $smarty.const.DEFAULT_COUNTRY, "form_menu") }
					{/if}
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=tax}
                            field_zone
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="tax_zone" value="{$VAR.tax_zone}" {if $tax_zone == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=tax}
                            field_description
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="tax_description" value="{$VAR.tax_description}" {if $tax_description == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=tax}
                            field_rate
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="tax_rate" value="{$VAR.tax_rate}" {if $tax_rate == true}class="form_field_error"{/if} size="5">
                    </td>
                  </tr>
                <tr valign="middle" align="left">
                  <td>{translate module=tax} field_tax_id_collect{/translate}</td>
                  <td>{ $list->bool("tax_tax_id_collect", $VAR.tax_id_collect, "\" onChange=\"if (this.value==1)  document.getElementById('tax_id').style.display='block'; else document.getElementById('tax_id').style.display='none'; \"") } </td>
                </tr> 
				
           		<tr valign="top">
                    <td colspan="2">
					<div  id="tax_id" {style_hide}>
					  <table width="100%" class="row1"> 
						<tr valign="middle" align="left">
						  <td width="35%">{translate module=tax} field_tax_id_name{/translate}</td>
						  <td width="65%"><input type="text" name="tax_tax_id_name" value="{$VAR.tax_id_name}"  size="32"></td>
						</tr>
						<!-- <tr valign="middle" align="left">
						  <td>{translate module=tax} field_tax_id_exempt{/translate}</td>
						  <td> { $list->bool("tax_tax_id_exempt", $VAR.tax_id_exempt, "") } </td>
						</tr> -->
						<tr valign="middle" align="left">
						  <td>{translate module=tax} field_tax_id_req{/translate}</td>
						  <td>{ $list->bool("tax_tax_id_req", $VAR.tax_id_req, "") }</td>
						</tr>
						<tr valign="middle" align="left">
						  <td>{translate module=tax} field_tax_id_regex{/translate}</td>
						  <td><input type="text" name="tax_tax_id_regex" value="{$VAR.tax_id_regex}"  size="32"></td>
						</tr> 
				      </table> 
				      </div>
					</td>
                </tr> 
				
           		<tr valign="top">
           		  <td></td>
           		  <td><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="tax:view">
                    <input type="hidden" name="_page_current" value="tax:add">
                    <input type="hidden" name="do[]" value="tax:add"></td>
       		    </tr>
                </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
