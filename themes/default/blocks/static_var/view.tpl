{ $method->exe("static_var","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else} 

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'static_var';
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
{foreach from=$static_var item=static_var} <a name="{$static_var.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="update_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=static_var}
                title_view
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=static_var}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="static_var_name"  cols="50" rows="4">{$static_var.name|escape:"htmlall"}</textarea>
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="50%"> 
                    {translate module=static_var}
                    field_description 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="static_var_description" cols="50" rows="2" >{$static_var.description}</textarea>
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="50%"> 
                    {translate module=static_var}
                    field_input_format 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <select name="static_var_input_format" >
                      <option value="small_text"{if $static_var.input_format == "small_text"} selected{/if}> 
                      {translate module=static_var}
                      small_text 
                      {/translate}
                      </option>
                      <option value="medium_text"{if $static_var.input_format == "medium_text"} selected{/if}> 
                      {translate module=static_var}
                      medium_text 
                      {/translate}
                      </option>
                      <option value="large_text"{if $static_var.input_format == "large_text"} selected{/if}> 
                      {translate module=static_var}
                      large_text 
                      {/translate}
                      </option>
                      <option value="dropdown_list"{if $static_var.input_format == "dropdown_list"} selected{/if}> 
                      {translate module=static_var}
                      dropdown_list 
                      {/translate}
                      </option>
                      <option value="calendar"{if $static_var.input_format == "calendar"} selected{/if}> 
                      {translate module=static_var}
                      calendar 
                      {/translate}
                      </option>
                      <option value="file_upload"{if $static_var.input_format == "file_upload"} selected{/if}> 
                      {translate module=static_var}
                      file_upload 
                      {/translate}
                      </option>
                      <option value="status"{if $static_var.input_format == "status"} selected{/if}> 
                      {translate module=static_var}
                      status 
                      {/translate}
                      </option>
                      <option value="checkbox"{if $static_var.input_format == "checkbox"} selected{/if}> 
                      {translate module=static_var}
                      checkbox 
                      {/translate}
                      </option>
                      <option value="hidden"{if $static_var.input_format == "hidden"} selected{/if}> 
                      {translate module=static_var}
                      hidden 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr class="row3" valign="top"> 
                  <td width="50%"> 
                    {translate module=static_var}
                    field_validation_type 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <select name="static_var_validation_type" >
                      <option value="none"{if $static_var.validation_type == "none"} selected{/if}> 
                      {translate module=static_var}
                      none 
                      {/translate}
                      </option>
                      <option value="any"{if $static_var.validation_type == "any"} selected{/if}> 
                      {translate module=static_var}
                      any 
                      {/translate}
                      </option>
                      <option value="email"{if $static_var.validation_type == "email"} selected{/if}> 
                      {translate module=static_var}
                      email 
                      {/translate}
                      </option>
                      <option value="date"{if $static_var.validation_type == "date"} selected{/if}> 
                      {translate module=static_var}
                      date 
                      {/translate}
                      </option>
                      <option value="address"{if $static_var.validation_type == "address"} selected{/if}> 
                      {translate module=static_var}
                      address 
                      {/translate}
                      </option>
                      <option value="zip"{if $static_var.validation_type == "zip"} selected{/if}> 
                      {translate module=static_var}
                      zip 
                      {/translate}
                      </option>
                      <option value="phone"{if $static_var.validation_type == "phone"} selected{/if}> 
                      {translate module=static_var}
                      phone 
                      {/translate}
                      </option>
                      <option value="credit_card"{if $static_var.validation_type == "credit_card"} selected{/if}> 
                      {translate module=static_var}
                      credit_card 
                      {/translate}
                      </option>
                      <option value="numeric"{if $static_var.validation_type == "numeric"} selected{/if}> 
                      {translate module=static_var}
                      numeric 
                      {/translate}
                      </option>
                      <option value="alphanumeric"{if $static_var.validation_type == "alphanumeric"} selected{/if}> 
                      {translate module=static_var}
                      alphanumeric 
                      {/translate}
                      </option>
                      <option value="non_numeric"{if $static_var.validation_type == "non_numeric"} selected{/if}> 
                      {translate module=static_var}
                      non_numeric 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr class="row4" valign="top"> 
                  <td width="50%"> 
                    {translate module=static_var}
                    field_convert_type 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <select name="static_var_convert_type" >
                      <option value="none"{if $static_var.convert_type == "none"} selected{/if}> 
                      {translate module=static_var}
                      none 
                      {/translate}
                      </option>
                      <option value="date"{if $static_var.convert_type == "date"} selected{/if}> 
                      {translate module=static_var}
                      date 
                      {/translate}
                      </option>
                      <option value="md5"{if $static_var.convert_type == "md5"} selected{/if}> 
                      {translate module=static_var}
                      md5 
                      {/translate}
                      </option>
                      <option value="rc5"{if $static_var.convert_type == "rc5"} selected{/if}> 
                      {translate module=static_var}
                      rc5 
                      {/translate}
                      </option>
                      <option value="crypt"{if $static_var.convert_type == "crypt"} selected{/if}> 
                      {translate module=static_var}
                      crypt 
                      {/translate}
                      </option>
                      <option value="gpg"{if $static_var.convert_type == "gpg"} selected{/if}> 
                      {translate module=static_var}
                      gpg 
                      {/translate}
                      </option>
                      <option value="pgp"{if $static_var.convert_type == "pgp"} selected{/if}> 
                      {translate module=static_var}
                      pgp 
                      {/translate}
                      </option>
                      <option value="array"{if $static_var.convert_type == "array"} selected{/if}> 
                      {translate module=static_var}
                      array 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="static_var:view">
                    <input type="hidden" name="static_var_id" value="{$static_var.id}">
                    <input type="hidden" name="do[]" value="static_var:update">
                    <input type="hidden" name="id" value="{$VAR.id}">
                    <a href="javascript:showVars();"> </a><a href="javascript:addVars();"></a> 
                  </td>
                  <td width="50%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>&nbsp; </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$static_var.id}','{$VAR.id}');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td width="50%"><a href="javascript:showVars();"> 
                    {translate module=static_var}
                    title_view 
                    {/translate}
                    </a></td>
                  <td width="50%">
                    <div align="right"><a href="javascript:addVars();"> 
                      {translate module=static_var}
                      title_add 
                      {/translate}
                      </a></div>
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
  
{/foreach}
{/if}
<center>
	<iframe name="iframeStatic" id="iframeStatic" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
</center>
 
{literal}
<script language="JavaScript">
<!-- START
 
var static_var_id 	= {/literal}{$static_var.id}{literal};   
 
 
function showVars() {
	showIFrame('iframeStatic',getPageWidth(600),300,'?_page=core:search_iframe&module=static_relation&_escape=1&static_relation_static_var_id='+static_var_id+
			   '&_escape_next=1&_next_page_one=view&_next_page_none=add&name_id1=static_relation_static_var_id&val_id1='+static_var_id);
}

function addVars() {
	showIFrame('iframeStatic',getPageWidth(600),300,'?_page=static_relation:add&_escape=1&static_relation_static_var_id='+static_var_id);
}
 
//  END -->
</script>
{/literal}
