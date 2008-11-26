
{ $method->exe("voip_cdr","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'voip_cdr';
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
{foreach from=$voip_cdr item=voip_cdr} <a name="{$voip_cdr.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="voip_cdr_view" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_cdr}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time($voip_cdr.date_orig)}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                        {html_select_account name=&quot;voip_cdr_account_id&quot; default=$voip_cdr.account_id}</td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_voip_rate_id
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.voip_rate_id}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_clid
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.clid}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_src
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.src}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_dst
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.dst}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_dcontext
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.dcontext}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_channel
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.channel}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_dstchannel
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.dstchannel}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_lastapp
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.lastapp}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_lastdata
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.lastdata}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_duration
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.duration}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_billsec
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.billsec}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_disposition
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.disposition}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_amaflags
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.amaflags}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_accountcode
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.accountcode}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_uniqueid
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.uniqueid}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_cdrid
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.cdrid}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_amount
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.amount}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_calltype
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.calltype}
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_cdr}
                            field_realamount
                        {/translate}</td>
                    <td width="65%">
                        {$voip_cdr.realamount}
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
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$voip_cdr.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="voip_cdr:view">
    <input type="hidden" name="voip_cdr_id" value="{$voip_cdr.id}">
    <input type="hidden" name="do[]" value="voip_cdr:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
