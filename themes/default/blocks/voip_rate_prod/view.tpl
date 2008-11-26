
{ $method->exe("voip_rate_prod","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'voip_rate_prod';
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
{foreach from=$voip_rate_prod item=voip_rate_prod} <a name="{$voip_rate_prod.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="voip_rate_prod_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=voip_rate_prod}
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
                    {translate module=voip_rate_prod}
                    field_product_id 
                    {/translate}
                  </td>
                  <td width="65%">{ $list->menu("no", "voip_rate_prod_product_id", "product", "sku", $voip_rate_prod.product_id, "form_field") }  
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate_prod}
                    field_voip_rate_id
                    {/translate}
                  </td>
                  <td width="65%">{ $list->menu("no", "voip_rate_prod_voip_rate_id", "voip_rate", "name", $voip_rate_prod.voip_rate_id, "form_field") }  
                  </td>
                </tr>


                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"><input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$voip_rate_prod.id}','{$VAR.id}');"> </td>
                  <td width="65%">&nbsp; 
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="voip_rate_prod:view">
    <input type="hidden" name="voip_rate_prod_id" value="{$voip_rate_prod.id}">
    <input type="hidden" name="do[]" value="voip_rate_prod:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>  
  {/foreach}
{/if}
