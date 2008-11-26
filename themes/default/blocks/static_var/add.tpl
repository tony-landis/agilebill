
<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="static_var_add" name="static_var_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=static_var}
                title_add
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=static_var}
                    field_name
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="static_var_name" value="{$VAR.static_var_name}" {if $static_var_name == true}class="form_field_error"{/if} size="40">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_var}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="static_var_description" cols="40" rows="2" {if $static_var_description == true}class="form_field_error"{/if}>{$VAR.static_var_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_var}
                    field_input_format 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="static_var_input_format" >
                      <option value="small_text"{if $VAR.static_var_input_format == "small_text"} selected{/if}> 
                      {translate module=static_var}
                      small_text 
                      {/translate}
                      </option>
                      <option value="medium_text"{if $VAR.static_var_input_format == "medium_text"} selected{/if}> 
                      {translate module=static_var}
                      medium_text 
                      {/translate}
                      </option>
                      <option value="large_text"{if $VAR.static_var_input_format == "large_text"} selected{/if}> 
                      {translate module=static_var}
                      large_text 
                      {/translate}
                      </option>
                      <option value="dropdown_list"{if $VAR.static_var_input_format == "dropdown_list"} selected{/if}> 
                      {translate module=static_var}
                      dropdown_list 
                      {/translate}
                      </option>
                      <option value="calendar"{if $VAR.static_var_input_format == "calendar"} selected{/if}> 
                      {translate module=static_var}
                      calendar 
                      {/translate}
                      </option>
                      <option value="file_upload"{if $VAR.static_var_input_format == "file_upload"} selected{/if}> 
                      {translate module=static_var}
                      file_upload 
                      {/translate}
                      </option>
                      <option value="status"{if $VAR.static_var_input_format == "status"} selected{/if}> 
                      {translate module=static_var}
                      status 
                      {/translate}
                      </option>
                      <option value="checkbox"{if $VAR.static_var_input_format == "checkbox"} selected{/if}> 
                      {translate module=static_var}
                      checkbox 
                      {/translate}
                      </option>
                      <option value="hidden"{if $VAR.static_var_input_format == "hidden"} selected{/if}> 
                      {translate module=static_var}
                      hidden 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_var}
                    field_validation_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="static_var_validation_type" >
                      <option value="none"{if $VAR.static_var_validation_type == "none"} selected{/if}> 
                      {translate module=static_var}
                      none 
                      {/translate}
                      </option>
                      <option value="any"{if $VAR.static_var_validation_type == "any"} selected{/if}> 
                      {translate module=static_var}
                      any 
                      {/translate}
                      </option>
                      <option value="email"{if $VAR.static_var_validation_type == "email"} selected{/if}> 
                      {translate module=static_var}
                      email 
                      {/translate}
                      </option>
                      <option value="date"{if $VAR.static_var_validation_type == "date"} selected{/if}> 
                      {translate module=static_var}
                      date 
                      {/translate}
                      </option>
                      <option value="address"{if $VAR.static_var_validation_type == "address"} selected{/if}> 
                      {translate module=static_var}
                      address 
                      {/translate}
                      </option>
                      <option value="zip"{if $VAR.static_var_validation_type == "zip"} selected{/if}> 
                      {translate module=static_var}
                      zip 
                      {/translate}
                      </option>
                      <option value="phone"{if $VAR.static_var_validation_type == "phone"} selected{/if}> 
                      {translate module=static_var}
                      phone 
                      {/translate}
                      </option>
                      <option value="credit_card"{if $VAR.static_var_validation_type == "credit_card"} selected{/if}> 
                      {translate module=static_var}
                      credit_card 
                      {/translate}
                      </option>
                      <option value="numeric"{if $VAR.static_var_validation_type == "numeric"} selected{/if}> 
                      {translate module=static_var}
                      numeric 
                      {/translate}
                      </option>
                      <option value="alphanumeric"{if $VAR.static_var_validation_type == "alphanumeric"} selected{/if}> 
                      {translate module=static_var}
                      alphanumeric 
                      {/translate}
                      </option>
                      <option value="non_numeric"{if $VAR.static_var_validation_type == "non_numeric"} selected{/if}> 
                      {translate module=static_var}
                      non_numeric 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_var}
                    field_convert_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="static_var_convert_type" >
                      <option value="none"{if $VAR.static_var_convert_type == "none"} selected{/if}> 
                      {translate module=static_var}
                      none 
                      {/translate}
                      </option>
                      <option value="date"{if $VAR.static_var_convert_type == "date"} selected{/if}> 
                      {translate module=static_var}
                      date 
                      {/translate}
                      </option>
                      <option value="md5"{if $VAR.static_var_convert_type == "md5"} selected{/if}> 
                      {translate module=static_var}
                      md5 
                      {/translate}
                      </option>
                      <option value="rc5"{if $VAR.static_var_convert_type == "rc5"} selected{/if}> 
                      {translate module=static_var}
                      rc5 
                      {/translate}
                      </option>
                      <option value="crypt"{if $VAR.static_var_convert_type == "crypt"} selected{/if}> 
                      {translate module=static_var}
                      crypt 
                      {/translate}
                      </option>
                      <option value="gpg"{if $VAR.static_var_convert_type == "gpg"} selected{/if}> 
                      {translate module=static_var}
                      gpg 
                      {/translate}
                      </option>
                      <option value="pgp"{if $VAR.static_var_convert_type == "pgp"} selected{/if}> 
                      {translate module=static_var}
                      pgp 
                      {/translate}
                      </option>
                      <option value="array"{if $VAR.static_var_convert_type == "array"} selected{/if}> 
                      {translate module=static_var}
                      array 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="static_var:view">
                    <input type="hidden" name="_page_current" value="static_var:add">
                    <input type="hidden" name="do[]" value="static_var:add">
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
