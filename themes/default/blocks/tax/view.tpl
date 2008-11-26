
{ $method->exe("tax","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'tax';
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
{foreach from=$tax item=tax} <a name="{$tax.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="tax_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=tax}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=tax}
                            field_country_id
                        {/translate}</td>
                    <td width="65%">
                        { $list->menu("", "tax_country_id", "country", "name", $tax.country_id, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=tax}
                            field_zone
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="tax_zone" value="{$tax.zone}"  size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=tax}
                            field_description
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="tax_description" value="{$tax.description}"  size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=tax}
                            field_rate
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="tax_rate" value="{$tax.rate}"  size="5">
                    </td>
                  </tr>
          		<tr class="row1" valign="middle" align="left">
                    <td width="35%">{translate module=tax} field_tax_id_collect{/translate}</td>
                    <td width="65%">{ $list->bool("tax_tax_id_collect", $tax.tax_id_collect, "\" onChange=\"if (this.value==1)  document.getElementById('tax_id').style.display='block'; else document.getElementById('tax_id').style.display='none'; \"") } 
                  </td>
                </tr> 
           		<tr valign="top">
                    <td colspan="2">
					<div  id="tax_id" {if !$tax.tax_id_collect}{style_hide}{/if}>
					  <table width="100%" class="row1"> 		  
					  <tr class="row1" valign="middle" align="left">
						<td width="35%">{translate module=tax} field_tax_id_name{/translate}</td>
						<td width="65%"><input type="text" name="tax_tax_id_name" value="{$tax.tax_id_name}"  size="32"></td>
					  </tr>
					  <!-- <tr class="row1" valign="middle" align="left">
						<td height="22">{translate module=tax} field_tax_id_exempt{/translate}</td>
						<td> { $list->bool("tax_tax_id_exempt", $tax.tax_id_exempt, "") } </td>
					  </tr> -->
					  <tr class="row1" valign="middle" align="left">
						<td>{translate module=tax} field_tax_id_req{/translate}</td>
						<td>{ $list->bool("tax_tax_id_req", $tax.tax_id_req, "") }</td>
					  </tr>
					  <tr class="row1" valign="middle" align="left">
						<td>{translate module=tax} field_tax_id_regex{/translate}</td>
						<td><input type="text" name="tax_tax_id_regex" value="{$tax.tax_id_regex}"  size="32"></td>
					  </tr>
				      </table> 
				      </div>
					</td>
                </tr> 
									  
			<tr class="row1" valign="middle" align="left">
			<td></td>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td align="right"><input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$tax.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="tax:view">
    <input type="hidden" name="tax_id" value="{$tax.id}">
    <input type="hidden" name="do[]" value="tax:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
