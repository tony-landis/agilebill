
<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="file_add" name="file_add" method="post" action="" enctype="multipart/form-data">
  {$COOKIE_FORM}
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=file}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_file_category_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "file_file_category_id", "file_category", "name", $VAR.file_file_category_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.file_status != ""}
                    { $list->bool("file_status", $VAR.file_status, "form_menu") }
                    {else}
                    { $list->bool("file_status", "1", "form_menu") }
                    {/if}
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_location_type
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="radio" id="loc0" name="file_location_type" value="0" onClick="document.getElementById('file0').style.display = 'block'; document.getElementById('file1').style.display = 'none'; document.getElementById('file2').style.display = 'none';">
                    Upload from local computer<br>
                    <input type="radio" id="loc1" name="file_location_type" value="1" onClick="document.getElementById('file0').style.display = 'none'; document.getElementById('file1').style.display = 'block'; document.getElementById('file2').style.display = 'none';">
                    Enter URL to file located on the Internet<br>
                    <input type="radio" id="loc2" name="file_location_type" value="2" onClick="document.getElementById('file0').style.display = 'none'; document.getElementById('file1').style.display = 'none'; document.getElementById('file2').style.display = 'block';">
                    Select file already on the webserver</td>
                </tr> 		
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%">  
					<div id="file0" style="display:none"> 
                      {translate module=file}
                      upload_file 
                      {/translate}
                      <input type="file" name="upload_file" class="form_field" size="40" value="{$upload_file}">
					</div> 
					<div id="file1" style="display:none"> 
                      {translate module=file}
                      file_url
                      {/translate}
                      <input type="text" name="url_file" size="50" value="{$VAR.url_file}">
                    </div> 
					<div id="file2" style="display:none"> 
                      {translate module=file}
                      file_path
                      {/translate}
                      <input type="text" name="local_file" size="50" value="{$VAR.local_file}">
                    </div> 
                  </td>
                </tr>
				
				<script language=javascript>
				if(document.getElementById('loc0').checked  )
					document.getElementById('file0').style.display = 'block';
				else if(document.getElementById('loc1').checked )
					document.getElementById('file1').style.display = 'block'; 
				else if(document.getElementById('loc2').checked )
					document.getElementById('file2').style.display = 'block';
				</script>
								
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="file_description" cols="50" rows="4" {if $file_description == true}class="form_field_error"{/if}>{$VAR.file_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%" height="31"> 
                    {translate module=file}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="65%" height="31"> 
                    <input type="text" name="file_sort_order" value="{$VAR.file_sort_order}" {if $file_sort_order == true}class="form_field_error"{/if} size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->select_groups($VAR.file_group_avail,"file_group_avail","form_field","10","") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("file_date_start", $VAR.file_date_start, "form_field") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("file_date_expire", $VAR.file_date_expire, "form_field") }
                  </td>
                </tr>				
                <tr valign="top">
                  <td width="35%"></td>
                  <td width="65%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="file:view">
                    <input type="hidden" name="_page_current" value="file:add">
                    <input type="hidden" name="do[]" value="file:add">
                    <input type="hidden" name="file_date_last" value="{$smarty.now}">
                    <input type="hidden" name="file_date_orig" value="{$smarty.now}">
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
