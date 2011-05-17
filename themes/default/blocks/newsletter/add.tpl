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
<form id="newsletter_add" name="newsletter_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=newsletter}
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
                    {translate module=newsletter}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_name" value="{$VAR.newsletter_name}" {if $newsletter_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($VAR.newsletter_group_avail, 'newsletter_group_avail', 'group', 'name', '', '10', 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    field_active 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("newsletter_active", $VAR.newsletter_active, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    field_display_signup 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("newsletter_display_signup", $VAR.newsletter_display_signup, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="newsletter_notes" cols="40" rows="5" {if $newsletter_notes == true}class="form_field_error"{/if}>{$VAR.newsletter_notes}</textarea>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    field_description 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    <textarea name="newsletter_description" cols="80" rows="13" {if $newsletter_description == true}class="form_field_error"{/if} mce_editable="true">{$VAR.newsletter_description}</textarea>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="3" cellpadding="5" class="row1">
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%" align="right"> 
                    <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="newsletter:view">
                    <input type="hidden" name="_page_current" value="newsletter:add">
                    <input type="hidden" name="do[]" value="newsletter:add">
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
