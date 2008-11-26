
{ $method->exe("voip_pool","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'voip_pool';
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
{foreach from=$voip_pool item=voip_pool} <a name="{$voip_pool.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="voip_pool_view" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_pool}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="16%">
                        {translate module=voip_pool}
                            field_npa
                        {/translate}</td>
                    <td width="84%">
                        <input name="voip_pool_npa" type="text" value="{$voip_pool.npa}" size="3" maxlength="3">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="16%">
                        {translate module=voip_pool}
                            field_nxx
                        {/translate}</td>
                    <td width="84%">
                        <input name="voip_pool_nxx" type="text" value="{$voip_pool.nxx}" size="3" maxlength="3">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="16%">
                        {translate module=voip_pool}
                            field_station
                        {/translate}</td>
                    <td width="84%">
                        <input name="voip_pool_station" type="text" value="{$voip_pool.station}" size="4" maxlength="4">
                    </td>
                  </tr>
          <tr class="row1" valign="middle" align="left">
                    <td width="16%"></td>
                    <td width="84%">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>
                            <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                          </td>
                          <td align="right">
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$voip_pool.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="voip_pool:view">
    <input type="hidden" name="voip_pool_id" value="{$voip_pool.id}">
    <input type="hidden" name="do[]" value="voip_pool:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
