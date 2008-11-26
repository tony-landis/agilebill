
{ $method->exe("net_term","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'net_term';
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
{foreach from=$net_term item=net_term} <a name="{$net_term.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="net_term_view" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=net_term}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="net_term_name" value="{$net_term.name}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_sku
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="net_term_sku" value="{$net_term.sku}" size="32">
                    </td>
                  </tr>			
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_terms
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="net_term_terms" value="{$net_term.terms}" size="5">
                    </td>				  
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_status
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("net_term_status", $net_term.status, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_group_avail
                        {/translate}</td>
                    <td width="65%">
                         { $list->menu_multi($net_term.group_avail, "net_term_group_avail", "group", "name", "5", "5", "form_menu") } 
					</td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_checkout_id
                        {/translate}</td>
                    <td width="65%">
                        { $list->menu("", "net_term_checkout_id", "checkout", "name", $net_term.checkout_id, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_fee_type
                        {/translate}</td>
                    <td width="65%">
                        <select name="net_term_fee_type" size="2">
						  <option value="0" {if $net_term.fee_type==0}selected{/if}>Percentage of Invoice Total</option>
						  <option value="1" {if $net_term.fee_type==1}selected{/if}>Fixed Rate</option>
						</select>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_fee
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="net_term_fee" value="{$net_term.fee}" size="5">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_suspend_intervals
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="net_term_suspend_intervals" value="{$net_term.suspend_intervals}" size="5">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_enable_emails
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("net_term_enable_emails", $net_term.enable_emails, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=net_term}
                            field_sweep_type
                        {/translate}</td>
                    <td width="65%">
                    {if $net_term.sweep_type == "0"} Daily {/if} {if $net_term.sweep_type == "1"} Weekly {/if} {if $net_term.sweep_type == "2"} Monthly {/if} {if $net_term.sweep_type == "3"} Quarterly {/if} {if $net_term.sweep_type == "4"} Semi-Anually {/if} {if $net_term.sweep_type == "5"} Anually {/if} {if $net_term.sweep_type == "6"} On Service Rebill {/if}                    </td>
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
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$net_term.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="net_term:view">
    <input type="hidden" name="net_term_id" value="{$net_term.id}">
    <input type="hidden" name="do[]" value="net_term:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
