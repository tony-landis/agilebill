
{ $method->exe("voip_did","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'voip_did';
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
{foreach from=$voip_did item=voip_did} <a name="{$voip_did.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="voip_did_view" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_did}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                     {html_select_account name="voip_did_account_id" default=$voip_did.account_id} 
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_active
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_active", $voip_did.active, "form_menu") }
                    </td>
                  </tr>				  
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_service_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_service_id" value="{$voip_did.service_id}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_service_parent_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_service_parent_id" value="{$voip_did.service_parent_id}" size="32">
                    </td>
                  </tr> 
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_did
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_did" value="{$voip_did.did}" size="32">
                    </td>
                  </tr>

                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_cnam
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_cnam", $voip_did.cnam, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_blacklist
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_blacklist", $voip_did.blacklist, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_anirouting
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_anirouting", $voip_did.anirouting, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_faxdetection
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_faxdetection", $voip_did.faxdetection, "form_menu") }
                    </td>
                  </tr>                                                      
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_channel
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_channel" value="{$voip_did.channel}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_channelarg
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_channelarg" value="{$voip_did.channelarg}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_voicemailenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_voicemailenabled", $voip_did.voicemailenabled, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_callforwardingenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_callforwardingenabled", $voip_did.callforwardingenabled, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_busycallforwardingenabled
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_busycallforwardingenabled", $voip_did.busycallforwardingenabled, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_voicemailafter
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_voicemailafter" value="{$voip_did.voicemailafter}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_cfringfor
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_cfringfor" value="{$voip_did.cfringfor}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_cfnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_cfnumber" value="{$voip_did.cfnumber}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_bcfnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_bcfnumber" value="{$voip_did.bcfnumber}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_rxfax
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_rxfax", $voip_did.rxfax, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_faxemail
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_faxemail" value="{$voip_did.faxemail}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_conf
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_conf", $voip_did.conf, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_conflimit
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_conflimit" value="{$voip_did.conflimit}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_failover
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_failover", $voip_did.failover, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_failovernumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_failovernumber" value="{$voip_did.failovernumber}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_remotecallforward
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("voip_did_remotecallforward", $voip_did.remotecallforward, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_remotecallforwardnumber
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_remotecallforwardnumber" value="{$voip_did.remotecallforwardnumber}" size="32">
                    </td>
                  </tr>
                  
                  <tr valign="top">
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_callerid
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="sip_callerid" value="{$sip_callerid|escape:"html"}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_username
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="sip_username" value="{$sip_username|escape:"html"}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_secret
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="sip_secret" value="{$sip_secret|escape:"html"}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_nat
                        {/translate}</td>
                    <td width="65%">
                        <select name="sip_nat">{html_options options=$sip_nat_options selected=$sip_nat}</select>
                    </td>
                  </tr>                  
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_qualify
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="sip_qualify" value="{$sip_qualify}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_mailbox
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="sip_mailbox" value="{$sip_mailbox}" size="32">
                    </td>
                  </tr>                  
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_incominglimit
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="sip_incominglimit" value="{$sip_incominglimit}" size="32"> (1 disables call waiting)
                    </td>
                  </tr>    
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_dtmfmode
                        {/translate}</td>
                    <td width="65%">
                        <select name="sip_dtmfmode">{html_options options=$sip_dtmfmode_options selected=$sip_dtmfmode}</select>
                    </td>
                  </tr>  
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did}
                            field_canreinvite
                        {/translate}</td>
                    <td width="65%">
                        <select name="sip_canreinvite">{html_options options=$sip_canreinvite_options selected=$sip_canreinvite}</select>
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
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$voip_did.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="voip_did:view">
    <input type="hidden" name="voip_did_id" value="{$voip_did.id}">
    <input type="hidden" name="do[]" value="voip_did:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
