
{ $method->exe("newsletter_subscriber","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'newsletter_subscriber';
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
{foreach from=$newsletter_subscriber item=newsletter_subscriber} <a name="{$newsletter_subscriber.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="newsletter_subscriber_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=newsletter_subscriber}
                title_view
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->date_time($newsletter_subscriber.date_orig) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_newsletter_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "newsletter_subscriber_newsletter_id", "newsletter", "name", $newsletter_subscriber.newsletter_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_email 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_subscriber_email" value="{$newsletter_subscriber.email}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_html 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("newsletter_subscriber_html", $newsletter_subscriber.html, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_first_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_subscriber_first_name" value="{$newsletter_subscriber.first_name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=newsletter_subscriber}
                    field_last_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="newsletter_subscriber_last_name" value="{$newsletter_subscriber.last_name}"  size="32">
                  </td>
                </tr>
				
                {foreach from=$static_var item=record}
                <tr valign="top"> 
                  <td width="35%"> 
                    {$record.name}
                  </td>
                  <td width="65%"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}
 
								
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>&nbsp; </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$newsletter_subscriber.id}','{$VAR.id}');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="newsletter_subscriber:view">
    <input type="hidden" name="newsletter_subscriber_id" value="{$newsletter_subscriber.id}">
    <input type="hidden" name="do[]" value="newsletter_subscriber:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
