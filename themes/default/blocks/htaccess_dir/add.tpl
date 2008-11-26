

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading"> 
            <center>
              {translate module=htaccess_dir}
              title_add 
              {/translate}
            </center>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <form id="htaccess_dir_add" name="htaccess_dir_add" method="post" action="">
              
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess_dir}
                    field_htaccess_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.id != "" }
                    { $list->menu("", "htaccess_dir_htaccess_id", "htaccess", "name", $VAR.id, "form_menu") }
                    {else}
                    { $list->menu("", "htaccess_dir_htaccess_id", "htaccess", "name", $VAR.htaccess_dir_htaccess_id, "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess_dir}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="htaccess_dir_name" value="{$VAR.htaccess_dir_name}" { if $htaccess_dir_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess_dir}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="htaccess_dir_description"  cols="55">{$VAR.htaccess_dir_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess_dir}
                    field_url 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="htaccess_dir_url" value="{$VAR.htaccess_dir_url}" {if $htaccess_dir_url == true}class="form_field_error"{/if} size="55">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess_dir}
                    field_path 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="htaccess_dir_path"  {if $htaccess_dir_path == true}class="form_field_error"{/if} cols="55" rows="2">{$VAR.htaccess_dir_path}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess_dir}
                    exclude_short 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($htaccess_dir.exclude, 'htaccess_dir_exclude', 'htaccess_exclude', 'name', '', '12', 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess_dir}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool('htaccess_dir_status', $VAR.htaccess_dir_status, 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess_dir}
                    field_recursive 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->bool('htaccess_dir_recursive', $VAR.htaccess_dir_recursive, 'form_menu') }
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%"></td>
                  <td width="65%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="htaccess_dir:view">
                    <input type="hidden" name="_page_current" value="htaccess_dir:add">
                    <input type="hidden" name="do[]" value="htaccess_dir:add">
                  </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
      </table>
      </td>
    </tr>
  </table>
  
