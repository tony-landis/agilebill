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
<form id="campaign_add" name="campaign_add" method="post" action="" enctype="multipart/form-data">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=campaign}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time("")}
                    <input type="hidden" name="campaign_date_orig" value="{$smarty.now}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time("")}
                    <input type="hidden" name="campaign_date_last" value="{$smarty.now}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("campaign_date_start", $VAR.campaign_date_start, "form_field") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("campaign_date_expire", $VAR.campaign_date_expire, "form_field") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.campaign_status != ""}
                    { $list->bool("campaign_status", $VAR.campaign_status, "form_menu") }
                    {else}
                    { $list->bool("campaign_status", "1", "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="campaign_name" value="{$VAR.campaign_name}" {if $campaign_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="campaign_description" cols="40" rows="5" {if $campaign_description == true}class="form_field_error"{/if}>{$VAR.campaign_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_budget 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="campaign_budget" value="{$VAR.campaign_budget}" {if $campaign_budget == true}class="form_field_error"{/if} size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_url 
                    {/translate}
                    <br>
                  </td>
                  <td width="65%"> 
                    <input type="text" name="campaign_url" value="{$VAR.campaign_url}" {if $campaign_url == true}class="form_field_error"{/if}>
                    <br>
                    <br>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp; </td>
                  <td width="65%"> 
                    {translate module=campaign}
                    files 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_one 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file1"  size="38" {if $campaign_file1 == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_two 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file2"  size="38">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_three 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file3"  size="38">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_four 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file4"  size="38">
                  </td>
                </tr>
				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_five 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file5"  size="38">
                  </td>
                </tr>
				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_six 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file6"  size="38">
                  </td>
                </tr>
				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_seven 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file7"  size="38">
                  </td>
                </tr>
				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_eight 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file8"  size="38">
                  </td>
                </tr>
				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_nine 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file9"  size="38">
                  </td>
                </tr>
				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_ten 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file10"  size="38">
                  </td>
                </tr>
				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_eleven 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file11"  size="38">
                  </td>
                </tr>
				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    add_twelve 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="file" name="upload_file12"  size="38">
                  </td>
                </tr>
																																				
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="campaign:view">
                    <input type="hidden" name="_page_current" value="campaign:add">
                    <input type="hidden" name="do[]" value="campaign:add">
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
