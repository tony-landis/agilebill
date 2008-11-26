

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="faq_add" name="faq_add" method="post" action="">
{$COOKIE_FORM}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=faq}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="29%"> 
                    {translate module=faq}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="71%">
                    <input type="text" name="faq_sort_order" value="{$VAR.faq_sort_order}" {if $faq_sort_order == true}class="form_field_error"{else}class="form_field"{/if} size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="29%"> 
                    {translate module=faq}
                    field_faq_category_id 
                    {/translate}
                  </td>
                  <td width="71%">
                    { $list->menu("", "faq_faq_category_id", "faq_category", "name", $VAR.faq_faq_category_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="29%"> 
                    {translate module=faq}
                    field_status 
                    {/translate}
                  </td>
                  <td width="71%"> 
				  {if $VAR.faq_status != ""}
                    { $list->bool("faq_status", $VAR.faq_status, "form_menu") }
				{else}
                    { $list->bool("faq_status", "1", "form_menu") }
					{/if}
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="29%">{translate module=faq}
field_name{/translate}
                  </td>
                  <td width="71%"> 
                    <input name="faq_name" type="text" value="{$VAR.faq_name}" size="45" {if $faq_name == true}class="form_field_error"{else}class="form_field"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="29%">{translate module=faq_translate}
field_question
  {/translate}</td>
                  <td width="71%"><textarea name="faq_question" cols="65" rows="10">{$VAR.faq_question}</textarea></td>
                </tr>
                <tr valign="top">
                  <td>{translate module=faq_translate}
field_answer
  {/translate}</td>
                  <td><textarea name="faq_answer" cols="65" rows="10">{$VAR.faq_answer}</textarea></td>
                </tr>
                <tr valign="top">
                  <td></td>
                  <td><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="faq:view">
                    <input type="hidden" name="_page_current" value="faq:add">
                    <input type="hidden" name="do[]" value="faq:add">
                    <input type="hidden" name="faq_date_last" value="{$smarty.now}">
                    <input type="hidden" name="faq_date_orig" value="{$smarty.now}"></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
