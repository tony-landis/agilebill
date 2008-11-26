{literal}
<script type="text/javascript"> 
   var _editor_url = "includes/htmlarea/";
   var _editor_lang = "{/literal}{$smarty.const.SESS_LANGUAGE}{literal}";
</script>	
<script type="text/javascript" src="includes/htmlarea/htmlarea.js"></script>
{/literal}

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="faq_translate_add" name="faq_translate_add" method="post" action="">
{$COOKIE_FORM}
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=faq_translate}
                title_add 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="23%"> 
                    {translate module=faq_translate}
                    field_faq_id 
                    {/translate}
                  </td>
                  <td width="77%"> 
                    {if $VAR.id == ""}
                    { $list->menu("", "faq_translate_faq_id", "faq", "name", $VAR.faq_translate_faq_id, "form_menu") }
                    {else}
                    { $list->menu("", "faq_translate_faq_id", "faq", "name", $VAR.id, "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="23%"> 
                    {translate module=faq_translate}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="77%"> 
                    {if $VAR.faq_translate_language_id == ""}
                    { $list->menu_files("", "faq_translate_language_id", $smarty.const.DEFAULT_LANGUAGE, "language", "", "_core.xml", "form_menu") }
                    {else}
                    { $list->menu_files("", "faq_translate_language_id", $VAR.faq_translate_language_id, "language", "", "_core.xml", "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="23%"> 
                    {translate module=faq_translate}
                    field_question 
                    {/translate}
                  </td>
                  <td width="77%"> 
				  	<textarea name="faq_translate_question" cols="60" rows="10">{$VAR.faq_translate_question} </textarea>                    
                  </td>
                </tr>
                <tr valign="top">
                  <td>                    {translate module=faq_translate}
field_answer
{/translate}                  </td>
                  <td><textarea name="faq_translate_answer" cols="60" rows="10">{$VAR.faq_translate_answer} 
                  </textarea></td>
                </tr>
                <tr valign="top">
                  <td>&nbsp;</td>
                  <td><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button"></td>
                </tr>
              </table>
              <p><input type="hidden" name="_page" value="faq_translate:view">
                <input type="hidden" name="_page_current" value="faq_translate:add">
                <input type="hidden" name="do[]" value="faq_translate:add">
                <input type="hidden" name="faq_translate_date_last" value="{$smarty.now}">
                <input type="hidden" name="faq_translate_date_orig" value="{$smarty.now}">
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
