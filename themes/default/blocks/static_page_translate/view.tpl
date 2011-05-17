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

{ $method->exe("static_page_translate","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'static_page_translate';
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
    </script>{/literal}

<!-- Loop through each record -->
{foreach from=$static_page_translate item=static_page_translate} <a name="{$static_page_translate.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="static_page_translate_view" method="post" action="">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=static_page_translate}
                title_view
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page_translate}
                    field_static_page_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "static_page_translate_static_page_id", "static_page", "name", $static_page_translate.static_page_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page_translate}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_files("", "static_page_translate_language_id", $static_page_translate.language_id, "language", "", "_core.xml", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page_translate}
                    field_title 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="static_page_translate_title" value="{$static_page_translate.title}"  size="45">
                  </td>
                </tr>
              </table>
              <br>
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class=body>
                <tr> 
                  <td> <b>
                    {translate module=static_page_translate}
                    field_body_intro 
                    {/translate}
                    </b> </td>
                </tr>
                <tr> 
                  <td> 
                    <textarea id="static_page_translate_body_intro" name="static_page_translate_body_intro" mce_editable="true" cols="80" rows="12">{$static_page_translate.body_intro}</textarea>
                  </td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
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
                    <textarea id="static_page_translate_body_full" name="static_page_translate_body_full" mce_editable="true" cols="80" rows="18">{$static_page_translate.body_full}</textarea>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top">
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="2" cellpadding="5" class="row2">
                <tr> 
                  <td><a href="{$URL}?_page=core:search&module=static_page_translate&static_page_translate_static_page_id={$static_page_translate.static_page_id}"> 
                    </a>
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td align="right"><a href="{$URL}?_page=static_page:view&id={$static_page_translate.static_page_id}"> 
                    </a>
                    <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$static_page_translate.id}','{$VAR.id}');">
                  </td>
                </tr>
                <tr>
                  <td><a href="{$URL}?_page=core:search&module=static_page_translate&static_page_translate_static_page_id={$static_page_translate.static_page_id}">
                    {translate module=static_page_translate}
                    view_all 
                    {/translate}
                    </a></td>
                  <td align="right"><a href="{$URL}?_page=static_page:view&id={$static_page_translate.static_page_id}">
                    {translate module=static_page_translate}
                    return 
                    {/translate}
                    </a></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="static_page_translate:view">
    <input type="hidden" name="static_page_translate_id" value="{$static_page_translate.id}">
    <input type="hidden" name="do[]" value="static_page_translate:update">
    <input type="hidden" name="id" value="{$VAR.id}">
    <input type="hidden" name="static_page_translate_date_last" value="{$smarty.now}">
</form>
  {/foreach}
{/if}
