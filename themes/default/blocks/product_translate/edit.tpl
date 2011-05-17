{ $block->display("core:top_clean") }

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
		relative_urls: 'true',
		width : "100%"
	}); 
</script>
<!-- /tinyMCE -->{/literal} 

{ $method->exe("product_translate","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

 <!-- Loop through each record -->
{foreach from=$product_translate item=product_translate}
<a name="{$product_translate.id}"></a> 
<!-- Display the field validation -->
{if $form_validation}
{ $block->display("core:alert_fields") }
{/if}
<!-- Display each record -->
<form name="product_translate_view" method="post" action="">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="body">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> <b>
                    {translate module=product_translate}
                    field_name 
                    {/translate}
                    </b> </td>
                  <td width="65%"> 
                    <input type="text" name="product_translate_name" value="{$product_translate.name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> <b> 
                    {translate module=product_translate}
                    field_email_template 
                    {/translate}
                    <br>
                    <br>
                    <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                    </b> </td>
                  <td width="65%"> 
                    <textarea  name="product_translate_email_template" rows="3" cols="55" >{$product_translate.email_template}</textarea>
                  </td>
                </tr>
              </table>
              <br>
              <table width="100%" border="0" cellspacing="0" cellpadding="3" class="body">
                <tr> 
                  <td> <b>
                    {translate module=product_translate}
                    field_description_short 
                    {/translate}
                    </b> </td>
                </tr>
                <tr> 
                  <td>
                    <textarea name="product_translate_description_short" rows="10" cols="80" mce_editable="true">{$product_translate.description_short}</textarea>
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
                    <textarea name="product_translate_description_full" rows="13" cols="80" mce_editable="true">{$product_translate.description_full}</textarea>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td align="right"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button"> 
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="product_translate:edit">
    <input type="hidden" name="product_translate_id" value="{$product_translate.id}">
    <input type="hidden" name="do[]" value="product_translate:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  <input type="hidden" name="product_translate_product_id" value="{$product_translate.product_id}">
  <input type="hidden" name="product_translate_language_id" value="{$product_translate.language_id}">
</form>
  {/foreach}
{/if}
