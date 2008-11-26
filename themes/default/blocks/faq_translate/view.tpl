
{ $method->exe("faq_translate","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'faq_translate';
    	var locations = '{/literal}{$VAR.module_id}{literal}';
    	if (locations != "")
    	{
    		refresh(0,'#'+locations)
    	}
    	// Mass update, view, and delete controller
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
	
	<script type="text/javascript"> 
	   var _editor_url  = "includes/htmlarea/";
	   var _editor_lang = "{/literal}{$smarty.const.SESS_LANGUAGE}{literal}";
	</script>	
	<script type="text/javascript" src="includes/htmlarea/htmlarea.js"></script>

{/literal}

<!-- Loop through each record -->
{foreach from=$faq_translate item=faq_translate} <a name="{$faq_translate.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="faq_translate_view" method="post" action="">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=faq_translate}
                title_view
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq_translate}
                    field_faq_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "faq_translate_faq_id", "faq", "name", $faq_translate.faq_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq_translate}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_files("", "faq_translate_language_id", $faq_translate.language_id, "language", "", "_core.xml", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq_translate}
                    field_question 
                    {/translate}
                  </td>
                  <td width="65%">  
					<textarea name="faq_translate_question" cols="65" rows="6">{$faq_translate.question}</textarea>
                  </td>
                </tr>
                <tr valign="top">
                  <td>                    {translate module=faq_translate}
field_answer
{/translate}                  </td>
                  <td><textarea name="faq_translate_answer" cols="65" rows="6">{$faq_translate.answer}</textarea></td>
                </tr>
                <tr valign="top">
                  <td><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button"></td>
                  <td><table width="100%" border="0" cellspacing="2" cellpadding="5" class="row2">
                    <tr>
                      <td><a href="?_page=core:search&module=faq_translate&faq_translate_faq_id={$faq_translate.faq_id}"> </a><a href="?_page=core:search&module=faq_translate&faq_translate_faq_id={$faq_translate.faq_id}">
                        {translate module=faq_translate}
      view_all
      {/translate}
                      </a> </td>
                      <td align="right"><a href="?_page=faq:view&id={$faq_translate.faq_id}"> </a>
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$faq_translate.id}','{$VAR.id}');">
                      </td>
                    </tr>
                  </table></td>
                </tr>
              </table>  
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="faq_translate:view">
    <input type="hidden" name="faq_translate_id" value="{$faq_translate.id}">
    <input type="hidden" name="do[]" value="faq_translate:update">
    <input type="hidden" name="id" value="{$VAR.id}">
    <input type="hidden" name="faq_translate_date_last" value="{$smarty.now}">
</form>
  {/foreach}
{/if}
