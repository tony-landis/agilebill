
{ $method->exe("affiliate_commission","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'affiliate_commission';
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
{foreach from=$affiliate_commission item=affiliate_commission} <a name="{$affiliate_commission.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="affiliate_commission_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=affiliate_commission}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate_commission}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($affiliate_commission.date_orig)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate_commission}
                    field_date_begin 
                    {/translate}
                  </td>
                  <td width="65%"> 
				  	{if $affiliate_commission.date_begin > 0}
                    {$list->date($affiliate_commission.date_begin)}
					{else}
					---
					{/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate_commission}
                    field_date_end 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date($affiliate_commission.date_end)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate_commission}
                    field_commissions
                    {/translate}
                  </td>
                  <td width="65%"> {if $affiliate_commission.commissions > 0}
				   {$list->format_currency($affiliate_commission.commissions, '')}
				   {else}0{/if}
				   </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate_commission}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->bool("affiliate_commission_status", $affiliate_commission.status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate_commission}
                    field_notes_admin 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="affiliate_commission_notes_admin" cols="40" rows="5" >{$affiliate_commission.notes_admin}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate_commission}
                    field_notes_affiliate 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="affiliate_commission_notes_affiliate" cols="40" rows="5" >{$affiliate_commission.notes_affiliate}</textarea>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>&nbsp; </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$affiliate_commission.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="affiliate_commission:view">
    <input type="hidden" name="affiliate_commission_id" value="{$affiliate_commission.id}">
    <input type="hidden" name="do[]" value="affiliate_commission:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  <br>
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=affiliate_commission}
                title_view
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <!-- Loop through each affiliate plugin -->
                {foreach from=$plugindata item=plugin}
                <tr valign="top"> 
                  <td width="35%">
                    {$plugin.name}
                  </td>
                  <td width="23%"> 
                    {$plugin.count}
                    {translate module=affiliate_commission}
                    commissions 
                    {/translate}
                  </td>
                  <td width="42%"> 
                    {if $plugin.count > 0}
                    <a href="?_page=core:blank&_escape=1&do%5B%5D=affiliate_commission:export&id={$affiliate_commission.id}&plugin={$plugin.plugin}" target="_blank"> 
                    <b>
                    {translate module=affiliate_commission}
                    export 
                    {/translate}
                    </b>
                    {else}
                    </a> 
                    {translate module=affiliate_commission}
                    do_nothing 
                    {/translate}
                    {/if}
                  </td>
                </tr>
                {/foreach}
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
  {/foreach}
{/if}
