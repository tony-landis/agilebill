

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="db_mapping_add" name="db_mapping_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=db_mapping}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("db_mapping_status", $VAR.db_mapping_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_db_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="db_mapping_db_name" value="{$VAR.db_mapping_db_name}" {if $db_mapping_db_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_db_host 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="db_mapping_db_host" value="{$VAR.db_mapping_db_host}" {if $db_mapping_db_host == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_db_user 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="db_mapping_db_user" value="{$VAR.db_mapping_db_user}" {if $db_mapping_db_user == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_db_pass 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="db_mapping_db_pass" value="{$VAR.db_mapping_db_pass}" {if $db_mapping_db_pass == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_db_prefix 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="db_mapping_db_prefix" value="{$VAR.db_mapping_db_prefix}" {if $db_mapping_db_prefix == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_cookie_name
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="db_mapping_cookie_name" value="{$VAR.db_mapping_cookie_name}" {if $db_mapping_cookie_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp; </td>
                  <td width="65%">&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="db_mapping:view">
                    <input type="hidden" name="_page_current" value="db_mapping:add">
                    <input type="hidden" name="do[]" value="db_mapping:add">
                    <input type="hidden" name="db_mapping_map_file" value="{$VAR.db_mapping_map_file}">
                    <input type="hidden" name="db_mapping_name" value="{$VAR.db_mapping_map_file}">
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
