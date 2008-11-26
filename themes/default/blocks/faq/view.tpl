
{ $method->exe("faq","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'faq';
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
{/literal}

<!-- Loop through each record -->
{foreach from=$faq item=faq} 

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="faq_view" method="post" action="">
{$COOKIE_FORM}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=faq}
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
                    {translate module=faq}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($faq.date_orig)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($faq.date_last)}
                  </td>
                </tr>							  
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="faq_sort_order" value="{$faq.sort_order}" class="form_field" size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->calender_view("faq_date_start", $faq.date_start, "form_field", $faq.id) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_view("faq_date_expire", $faq.date_expire, "form_field", $faq.id) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq}
                    field_faq_category_id 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->menu("", "faq_faq_category_id", "faq_category", "name", $faq.faq_category_id, "form_menu") }
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("faq_status", $faq.status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=faq}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="faq_name" value="{$faq.name}" class="form_field" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td> 
                          <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                        </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$faq.id}','{$VAR.id}');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="5" class="row1">
                <tr> 
                  <td width="42%"> <a href="javascript:showFaqTranslations('{$faq.id}')"> 
                    {translate module=faq}
                    view_all 
                    {/translate}
                    </a></td>
                  <td align="right" width="39%"> <a href="javascript:addFaqTranslations('{$faq.id}')"> 
                    {translate module=faq}
                    add_translation 
                    {/translate}
                    </a> </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="faq:view">
    <input type="hidden" name="faq_id" value="{$faq.id}">
    <input type="hidden" name="do[]" value="faq:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  <input type="hidden" name="faq_date_last" value="{$smarty.now}">
</form>

  <center>  
	<iframe name="iframeFaq" id="iframeFaq" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
  </center>
  
  
{literal}
<script language="JavaScript">
<!-- START  
function showFaqTranslations(id) { 
	showIFrame('iframeFaq',getPageWidth(650),350,'?_page=core:search&_next_page_one=view&module=faq_translate&_escape=1&faq_translate_faq_id='+id);
} 
function addFaqTranslations(id) { 
	showIFrame('iframeFaq',getPageWidth(650),350,'?_page=faq_translate:add&faq_translate_faq_id='+id);
} 
showFaqTranslations({/literal}{$faq.id}{literal});
//  END -->
</script>
{/literal} 
  
  {/foreach}
  

    
 
{/if}
