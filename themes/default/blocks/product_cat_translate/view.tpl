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

{ $method->exe("product_cat_translate","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'product_cat_translate';
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
{foreach from=$product_cat_translate item=product_cat_translate} <a name="{$product_cat_translate.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="product_cat_translate_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=product_cat_translate}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=product_cat_translate}
                    field_product_cat_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu("", "product_cat_translate_product_cat_id", "product_cat", "name", $product_cat_translate.product_cat_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=product_cat_translate}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_files("", "product_cat_translate_language_id", $product_cat_translate.language_id, "language", "", "_core.xml", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=product_cat_translate}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="product_cat_translate_name" value="{$product_cat_translate.name}"  size="32">
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="65%"> 
                    {translate module=product_cat_translate}
                    field_description 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="65%" align="center"> 
                    <textarea name="product_cat_translate_description" cols="90" rows="13" mce_editable="true">{$product_cat_translate.description}</textarea>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="8">
                <tr> 
                  <td> 
                    <input type="submit" name="Submit3" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td align="right"> 
                    <input type="button" name="delete3" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$product_cat_translate.id}','{$VAR.id}');">
                  </td>
                </tr>
              </table>
              
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="product_cat_translate:view">
    <input type="hidden" name="product_cat_translate_id" value="{$product_cat_translate.id}">
    <input type="hidden" name="do[]" value="product_cat_translate:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
