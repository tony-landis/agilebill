
{ $method->exe("voip_blacklist","user_view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript"> 
        var module = 'voip_blacklist';
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
    			var url = '?_page=core:user_search&module=' + module + '&do[]=' + module + ':user_delete&_next_page=user_search_show&delete_id=' + id; 
				window.location = url;
    			return;
    		} else {
    			var page = 'user_view&id=' +ids;
    		}		
    		
    		var doit = 'delete';
    		var url = '?_page='+ module +':user_'+ page +'&do[]=' + module + ':user_' + doit + '&delete_id=' + id;
    		window.location = url;	
    	} 
    </script>
{/literal}

<!-- Loop through each record -->
{foreach from=$voip_blacklist item=voip_blacklist}  

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="voip_blacklist_view" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_blacklist}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_blacklist}
                            field_voip_did_id
                        {/translate}</td>
                    <td width="65%"> 
					{html_menu field=voip_blacklist_voip_did_id assoc_table=voip_did assoc_field=did conditions="account_id = `$smarty.const.SESS_ACCOUNT`" default=$voip_blacklist.voip_did_id }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_blacklist}
                            field_src
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_blacklist_src" value="{$voip_blacklist.src}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_blacklist}
                            field_dst
                        {/translate}</td>
                    <td width="65%"> 
                        <select name="voip_blacklist_dst">
                          <option value="Playback tt-monkeys" {if $voip_blacklist.dst=='Playback tt-monkeys'}selected{/if}>Screaming Monkeys</option>
                          <option value="Playback tt-somethingwrong" {if $voip_blacklist.dst=='Playback tt-somethingwrong'}selected{/if}>Something has gone terribly wrong</option>
                          <option value="Playback tt-weasels" {if $voip_blacklist.dst=='Playback tt-weasels'}selected{/if}>Weasels have eaten the phone system</option>
                          <option value="Playback discon-or-out-of-service" {if $voip_blacklist.dst=='Playback discon-or-out-of-service'}selected{/if}>Number disconnected or out of service</option>
                          <option value="Congestion" {if $voip_blacklist.dst=='Congestion'}selected{/if}>Fast Busy Signal</option>
                          <option value="Hangup" {if $voip_blacklist.dst=='Hangup'}selected{/if}>Hang up on caller</option>
                        </select>
                    </td>
                  </tr>
          <tr class="row1" valign="middle" align="left">
                    <td width="35%"><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button"></td>
                    <td width="65%">
                      <div align="right">
                        <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$voip_blacklist.id}','{$VAR.id}');">
                      </div></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="voip_blacklist:user_view">
    <input type="hidden" name="voip_blacklist_id" value="{$voip_blacklist.id}">
    <input type="hidden" name="do[]" value="voip_blacklist:user_update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
