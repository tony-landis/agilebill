{literal}<!-- tinyMCE -->
<script language="javascript" type="text/javascript" src="includes/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		mode : "specific_textareas",
		theme : "advanced",
		plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print",
		theme_advanced_buttons1_add : "fontselect,fontsizeselect",
		theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
		theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
		theme_advanced_buttons3_add_before : "tablecontrols,separator",
		theme_advanced_buttons3_add : "iespell,media,advhr",
		theme_advanced_toolbar_location : "bottom",
		theme_advanced_toolbar_align : "center", 
		content_css : "themes/default_admin/style.css",
	    plugin_insertdate_dateFormat : "%Y-%m-%d",
	    plugin_insertdate_timeFormat : "%H:%M:%S",
		extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
		relative_urls: 'false',
		width : "100%"
	}); 
</script>
<!-- /tinyMCE -->{/literal}

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="product_add" name="product_add" method="post" action="">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=product}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_sku 
                    {/translate}
                    </b> </td>
                  <td width="50%"> <b> 
                    {translate module=product_translate}
                    field_name 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <input type="text" name="product_sku" value="{$VAR.product_sku}" {if $product_sku == true}class="form_field_error"{/if}>
                  </td>
                  <td width="50%"> 
                    <input type="text" name="translate_name" value="{$VAR.translate_name}" >
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_price_type 
                    {/translate}
                    </b> </td>
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_avail_category_id 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <input type="radio" name="product_price_type" value="0" {if $VAR.product_price_type == "0" || $VAR.product_price_type == "" }checked{/if}>
                    {translate module=product}
                    price_type_one 
                    {/translate}
                    <br>
                    <input type="radio" name="product_price_type" value="1" {if $VAR.product_price_type == "1"}checked{/if}>
                    {translate module=product}
                    price_type_recurr 
                    {/translate}
                    <br>
                    <input type="radio" name="product_price_type" value="2" {if $VAR.product_price_type == "2"}checked{/if}>
                    {translate module=product}
                    price_type_trial 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($VAR.product_avail_category_id, "product_avail_category_id", "product_cat", "name", "5", "", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                      <tr> 
                        <td width="50%"><b> 
                          {translate module=product}
                          field_price_base 
                          {/translate}
                          </b></td>
                        <td width="50%"><b> 
                          {translate module=product}
                          field_price_setup 
                          {/translate}
                          </b> </td>
                      </tr>
                    </table>
                  </td>
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_taxable 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                      <tr> 
                        <td width="50%"> 
                          <input type="text" name="product_price_base" value="{$VAR.product_price_base}" {if $product_price_base == true}class="form_field_error"{/if} size="5">
                          {$list->currency_iso("")}
                        </td>
                        <td width="50%"> 
                          <input type="text" name="product_price_setup" value="{$VAR.product_price_setup}" {if $product_price_setup == true}class="form_field_error"{/if} size="5">
                          {$list->currency_iso("")}
                        </td>
                      </tr>
                    </table>
                    <b> </b></td>
                  <td width="50%"> 
                    {if $VAR.product_taxable != ""}
                    { $list->bool("product_taxable", $VAR.product_taxable, "form_menu") }
                    {else}
                    { $list->bool("product_taxable", "1", "form_menu") }
                    {/if}
                  </td>
                </tr>
              </table>
              <br>
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
                <tr> 
                  <td> <b>
                    {translate module=product_translate}
                    field_description_short 
                    {/translate}
                    </b> </td>
                </tr>
                <tr> 
                  <td>
<textarea name="translate_description_short" cols="80" rows="10" mce_editable="true">{$VAR.translate_description_short}</textarea>
                  </td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                </tr>
                <tr> 
                  <td> <b>
                    {translate module=product_translate}
                    field_description_full 
                    {/translate}
                    </b> </td>
                </tr>
                <tr> 
                  <td>
                    <textarea name="translate_description_full" cols="80" rows="13" mce_editable="true">{$VAR.translate_description_full}</textarea>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="product:view">
                    <input type="hidden" name="_page_current" value="product:add">
                    <input type="hidden" name="do[]" value="product:add">
                    <input type="hidden" name="product_active" value="1">
                    <input type="hidden" name="product_plugin" value="0">
                    <input type="hidden" name="product_discount" value="0">
                    <input type="hidden" name="product_date_orig" value="{$list->date($smarty.now)}">
                    <input type="hidden" name="product_date_last" value="{$smarty.now}">
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
