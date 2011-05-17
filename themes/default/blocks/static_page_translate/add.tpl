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

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="static_page_translate_add" name="static_page_translate_add" method="post" action="">
{$COOKIE_FORM}
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="5">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=static_page_translate}
                title_add 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="23%"> 
                    {translate module=static_page_translate}
                    field_static_page_id 
                    {/translate}
                  </td>
                  <td width="77%"> 
                    {if $VAR.id == ""}
                    { $list->menu("", "static_page_translate_static_page_id", "static_page", "name", $VAR.static_page_translate_static_page_id, "form_menu") }
                    {else}
                    { $list->menu("", "static_page_translate_static_page_id", "static_page", "name", $VAR.id, "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="23%"> 
                    {translate module=static_page_translate}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="77%"> 
                    {if $VAR.static_page_translate_language_id == ""}
                    { $list->menu_files("", "static_page_translate_language_id", $smarty.const.DEFAULT_LANGUAGE, "language", "", "_core.xml", "form_menu") }
                    {else}
                    { $list->menu_files("", "static_page_translate_language_id", $VAR.static_page_translate_language_id, "language", "", "_core.xml", "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="23%"> 
                    {translate module=static_page_translate}
                    field_title 
                    {/translate}
                  </td>
                  <td width="77%"> 
                    <input type="text" name="static_page_translate_title" value="{$VAR.static_page_translate_title}" {if $static_page_translate_title == true}class="form_field_error"{/if} size="40">
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
                <tr> 
                  <td> <b> 
                    {translate module=static_page_translate}
                    field_body_intro 
                    {/translate}
                    </b> </td>
                </tr>
                <tr> 
                  <td> 
                    <textarea id="static_page_translate_body_intro" name="static_page_translate_body_intro" mce_editable="true" cols="80" rows="12">{$VAR.static_page_translate_body_intro}</textarea>
                  </td>
                </tr>
                <tr> 
                  <td> <b> 
                    {translate module=static_page_translate}
                    field_body_full 
                    {/translate}
                    </b> </td>
                </tr>
                <tr> 
                  <td> 
                    <textarea id="static_page_translate_body_full" name="static_page_translate_body_full" mce_editable="true" cols="80" rows="18">{$VAR.static_page_translate_body_full}</textarea>
                  </td>
                </tr>
              </table>
              <p>
<input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                <input type="hidden" name="_page" value="static_page_translate:view">
                <input type="hidden" name="_page_current" value="static_page_translate:add">
                <input type="hidden" name="do[]" value="static_page_translate:add">
                <input type="hidden" name="static_page_translate_date_last" value="{$smarty.now}">
                <input type="hidden" name="static_page_translate_date_orig" value="{$smarty.now}">
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
