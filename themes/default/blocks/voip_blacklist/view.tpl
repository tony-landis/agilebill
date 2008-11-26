
{ $method->exe("voip_blacklist","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
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
{foreach from=$voip_blacklist item=voip_blacklist} <a name="{$voip_blacklist.id}"></a>

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
                    <p>{ $list->menu("no", "voip_blacklist_voip_did_id", "voip_did", "did", $voip_blacklist.voip_did_id, "form_menu") } </p>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_blacklist}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                    {html_select_account name="voip_blacklist_account_id" default=$voip_blacklist.account_id} </td>
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
                    <td width="35%"></td>
                    <td width="65%">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>
                            <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                          </td>
                          <td align="right">
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$voip_blacklist.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="voip_blacklist:view">
    <input type="hidden" name="voip_blacklist_id" value="{$voip_blacklist.id}">
    <input type="hidden" name="do[]" value="voip_blacklist:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
