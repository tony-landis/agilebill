

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="htaccess_add" name="htaccess_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=htaccess}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="htaccess_name" value="{$VAR.htaccess_name}" {if $htaccess_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="htaccess_description" cols="40" rows="5" {if $htaccess_description == true}class="form_field_error"{/if}>{$VAR.htaccess_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("htaccess_status", $VAR.htaccess_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->select_groups($VAR.htaccess_group_avail,"htaccess_group_avail","form_field","10","") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="htaccess_dir:add">
                    <input type="hidden" name="_page_current" value="htaccess:add">
                    <input type="hidden" name="do[]" value="htaccess:add">
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
