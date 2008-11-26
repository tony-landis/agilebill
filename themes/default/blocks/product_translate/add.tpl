{ $block->display("core:top_clean") }
 
<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="product_translate_add" name="product_translate_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_translate}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="product_translate_name" value="{$VAR.product_translate_name}" {if $product_translate_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_translate}
                    field_description_short 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="product_translate_description_short" value="{$VAR.product_translate_description_short}" {if $product_translate_description_short == true}class="form_field_error"{/if} size="50">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_translate}
                    field_description_full 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="product_translate_description_full" cols="50" rows="3" {if $product_translate_description_full == true}class="form_field_error"{/if}>{$VAR.product_translate_description_full}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_translate}
                    field_email_template 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="product_translate_email_template" cols="50" rows="3" {if $product_translate_email_template == true}class="form_field_error"{/if}>{$VAR.product_translate_email_template}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="product_translate:view">
                    <input type="hidden" name="_page_current" value="product_translate:add">
                    <input type="hidden" name="do[]" value="product_translate:add">
                    <input type="hidden" name="product_translate_product_id" value="{$VAR.product_translate_product_id}">
                    <input type="hidden" name="product_translate_language_id" value="{$VAR.product_translate_language_id}">
                    <input type="hidden" name="_escape" value="1">
                    <input type="hidden" name="_escape_next" value="1">
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
