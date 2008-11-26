

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="static_page_add" name="static_page_add" method="post" action="">
{$COOKIE_FORM}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=static_page}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="static_page_sort_order" value="{$VAR.static_page_sort_order}" {if $static_page_sort_order == true}class="form_field_error"{/if} size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_date_start
                    {/translate}
                  </td>
                  <td width="65%"> 
				  {if $VAR.static_page_date_start != "" }
				  	{ $list->calender_add("static_page_date_start", $VAR.static_page_date_start, "form_field") }
				  {else}
                    { $list->calender_add("static_page_date_start", "now", "form_field") }
				  {/if}
                  </td>
                </tr>							  
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("static_page_date_expire", $VAR.static_page_date_expire, "form_field") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_static_page_category_id 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->menu("", "static_page_static_page_category_id", "static_page_category", "name", $VAR.static_page_static_page_category_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
				  {if $VAR.static_page_status != ""}
                    { $list->bool("static_page_status", $VAR.static_page_status, "form_menu") }
				{else}
                    { $list->bool("static_page_status", "1", "form_menu") }
					{/if}
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="static_page_name" value="{$VAR.static_page_name}" {if $static_page_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="static_page_description" cols="40" rows="5" {if $static_page_description == true}class="form_field_error"{/if}>{$VAR.static_page_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="static_page_translate:add">
                    <input type="hidden" name="_page_current" value="static_page:add">
                    <input type="hidden" name="do[]" value="static_page:add">
                    <input type="hidden" name="static_page_date_last" value="{$smarty.now}">
                    <input type="hidden" name="static_page_date_orig" value="{$smarty.now}">
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
