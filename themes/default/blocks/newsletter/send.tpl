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


<form name="newsletter_send" method="post" action=""> 
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=newsletter}
                menu_send 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    send_newsletters 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->check("", "newsletter_id", "newsletter", "name", "", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    send_from_email 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "setup_email_id", "setup_email", "name", $smarty.const.DEFAULT_SETUP_EMAIL, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    send_high_priority 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="checkbox" name="newsletter_priority" value="1">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    send_test 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="checkbox" name="newsletter_test" value="1">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    send_subject 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_subject" size="60"  value="" maxlength="255">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%">&nbsp; </td>
                  <td width="65%">&nbsp; </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="65%" align="center"> <br>
                    {translate module=newsletter}
                    send_body_text 
                    {/translate}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="65%" valign="top" align="center"> 
                    <textarea name="newsletter_body_text" cols="75" rows="6" ></textarea>
                    <br>
                    <br>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="65%" align="center"><br>
                    {translate module=newsletter}
                    send_body_html 
                    {/translate}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="65%" valign="top" align="center"> 
                    <textarea name="newsletter_body_html" cols="90" rows="13" mce_editable="false"></textarea>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1" align="right"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="5" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="65%" valign="top" align="right"> 
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
  <input type="hidden" name="_page" value="newsletter:main">
  <input type="hidden" name="do[]" value="newsletter:send">
</form>
