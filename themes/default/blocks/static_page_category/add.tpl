

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="static_page_category_add" name="static_page_category_add" method="post" action="">
{$COOKIE_FORM}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=static_page_category}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page_category}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($VAR.static_page_category_group_avail, 'static_page_category_group_avail', 'group', 'name', '', '10', 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page_category}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="static_page_category_name" value="{$VAR.static_page_category_name}" {if $static_page_category_name == true}class="form_field_error"{/if} size="35">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page_category}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="static_page_category_description" cols="40" rows="5" {if $static_page_category_description == true}class="form_field_error"{/if}>{$VAR.static_page_category_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page_category}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("static_page_category_status", $VAR.static_page_category_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page_category}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="static_page_category_sort_order" value="{$VAR.static_page_category_sort_order}" {if $static_page_category_sort_order == true}class="form_field_error"{/if} size="5">
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%"></td>
                  <td width="65%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="static_page_category:view">
                    <input type="hidden" name="_page_current" value="static_page_category:add">
                    <input type="hidden" name="do[]" value="static_page_category:add">
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
