

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="file_category_add" name="file_category_add" method="post" action="">
{$COOKIE_FORM}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=file_category}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=file_category}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time("")}  <input type="hidden" name="file_category_date_orig" value="{$smarty.now}">
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=file_category}
                            field_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="file_category_name" value="{$VAR.file_category_name}" {if $file_category_name == true}class="form_field_error"{else}class="form_field"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=file_category}
                            field_description
                        {/translate}</td>
                    <td width="65%">
                        <textarea name="file_category_description" cols="40" rows="5" {if $file_category_description == true}class="form_field_error"{else}class="form_field"{/if}>{$VAR.file_category_description}</textarea>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=file_category}
                            field_status
                        {/translate}</td>
                    
                  <td width="65%"> 
				  {if $VAR.file_category_status == ""}
                    { $list->bool("file_category_status", "1", "form_menu") }
				  {else}
                    { $list->bool("file_category_status", $VAR.file_category_status, "form_menu") }
				  {/if}
                  </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=file_category}
                            field_group_avail
                        {/translate}</td>
                    
                  <td width="65%"> 
                    { $list->select_groups($VAR.file_category_group_avail,"file_category_group_avail","form_field","10","") }
                  </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=file_category}
                            field_sort_order
                        {/translate}</td>
                    <td width="65%">
                        
                    <input type="text" name="file_category_sort_order" value="{$VAR.file_category_sort_order}" {if $file_category_sort_order == true}class="form_field_error"{else}class="form_field"{/if} size="3">
                    </td>
                  </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="file_category:view">
                      <input type="hidden" name="_page_current" value="file_category:add">
                      <input type="hidden" name="do[]" value="file_category:add">
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
