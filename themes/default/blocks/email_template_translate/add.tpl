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
<form id="email_template_translate_add" name="email_template_translate_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=email_template_translate}
                title_add
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="175"> 
                    {translate module=email_template_translate}
                    field_email_template_id 
                    {/translate}
                  </td>
                  <td> 
                    {if $VAR.id != ""}
                    { $list->menu("", "email_template_translate_email_template_id", "email_template", "name", $VAR.id, "form_menu") }
                    {else}
                    { $list->menu("", "email_template_translate_email_template_id", "email_template", "name", $VAR.email_template_translate_email_template_id, "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="175"> 
                    {translate module=email_template_translate}
                    field_language_id 
                    {/translate}
                  </td>
                  <td> 
                    {if $VAR.email_template_translate_language_id == ""}
                    { $list->menu_files("", "email_template_translate_language_id", $smarty.const.DEFAULT_LANGUAGE, "language", "", "_core.xml", "form_menu") }
                    {else}
                    { $list->menu_files("", "email_template_translate_language_id", $VAR.email_template_translate_language_id, "language", "", "_core.xml", "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="175"> 
                    {translate module=email_template_translate}
                    field_subject 
                    {/translate}
                  </td>
                  <td> 
                    <input type="text" name="email_template_translate_subject" value="{$VAR.email_template_translate_subject}" {if $email_template_translate_subject == true}class="form_field_error"{/if}>
                  </td>
                </tr>
              </table>
              <br>
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
                <tr> 
                  <td> 
                    {translate module=email_template_translate}
                    field_message_text 
                    {/translate}
                  </td>
                </tr>
                <tr> 
                  <td align="center"> 
                    <textarea name="email_template_translate_message_text" cols="90" rows="12" {if $email_template_translate_message_text == true}class="form_field_error"{/if}>{$VAR.email_template_translate_message_text}</textarea>
                  </td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                </tr>
                <tr> 
                  <td> 
                    {translate module=email_template_translate}
                    field_message_html 
                    {/translate}
                  </td>
                </tr>
                <tr> 
                  <td> 
                    <textarea id="email_template_translate_message_html" name="email_template_translate_message_html" mce_editable="true" cols="85" rows="17">{$VAR.email_template_translate_message_html}</textarea>
                  </td>
                </tr>
                <tr>
                  <td>
                    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
                      <tr>
                        <td width="49%"> 
                          <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                          <input type="hidden" name="_page" value="email_template_translate:view">
                          <input type="hidden" name="_page_current" value="email_template_translate:add">
                          <input type="hidden" name="do[]" value="email_template_translate:add">
                        </td>
                        <td width="51%">&nbsp;</td>
                      </tr>
                      <tr>
                        <td width="49%"> 
                          <p>Site Name<br>
                            Site E-mail<br>
                            URL<br>
                            SSL URL<br>
                            Current Date</p>
                          <p>Variable (replace &quot;variable&quot;)</p>
                          <p>Account field (replace &quot;field&quot;)</p>
                  </td>
                        <td width="51%"> 
                          <p>%site_name%<br>
                            %site_email%<br>
                            %url%<br>
                            %ssl_url%<br>
                            %date%</p>
                          <p>%var_variable%<br>
                            <br>
                            %acct_field% </p>
                  </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <p>&nbsp;</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
