{ if $list->auth_method_by_name($VAR.module,"import") } 
<form name="form1" method="post" action="" enctype="multipart/form-data">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row1">
                  <td colspan="2"><b>Step 1. Please select the file you wish to import.</b></td>
                </tr>
                <tr valign="top" class="row1"> 
                  <td width="43%">File to upload: </td>
                  <td width="57%">
                    <input type="file" name="file" >
                  </td>
                </tr> 

                <tr valign="top" class="row1">
                  <td colspan="2">&nbsp;</td>
                </tr>

                <tr valign="top" class="row1">
                  <td colspan="2"><b>Step 2. Please select the file type below.</b></td>
                </tr>                
                <tr valign="top"> 
                  <td width="43%">File Type:</td>
                  <td width="57%">
                    <select name="type" >
                      <option value="csv">CSV</option>
                      <option value="tab">TAB</option>
                    </select>				  
				  </td>
                </tr>
                <tr valign="top"> 
                  <td width="43%">&nbsp;</td>
                  <td width="57%"> 
                    <input type="submit" name="Submit" 		value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" 		value="core:import2_new"> 
                    <input type="hidden" name="module" 		value="{$VAR.module}">
                    <input type="hidden" name="filetype" 	value="upload">
					<input type="hidden" name="do[]" 		value="{$VAR.module}:import">
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
{else}
Not authorized!
{/if}
