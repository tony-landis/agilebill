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


<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="product_cat_add" name="product_cat_add" method="post" action="" enctype="multipart/form-data">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=product_cat}title_add{/translate}
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
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="product_cat_name" value="{$VAR.product_cat_name}" {if $product_cat_name == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=product_cat}
                            field_notes
                        {/translate}</td>
                    <td width="65%">
                        
                    <textarea name="product_cat_notes" class="{if $product_cat_notes == true}form_field_error{else}form_field{/if}" cols="40" rows="2">{$VAR.product_cat_notes}</textarea>
                    </td>
                  </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_parent_id 
                    {/translate}
                  </td>
                  <td width="65%">&nbsp; </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($VAR.product_cat_group_avail, 'product_cat_group_avail', 'group', 'name', '', '10', 'form_menu') }
                  </td>
                </tr>				  
                <tr valign="top">
                    <td width="35%">
                        {translate module=product_cat}
                            field_status
                        {/translate}</td>
                    <td width="65%">
					{ if $VAR.product_cat_status != ""}
                        { $list->bool("product_cat_status", $VAR.product_cat_status, "form_menu") }
					{else}
						{ $list->bool("product_cat_status", "1", "form_menu") }
					{/if}
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=product_cat}
                            field_template
                        {/translate}</td>
                    
                  <td width="65%"> 
                    { $list->menu_files("", "product_cat_template", $VAR.product_cat_template, "product_cat", "t_", ".tpl", "form_menu") }
                  </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=product_cat}
                            field_thumbnail
                        {/translate}</td>
                    
                  <td width="65%">
                    <input type="file" name="upload_file1"  size="38" {if $campaign_file1 == true}class="form_field_error"{/if}>
                    <img src="themes/{$THEME_NAME}/images/icons/picts_16.gif" onClick="previewImage(document.product_cat_add.upload_file1.value);"> 
                  </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=product_cat}
                            field_image
                        {/translate}</td>
                    
                  <td width="65%">
                    <input type="file" name="upload_file2"  size="38" {if $campaign_file2 == true}class="form_field_error"{/if}>
                    <img src="themes/{$THEME_NAME}/images/icons/picts_16.gif" onClick="previewImage(document.product_cat_add.upload_file2.value);"> 
                  </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=product_cat}
                            field_position
                        {/translate}</td>
                    <td width="65%">
                        
                    <input type="text" name="product_cat_position" value="{$VAR.product_cat_position}" {if $product_cat_position == true}class="form_field_error"{/if} size="3">
                    </td>
                  </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=product_cat}
                    field_max 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="product_cat_max" value="{$VAR.product_cat_max}" {if $product_cat_max == true}class="form_field_error"{/if} size="3">
                  </td>
                </tr>				  
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="product_cat_translate:add">
                      <input type="hidden" name="_page_current" value="product_cat:add">
                      <input type="hidden" name="do[]" value="product_cat:add">
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
