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

{ $method->exe("newsletter","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'newsletter';
    	var locations 	= '{/literal}{$VAR.module_id}{literal}';		
		var id 			= '{/literal}{$VAR.id}{literal}';
		var ids 		= '{/literal}{$VAR.ids}{literal}';    	 
		var array_id    = id.split(",");
		var array_ids   = ids.split(",");		
		var num=0;
		if(array_id.length > 2) {				 
			document.location = '?_page='+module+':view&id='+array_id[0]+'&ids='+id;				 		
		}else if (array_ids.length > 2) {
			document.write(view_nav_top(array_ids,id,ids));
		}
		
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
{foreach from=$newsletter item=newsletter} <a name="{$newsletter.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="newsletter_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=newsletter}
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
                    {translate module=newsletter}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_name" value="{$newsletter.name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($newsletter.group_avail, 'newsletter_group_avail', 'group', 'name', '', '10', 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    field_active 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("newsletter_active", $newsletter.active, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter}
                    field_display_signup 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("newsletter_display_signup", $newsletter.display_signup, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%" valign="top"> 
                    {translate module=newsletter}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="newsletter_notes" cols="40" rows="5" >{$newsletter.notes}</textarea>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="3"  class="row1">
                <tr> 
                  <td> 
                    {translate module=newsletter}
                    field_description 
                    {/translate}
                  </td>
                </tr>
                <tr> 
                  <td align="center"> 
                    <textarea name="newsletter_description" cols="80" rows="13" mce_editable="true">{$newsletter.description}</textarea>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="3" cellpadding="5" class="row1">
                <tr valign="top"> 
                  <td width="35%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="65%" align="right"> 
                    <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$newsletter.id}','{$VAR.id}');">
                  </td>
                </tr>
              </table>
              
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="newsletter:view">
    <input type="hidden" name="newsletter_id" value="{$newsletter.id}">
    <input type="hidden" name="do[]" value="newsletter:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  
<form name="newsletter_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="1" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
          <tr class="row1" valign="middle" align="left"> 
            <td width="65%"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td> 
                    <div align="center"> 
                      <input type="submit" name="Submit2" value="{translate module=newsletter}view_subscribers{/translate}" class="form_button">
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="core:search">
  <input type="hidden" name="module" value="newsletter_subscriber">
  <input type="hidden" name="_escape" value="1">
  <input type="hidden" name="newsletter_subscriber_newsletter_id" value="{$newsletter.id}">
 
</form>
{/foreach}
{/if}
