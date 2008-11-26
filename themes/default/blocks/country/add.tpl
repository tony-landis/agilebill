
<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="country_add" name="country_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=country}
                title_add
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=country}
                    field_name
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="country_name" value="{$VAR.country_name}" {if $country_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=country}
                    field_two_code 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="country_two_code" value="{$VAR.country_two_code}" {if $country_two_code == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=country}
                    field_three_code 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="country_three_code" value="{$VAR.country_three_code}" {if $country_three_code == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=country}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="country_description" cols="40" rows="5" {if $country_description == true}class="form_field_error"{/if}>{$VAR.country_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=country}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="country_notes" cols="40" rows="5" {if $country_notes == true}class="form_field_error"{/if}>{$VAR.country_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="country:view">
                    <input type="hidden" name="_page_current" value="country:add">
                    <input type="hidden" name="do[]" value="country:add">
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
