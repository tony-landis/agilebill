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

{ $method->exe("email_template_translate","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'email_template_translate';
    	var locations = '{/literal}{$VAR.module_id}{literal}';
    	if (locations != "")
    	{
    		refresh(0,'#'+locations)
    	}
    	// Mass update, view, and delete controller
    	function delete_record(id,ids)
    	{				
    		temp = window.confirm("{/literal}{translate}alert_delete{/translate}{literal}");
    		if(temp == false) return;
    		
    		var replace_id = id + ",";
    		ids = ids.replace(replace_id, '');		
    		if(ids == '') {
    			var url = '?_page=core:search&module=' + module + '&do[]=' + module + ':delete&delete_id=' + id + COOKIE_URL;
    			window.location = url;
    			return;
    		} else {
    			var page = 'view&id=' +ids;
    		}		
    		
    		var doit = 'delete';
    		var url = '?_page='+ module +':'+ page +'&do[]=' + module + ':' + doit + '&delete_id=' + id + COOKIE_URL;
    		window.location = url;	
    	}
    //  END -->
	</script>
{/literal}

<!-- Loop through each record -->
{foreach from=$email_template_translate item=email_template_translate} <a name="{$email_template_translate.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="email_template_translate_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=email_template_translate}
                title_view
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=email_template_translate}
                    field_email_template_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "email_template_translate_email_template_id", "email_template", "name", $email_template_translate.email_template_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=email_template_translate}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_files("", "email_template_translate_language_id", $email_template_translate.language_id, "language", "", "_core.xml", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=email_template_translate}
                    field_subject 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="email_template_translate_subject" value="{$email_template_translate.subject}"  size="48">
                  </td>
                </tr>
              </table>
              <br>
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
                <tr> 
                  <td> <b>
                    {translate module=email_template_translate}
                    field_message_text 
                    {/translate}
                    </b> </td>
                </tr>
                <tr> 
                  <td align="center"> 
                    <textarea name="email_template_translate_message_text" cols="90" rows="12">{$email_template_translate.message_text}</textarea>
                  </td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                </tr>
                <tr> 
                  <td> <b>
                    {translate module=email_template_translate}
                    field_message_html 
                    {/translate}
                    </b> </td>
                </tr>
                <tr> 
                  <td> 
                    <textarea id="email_template_translate_message_html" name="email_template_translate_message_html" mce_editable="true" cols="80" rows="17">{$email_template_translate.message_html}</textarea>
                  </td>
                </tr>
                <tr> 
                  <td> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
                      <tr> 
                        <td width="49%"> 
                          <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                        </td>
                        <td width="51%"> 
                          <div align="right">
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$email_template_translate.id}','{$VAR.id}');">
                          </div>
                        </td>
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
              <br>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="email_template_translate:view">
    <input type="hidden" name="email_template_translate_id" value="{$email_template_translate.id}">
    <input type="hidden" name="do[]" value="email_template_translate:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
