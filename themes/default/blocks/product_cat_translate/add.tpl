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
		width : "100%"
	}); 
</script>
<!-- /tinyMCE -->{/literal}

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="product_cat_translate_add" name="product_cat_translate_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=product_cat_translate}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat_translate}
                    field_product_cat_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.id == ""}
                    { $list->menu("", "product_cat_translate_product_cat_id", "product_cat", "name", $VAR.product_cat_translate_product_cat_id, "form_menu") }
                    {else}
                    { $list->menu("", "product_cat_translate_product_cat_id", "product_cat", "name", $VAR.id, "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat_translate}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.product_cat_translate_language_id != ""}
                    { $list->menu_files("", "product_cat_translate_language_id", $VAR.product_cat_translate_language_id, "language", "", "_core.xml", "form_menu") }
                    {else}
                    { $list->menu_files("", "product_cat_translate_language_id", $smarty.const.DEFAULT_LANGUAGE, "language", "", "_core.xml", "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat_translate}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="product_cat_translate_name" value="{$VAR.product_cat_translate_name}" {if $product_cat_translate_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat_translate}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%">&nbsp; </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="65%" align="center"> 
                    <textarea name="product_cat_translate_description" cols="80" rows="13" {if $product_cat_translate_description == true}class="form_field_error"{/if} mce_editable="true">{$VAR.product_cat_translate_description}</textarea>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="8">
                <tr> 
                  <td>&nbsp; </td>
                  <td align="right">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="product_cat_translate:view">
                    <input type="hidden" name="_page_current" value="product_cat_translate:add">
                    <input type="hidden" name="do[]" value="product_cat_translate:add">
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
