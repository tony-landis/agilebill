{ $method->exe("currency","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else} 

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'currency';
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
{foreach from=$currency item=currency} <a name="{$currency.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="update_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=currency}
                title_view
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row0" valign="top"> 
                  <td width="35%"> 
                    {translate module=currency}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="currency_name"  value="{$currency.name}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=currency}
                    field_symbol 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="currency_symbol"  value="{$currency.symbol}" size="2">
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=currency}
                    field_three_digit 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="currency_three_digit"  value="{$currency.three_digit}" size="3" maxlength="3">
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=currency}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->bool("currency_status",$currency.status, "form_menu")}
                  </td>
                </tr>
                <tr class="row3" valign="top"> 
                  <td width="35%"> 
                    {translate module=currency}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="currency_notes" cols="50" rows="2" >{$currency.notes}</textarea>
                  </td>
                </tr>
                <tr class="row3" valign="top">
                  <td width="35%"> 
                    {translate module=currency}
                    field_convert_array 
                    {/translate}
                    <br>
                    <input type="hidden" value="1" name="currency_convert_array[{$currency.id}][rate]">
                    <input type="hidden" value="{$currency.three_digit}" name="currency_convert_array[{$currency.id}][iso]">
                  </td>
                  <td width="65%">
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
					  {assign var=sql1 value=" AND id != '"}
					  {assign var=sql2 value=$currency.id}
					  {assign var=sql3 value="' AND status = '1'"}
					  {assign var=sql  value=$sql1$sql2$sql3} 
                      { if $list->smarty_array("currency", "name,symbol,three_digit", $sql, "currency_arr") }
                      {$list->unserial($currency.convert_array, "convert")}
                      {foreach from=$currency_arr item=c_arr}
                      <tr class="row1" valign="middle" align="left"> 
                        <td width="17%"> <a href="javascript:showXE('{$currency.three_digit}','{$c_arr.three_digit}');"><img src="themes/{$THEME_NAME}/images/currency/{$c_arr.three_digit}.gif" border="0"></a> 
                        </td>
                        <td width="14%"> <b> 
                          {$c_arr.three_digit}
                          </b> &nbsp; </td>
                        <td width="29%"> 
						  {assign var=id value=$c_arr.id}
						  {assign var=convert_rate value=$convert.$id}
                          <input type="text" onfocus="showXE('{$currency.three_digit}','{$c_arr.three_digit}'); select(this);" name="currency_convert_array[{$id}][rate]"  value="{$convert_rate.rate}" size="6">
						  <input type="hidden" value="{$c_arr.three_digit}" name="currency_convert_array[{$id}][iso]">
                        </td>
                        <td width="40%"> 
                          {$c_arr.name}
                        </td>
                      </tr>
                      {/foreach}
                      {/if}
                    </table>
                  </td>
                </tr>
              </table>
				
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>&nbsp; </td>
                        <td align="right">
                          <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
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
  <input type="hidden" name="_page" value="currency:view">
    <input type="hidden" name="currency_id" value="{$currency.id}">
    <input type="hidden" name="do[]" value="currency:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}   

{literal}<SCRIPT LANGUAGE="JavaScript">
<!-- START
function showXE(from,to)
{
	showIFrame('iframeXE',500,150,'http://www.xe.com/ucc/convert.cgi?language=xe&ecc=true&Template=se&Amount=1&From='+from+'&To='+to);  
}
//  END -->
</SCRIPT>{/literal}
   
<!-- Display the Quick Search Iframe -->
<iframe name="iframeXE" id="iframeXE" style="border:0px; width:0px; height:0px" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 


{/if}
