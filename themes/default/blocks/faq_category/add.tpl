

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="faq_category_add" name="faq_category_add" method="post" action="">
{$COOKIE_FORM}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=faq_category}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq_category}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($VAR.faq_category_group_avail, 'faq_category_group_avail', 'group', 'name', '', '10', 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq_category}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="faq_category_name" value="{$VAR.faq_category_name}" {if $faq_category_name == true}class="form_field_error"{else}class="form_field"{/if} size="35">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq_category}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="faq_category_description" cols="40" rows="5" {if $faq_category_description == true}class="form_field_error"{else}class="form_field"{/if}>{$VAR.faq_category_description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq_category}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("faq_category_status", $VAR.faq_category_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq_category}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="faq_category_sort_order" value="{$VAR.faq_category_sort_order}" {if $faq_category_sort_order == true}class="form_field_error"{else}class="form_field"{/if} size="5">
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%"></td>
                  <td width="65%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="faq_category:view">
                    <input type="hidden" name="_page_current" value="faq_category:add">
                    <input type="hidden" name="do[]" value="faq_category:add">
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
