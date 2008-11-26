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
                  <td colspan="3"><b>Step 3. Please define the field mappings for the import.</b><br><br>
                  For each field below, either enter a value to import always in to the field in the Constant Value Mapping, or
                  select one of the fields to map from the Field Import Mapping.<br><br>
                  <i>There are {$rows} records in the file.</i>
                  </td>
                </tr>
                <tr>
                  <td colspan="3">&nbsp;</td>
                </tr>
                <tr valign="top" class="table_heading"> 
                  <td width="33%">Field Name</td>
                  <td width="33%">
                    Field Import Mapping
				  </td>                  
                  <td width="33%">
                    Constant Value Mapping
                  </td>
                </tr>                 
                {section name=cur loop=$fields_array}
                <tr valign="top" class="row1"> 
                  <td width="33%">{$fields_array[cur].field}: </td>
                  <td width="33%">
                    <select name="import_select[{$fields_array[cur].field}]">
                      {html_options options=$fields_array[cur].options selected=$fields_array[cur].selected}
                    </select>
				  </td>                  
                  <td width="33%">
                  {if $fields_array[cur].type ne "select"}
                    <input type="text" name="import_constant[{$fields_array[cur].field}]" value="" >
                  {else}---&nbsp;{/if}
                  </td>
                </tr> 
                {/section}
       

                <tr valign="top"> 
                  <td width="33%">&nbsp;</td>
                  <td width="33%"> 
                    <input type="submit" name="Submit" 		value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" 		value="core:import2_new"> 
                    <input type="hidden" name="module" 		value="{$VAR.module}">
                    <input type="hidden" name="filetype" 	value="upload">
                    <input type="hidden" name="confirm"		value="yes">
                    <input type="hidden" name="file" 		value="{$file}">
                    <input type="hidden" name="type" 		value="{$type}">
                    <input type="hidden" name="import_type" value="db">
					<input type="hidden" name="do[]" 		value="{$VAR.module}:import">
                  </td>
                  <td>&nbsp;</td>
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
