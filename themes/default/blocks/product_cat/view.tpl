<!-- Image Preview -->{literal}
<script language="JavaScript" type="text/JavaScript">
function previewImage(fileInfo) {
var filename = "";
//create the path to your local file
if (fileInfo == null) {
if (document.form1.file != "") {
filename = "file:///" + document.form1.file.value;
}
} else {
filename = fileInfo;
}
//check if there is a value
if (filename == "") {
alert ("Please select a image.");
document.form1.file.focus();
} else {
//create the popup 
popup = window.open('', 'imagePreview', 'width=640,height=100,left=100,top=75, screenX=100,screenY=75,scrollbars,location,menubar,status=0,toolbar=0,resizable=1');
//start writing in the html code
popup.document.writeln("<html><body bgcolor='#FFFFFF'>");
//get the extension of the file to see if it has one of the image extensions
var fileExtension = filename.substring(filename.lastIndexOf(".")+1);
if (fileExtension == "jpg" || fileExtension == "jpeg" || fileExtension == "gif" 
|| fileExtension == "png")
popup.document.writeln("<img src='" + filename + "'>");
else
//if not extension fron list above write URL to file 
popup.document.writeln("<a href='" + filename + "'>" + filename + "</a>");
popup.document.writeln("</body></html>");
popup.document.close();
popup.focus();
}
}
 

</script>{/literal}


{ $method->exe("product_cat","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'product_cat';
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
{foreach from=$product_cat item=product_cat} <a name="{$product_cat.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form id="form10" name="product_cat_view" method="post" action="" enctype="multipart/form-data">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=product_cat}
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
                    {translate module=product_cat}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input id="name1" type="text" name="product_cat_name" value="{$product_cat.name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="product_cat_notes"  cols="40" rows="2">{$product_cat.notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=product_cat}
                    field_parent_id 
                    {/translate}
                  </td>
                  <td width="65%">{$method->exe_noauth("product_cat", "admin_menu_parent")} </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($product_cat.group_avail, 'product_cat_group_avail', 'group', 'name', '', '10', 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("product_cat_status", $product_cat.status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_template 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_files("", "product_cat_template", $product_cat.template, "product_cat", "t_", ".tpl", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_position 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="product_cat_position" value="{$product_cat.position}"  size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_max 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="product_cat_max" value="{$product_cat.max}"  size="5">
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_thumbnail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input id="thumbnail1" type="file" name="upload_file1"  size="38" {if $campaign_file1 == true}class="form_field_error"{/if}>
                    <img src="themes/{$THEME_NAME}/images/icons/picts_16.gif" onClick="previewImage(document.product_cat_view.upload_file1.value);"> 
                    {if $product_cat.thumbnail != ""}
                    <img title=Delete src="themes/{$THEME_NAME}/images/icons/del_16.gif" onClick="document.getElementById('delthumb').value = '1'; document.getElementById('form10').submit();"><br>
                     <br>
                    <img src="{$URL}{$smarty.const.URL_IMAGES}{$product_cat.thumbnail}"> 
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_image 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input id="image" type="file" name="upload_file2"  size="38" {if $campaign_file2 == true}class="form_field_error"{/if}>
                    <img src="themes/{$THEME_NAME}/images/icons/picts_16.gif" onClick="previewImage(document.product_cat_view.upload_file2.value);"> 
                    {if $product_cat.image != ""}
                    <img title=Delete src="themes/{$THEME_NAME}/images/icons/del_16.gif" onClick="document.getElementById('delimg').value = '1'; document.getElementById('form10').submit();"><br>
                     <br>
                    <img src="{$URL}{$smarty.const.URL_IMAGES}{$product_cat.image}"> 
					{/if}
					</td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>&nbsp; </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$product_cat.id}','{$VAR.id}');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top">
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="53%"> <a href="javascript:showCatTranslations({$product_cat.id})"> 
                    {translate module=product_cat}
                    title_view_translations 
                    {/translate}
                    </a> </td>
                  <td width="47%" valign="middle" align="right"> <a href="javascript:addCatTranslations({$product_cat.id})"> 
                    {translate module=product_cat}
                    title_add_translation 
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
    <input type="hidden" name="_page" value="product_cat:view">
    <input type="hidden" name="product_cat_id" value="{$product_cat.id}">
    <input type="hidden" name="do[]" value="product_cat:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  	<input type="hidden" id="delthumb" name="delthumb" value="0">
  	<input type="hidden" id="delimg" name="delimg" value="0">
</form>
  <center>  
	<iframe name="iframeCat" id="iframeCat" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
  </center>
  <script language=javascript>
  var CatId = '{$product_cat.id}';
  </script>
{/foreach}
  
{literal}
<script language="JavaScript">
<!-- START  
function showCatTranslations(id) { 
	showIFrame('iframeCat',getPageWidth(650),350,'?_page=core:search&_next_page_one=view&module=product_cat_translate&_escape=1&product_cat_translate_product_cat_id='+id);
} 
function addCatTranslations(id) { 
	showIFrame('iframeCat',getPageWidth(650),350,'?_page=product_cat_translate:add&product_cat_translate_product_cat_id='+id);
} 
showCatTranslations(CatId);
//  END -->
</script>
{/literal}

  
{/if}
