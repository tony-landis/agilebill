
{ $method->exe("asset","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'asset';
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
{foreach from=$asset item=asset} <a name="{$asset.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="asset_view" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=asset}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($asset.date_orig)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_date_last
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($asset.date_last)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_status
                        {/translate}</td>
                    <td width="65%">
                        {if $asset.status == "1"}{translate}true{/translate}{else}{translate}false{/translate}{/if}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_service_id
                        {/translate}</td>
                    <td width="65%">
                        {if $asset.service_id}                    
							<a href="?_page=service:view&id={$asset.service_id}">ID {$asset.service_id}</a>
						{else}
							---
						{/if}
					</td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_pool_id
                        {/translate}</td>
                    <td width="65%"> 
						{ $list->menu("no", "asset_pool_id", "asset_pool", "name", $asset.pool_id, "") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_asset
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="asset_asset" value="{$asset.asset}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=asset}
                            field_misc
                        {/translate}</td>
                    <td width="65%">
                        <textarea name="asset_misc" cols="40" rows="5">{$asset.misc}</textarea>
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
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$asset.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="asset:view">
    <input type="hidden" name="asset_id" value="{$asset.id}">
    <input type="hidden" name="do[]" value="asset:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
