{ $method->exe("email_template","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else} 

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'email_template';
    	var locations 	= '{/literal}{$VAR.module_id}{literal}';		
		var id 			= '{/literal}{$VAR.id}{literal}';
		var ids 		= '{/literal}{$VAR.ids}{literal}';    	 
		var array_id    = id.split(",");
		var array_ids   = ids.split(",");		
		var num=0;
		if(array_id.length > 2) {				 
			document.location = '?_page='+module+':view&id='+array_id[0]+'&ids='+id;				 		
		}else if (array_ids.length > 2) {
			document.write(view_nav_top(array_ids,id,ids));
		}
		
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
{/literal}

<!-- Loop through each record -->
{foreach from=$email_template item=email_template} 

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="email_template_view" method="post" action=""> 
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=email_template}
                title_view 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=email_template}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$email_template.name}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=email_template}
                    field_setup_email_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu("", "email_template_setup_email_id", "setup_email", "name", $email_template.setup_email_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=email_template}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("email_template_status", $email_template.status,"form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=email_template}
                    field_priority 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("email_template_priority", $email_template.priority, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=email_template}
                    field_sql_1 
                    {/translate}
                  </td>
                  <td width="50%">
                    {translate module=email_template}
                    field_sql_2 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%">
                    <textarea name="email_template_sql_1" cols="40" rows="1" >{$email_template.sql_1}</textarea>
                  </td>
                  <td width="50%">
                    <textarea name="email_template_sql_2" cols="40" rows="1" >{$email_template.sql_2}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%">
                    {translate module=email_template}
                    field_sql_3 
                    {/translate}
                  </td>
                  <td width="50%">
                    {translate module=email_template}
                    field_notes 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <textarea name="email_template_sql_3" cols="40" rows="1" >{$email_template.sql_3}</textarea>
                  </td>
                  <td width="50%">
                    <textarea name="email_template_notes" cols="40" rows="1" >{$email_template.notes}</textarea>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="50%"> 
                    <div align="right">
                      <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$email_template.id}','{$VAR.id}');">
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top">
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="row1">
                <tr> 
                  <td width="50%"> <a href="javascript:showEmailTranslations('{$email_template.id}');"> 
                    {translate module=email_template}
                    view_translations 
                    {/translate}
                    </a> </td>
                  <td align="right" width="50%"> 
                    <div align="right">
					<a href="javascript:addEmailTranslations('{$email_template.id}');"> 
					{translate module=email_template}add_translation{/translate}
					</a>
					</div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="email_template:view">
  <input type="hidden" name="email_template_id" value="{$email_template.id}">
  <input type="hidden" name="do[]" value="email_template:update">
  <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  <center>  
	<iframe name="iframeEmail" id="iframeEmail" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
  </center>
  <script language=javascript>
  var email_template = '{$email_template.id}';
  </script>
{/foreach}
{/if}
{literal}
<script language="JavaScript">
<!-- START  
function showEmailTranslations(id) { 
	showIFrame('iframeEmail',getPageWidth(650),350,'?_page=core:search&_next_page_one=view&module=email_template_translate&_escape=1&email_template_translate_email_template_id='+id);
} 
function addEmailTranslations(id) { 
	showIFrame('iframeEmail',getPageWidth(650),350,'?_page=email_template_translate:add&email_template_translate_email_template_id='+id);
} 
showEmailTranslations(email_template);
//  END -->
</script>
{/literal}
