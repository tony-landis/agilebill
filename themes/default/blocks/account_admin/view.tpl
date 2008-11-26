{ $method->exe("account_admin","view") } { if ($method->result == FALSE) }  { $block->display("core:method_error") } {else} 

{literal} 
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'account_admin';
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
		
		function showAccount(element)
		{
			if(element == 'collapse') 
				hideAll(); 
			else if(document.getElementById(element).style.display == 'none') 
				hideAll();
			document.getElementById(element).style.display='block';
			document.getElementById('tab').value = element; 
		}	
		
		function hideAll() 
		{
			var elements = new Array(10);
			elements[0] = 'main';
			elements[1] = 'options';
			elements[2] = 'custom';
			elements[3] = 'notes'; 
			elements[4] = 'overview'; 
			elements[5] = 'collapse';
			for(i=0;i<6;i++) document.getElementById(elements[i]).style.display='none'; 
			showIFrame('iframeAccount',0,0,'themes/{/literal}{$THEME_NAME}{literal}/IEFrameWarningBypass.htm');
		}
		
		function accountJump(account_id,tid)
		{
			var action = document.getElementById('Jump').value;
			if(action == 'become') {
				document.location = '{/literal}{$URL}{literal}?_page=account:account&id='+account_id+'&account_id='+account_id+'&do[]=account_admin:login&tid='+tid;
			} else if (action == 'suspend') {
				document.forms.update_form.account_admin_status.value='0';
				document.forms.update_form.submit();
			} else if (action == 'unsuspend') {
				document.forms.update_form.account_admin_status.value='1';
				document.forms.update_form.submit();
			} else if (action == 'verify_email') {
				showIFrame('iframeAccount',getPageWidth(600),350,'?_page=core:blank&_escape=1&do[]=account_admin:send_verify_email&id='+account_id);
			} else if (action == 'merge') { 
				showIFrame('iframeAccount',getPageWidth(600),350,'?_page=account_admin:merge&id='+account_id);
			} else if (action == 'email') { 
				showIFrame('iframeAccount',getPageWidth(600),350,'?_page=account_admin:iframe_mail_one&_escape=1&mail_account_id='+account_id);
			} else if (action == 'pw') { 
				showIFrame('iframeAccount',getPageWidth(600),350,'?_page=core:blank&_escape=1&do[]=account_admin:send_password_email&id='+account_id);
			} else if (action == 'refresh') { 
				window.location.href = '?_page=account_admin:view&id='+account_id;
			}
			document.forms.update_form.Jump.value='0';
		}
		
		function accountJumpView(account_id,parent_account_id) {
			var module = document.getElementById('JumpView').value;
			document.forms.update_form.JumpView.value='0';
			var url = '?_page=core:search&module='+module+'&_next_page_one=view&'+module+'_account_id='+account_id;
		    if(module != 'invoice' && module != 'service'  && module != 'subaccount' && module != 'parentaccount' ) {
				showAccount('collapse');
				showIFrame('iframeAccount',getPageWidth(600),500,url);
			} else if (module == 'subaccount') { 
				var url = '?_page=core:search&module=account_admin&account_admin_parent_id='+account_id;
				document.location = url;
			} else if (module == 'parentaccount') { 
				var url = '?_page=account_admin:view&id='+parent_account_id;
				document.location = url;
			} else {
				document.location = url;
			}
		}
		
		function accountJumpAdd(account_id) {
			var module = document.getElementById('JumpAdd').value;
			document.forms.update_form.JumpAdd.value='0';
			if(module == 'discount')
				var url = '?_page='+module+':add&'+module+'_avail_account_id='+account_id;
			else
				var url = '?_page='+module+':add&'+module+'_account_id='+account_id;
		    if(module != 'invoice' && module != 'service' && module != 'account_admin') {
				showAccount('collapse');
				showIFrame('iframeAccount',getPageWidth(600),500,url);
			} else {
				document.location = url;
			}
		} 
    </script>
{/literal}

<!-- Loop through each record -->
{foreach from=$account item=account_admin} 

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form id="update_form" name="update_form"  method="post" action="">
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading"> 
            <div align="center"> 
              {translate module=account_admin}
              title_view 
              {/translate}
            </div>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
              <tr valign="top" class="row1"> 
                <td width="33%"> <b> </b> 
                    <table width="100%" border="0" cellspacing="6" cellpadding="0" align="left"  class="body">
                      <tr class="row1" align="center"> 
                        <td width="33%" align="left"> 
                          <select name="Jump" id="Jump" onChange="accountJump('{$account_admin.id}','{$account_admin.theme_id}')">
                            <option value="0"> 
                            {translate module=account_admin}
                            jump 
                            {/translate}
                            <option value="refresh"> 
                            {translate module=account_admin}
                            jump_refresh
                            {/translate}
                            </option>  							
                            
                            {if $smarty.const.SESS_ACCOUNT != $account_admin.id }
                            <option value="become"> 
                            {translate module=account_admin}
                            jump_become 
                            {/translate}
                            </option>
                            {/if}
                            {if $account_admin.status == 1}
                            <option value="suspend"> 
                            {translate module=account_admin}
                            jump_suspend 
                            {/translate}
                            </option>
                            {else}
                            <option value="unsuspend"> 
                            {translate module=account_admin}
                            jump_unsuspend 
                            {/translate}
                            </option>
                            <option value="verify_email"> 
                            {translate module=account_admin}
                            jump_verify_email 
                            {/translate}
                            </option>
                            {/if} 
                            <option value="merge"> 
                            {translate module=account_admin}
                            jump_merge 
                            {/translate}
                            </option>
                            <option value="email"> 
                            {translate module=account_admin}
                            jump_email 
                            {/translate}
                            </option>
                            <option value="pw"> 
                            {translate module=account_admin}
                            jump_pw 
                            {/translate}
                            </option>                          
                          </select>
                        </td>
                        <td width="33%"> 
                          <select name="JumpView" id="JumpView" onChange="accountJumpView('{$account_admin.id}','{$account_admin.parent_id}')">
                            <option value="0"> 
                            {translate module=account_admin}
                            jump_view 
                            {/translate}
                            </option> 
                            <option value="account_memo"> 
                            {translate module=account_admin}
                            jump_memos 
                            {/translate}
                            </option> 							
							{if $account_admin.invoice}
                            <option value="invoice"> 
                            {translate module=account_admin}
                            jump_invoices 
                            {/translate}
                            </option>
							{/if}
							{if $account_admin.service}
                            <option value="service"> 
                            {translate module=account_admin}
                            jump_services 
                            {/translate}
                            </option>
							{/if}
                            <option value="login_log"> 
                            {translate module=account_admin}
                            jump_login_logs 
                            {/translate}
                            </option>
                            <option value="session"> 
                            {translate module=account_admin}
                            jump_sessions 
                            {/translate}
                            </option>
							{if $list->is_installed('ticket')}
                            <option value="ticket"> 
                            {translate module=account_admin}
                            jump_tickets 
                            {/translate}
                            </option>
							{/if}
							{if $list->is_installed('affiliate')}
                            <option value="affiliate"> 
                            {translate module=account_admin}
                            jump_affiliate 
                            {/translate}
                            </option>
							{/if}
                            <option value="account_billing"> 
                            {translate module=account_admin}
                            jump_billing 
                            {/translate}
                            </option>
                            <option value="discount"> 
                            {translate module=account_admin}
                            jump_discounts 
                            {/translate}
                            </option> 
							{if $account_admin.parent_id == 0 || $account_admin.parent_id == ''}
							<option value="subaccount">
							{translate module=account_admin}jump_subaccount{/translate}
							</option>
							{else if $account_admin.parent_id > 0}
							<option value="parentaccount">
							{translate module=account_admin}jump_parentaccount{/translate}
							</option>
							{/if}											
                          </select>
                        </td>
                        <td width="33%" align="right"> 
                          <select name="JumpAdd" id="JumpAdd" onChange="accountJumpAdd('{$account_admin.id}')">
                            <option value="0"> 
                            {translate module=account_admin}
                            jump_add 
                            {/translate}
							<option value="account_memo"> 
                            {translate module=account_admin}
                            jump_add_memo 
                            {/translate}
                            </option>
                            <option value="invoice"> 
                            {translate module=account_admin}
                            jump_add_invoice 
                            {/translate}
                            </option>
                            <option value="service"> 
                            {translate module=account_admin}
                            jump_add_service 
                            {/translate}
                            </option>
							{if $list->is_installed('ticket')}
                            <option value="ticket"> 
                            {translate module=account_admin}
                            jump_add_ticket 
                            {/translate}
							{/if}
                            </option>
                            <option value="account_billing"> 
                            {translate module=account_admin}
                            jump_add_billing 
                            {/translate}
                            </option>
                            <option value="discount"> 
                            {translate module=account_admin}
                            jump_add_discount 
                            {/translate}
                            </option>
							{if $list->is_installed('affiliate')}
                            <option value="affiliate"> 
                            {translate module=account_admin}
                            jump_add_affiliate 
                            {/translate}
                            </option>
							{/if}
							{if $account_admin.parent_id == 0 || $account_admin.parent_id == ''}
							<option value="account_admin">
							{translate module=account_admin}jump_add_subaccount{/translate}
							</option>
							{/if}
                          </select>
                        </td>
                      </tr>
                    </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> <a href="javascript:showAccount('overview');"><font color="#FFFFFF"> 
                {translate module=account_admin}
                menu_overview 
                {/translate}
                </font></a> | <a href="javascript:showAccount('main');"><font color="#FFFFFF"> 
                {translate module=account_admin}
                menu_details 
                {/translate}
                </font></a> | <a href="javascript:showAccount('options');"><font color="#FFFFFF"> 
                {translate module=account_admin}
                menu_settings 
                {/translate}
                </font></a> | <a href="javascript:showAccount('notes');"><font color="#FFFFFF"> 
                {translate module=account_admin}
                menu_notes 
                {/translate}
                </font></a> | 
                {if $account_admin.static_var}
                <a href="javascript:showAccount('custom');"><font color="#FFFFFF"> 
                {translate module=account_admin}
                menu_custom_fields 
                {/translate}
                </font></a> 
                {/if}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <!-- COLLAPSED VIEW -->
              <div id="collapse" {style_hide}> 
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top" class="row1"> 
                    <td width="33%"><b><a href="javascript:showAccount('overview');">
					{translate module=account_admin}restore_overview{/translate}
					</a> </b> </td>
                  </tr>
                </table>
              </div>			
              <!-- ACCOUNT OVERVIEW -->
              <div id="overview" {style_hide}> 
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top" class="row1"> 
                    <td width="25%"> 
                      <table width="100%" border="0" cellspacing="4" cellpadding="0" class="body">
                        <tr> 
                          <td colspan="3"> <b>{translate module=account_admin}menu_overview{/translate}</b></td>
                        </tr>
                        <tr> 
                          <td width="5">&nbsp; </td>
                          <td colspan="2"> 
                            {$account_admin.first_name}
                            {$account_admin.middle_name}
                            {$account_admin.last_name}
                          </td>
                        </tr>
                        <tr> 
                          <td width="5">&nbsp; </td>
                          <td colspan="2"> 
                            {$account_admin.address1}
                            {if $account_admin.address2 != ""}
                            , 
                            {$account_admin.address2}
                            {/if}
                          </td>
                        </tr>
                        <tr> 
                          <td width="5">&nbsp; </td>
                          <td colspan="2"> 
                            {$account_admin.city}
                            , 
                            {$account_admin.state}
                            {$account_admin.zip}
                          </td>
                        </tr>
                        <tr> 
                          <td width="5">&nbsp; </td>
                          <td colspan="2"> <a href="javascript:showIFrame('iframeAccount',getPageWidth(600),350,'?_page=account_admin:iframe_mail_one&_escape=1&mail_account_id={$account_admin.id}')"> 
                            {$account_admin.email}
                            </a></td>
                        </tr>						
                        <tr> 
                          <td colspan="3"> <b> 
                            {translate module=account_admin}
                            field_date_orig 
                            {/translate}
                            </b> </td>
                        </tr>
                        <tr> 
                          <td width="5">&nbsp;</td>
                          <td colspan="2"> 
                            {$list->date_time($account_admin.date_orig)}
                          </td>
                        </tr>
                        <tr> 
                          <td colspan="3"> <b> 
                            {translate module=account_admin}
                            field_date_last 
                            {/translate}
                            </b> </td>
                        </tr>
                        <tr> 
                          <td width="5">&nbsp;</td>
                          <td colspan="2"> 
                            {$list->date_time($account_admin.date_last)}
                            <input type="hidden" name="account_admin_date_last" value="{$smarty.now}">
                          </td>
                        </tr>
                        <tr> 
                          <td colspan="3"><b>{translate module=account_admin}last_activity{/translate}</b></td>
                        </tr>
                        <tr> 
                          <td width="5">&nbsp;</td>
                          <td colspan="2"> 
                            {$list->date_time($account_admin.last_activity)}
                          </td>
                        </tr>
                        <tr> 
                          <td width="5">&nbsp;</td>
                          <td colspan="2"> 
                            {if $account_admin.last_ip != ""}
                            <a href="javascript:showIFrame('iframeAccount',getPageWidth(600),250,'?_page=login_log:whois&ip={$account_admin.last_ip}')"> 
                            {$account_admin.last_ip}
                            </a> &nbsp;&nbsp;[ <a href="javascript:showIFrame('iframeAccount',getPageWidth(600),250,'?_page=login_log:map&ip={$account_admin.last_ip}')">?</a> 
                            ] 
                            {/if}
                          </td>
                        </tr>
                      </table>
                    </td>
                    <td width="75%"> 
                      <table width="100%" border="0" cellspacing="4" cellpadding="0" class="body">
                        
						{if $account_admin.invoice}
						<tr> 
                          <td colspan="6"><b>{translate module=account_admin}invoice_overview{/translate}</b></td>
                        </tr> 
                        {foreach from=$account_admin.invoice item=invoice}
                        <tr valign="middle"> 
                          <td width="6">&nbsp; </td>
                          <td width="38"> 
                            {if $invoice.process_status == "1"}
                            <img src="themes/{$THEME_NAME}/images/icons/go_16.gif" border="0"> 
                            {else}
                            <img src="themes/{$THEME_NAME}/images/icons/stop_16.gif" border="0"> 
                            {/if}
                          </td>
                          <td width="70"><a href="?_page=invoice:view&id={$invoice.id}"> 
                            {$invoice.id}
                            </a> </td>
                          <td width="70" align="right"> 
                            {if $invoice.due > 0}
                            <font color="#666666"> 
                            {$list->format_currency_num($invoice.total_amt, '')}
                            </font> 
                            {else}
                            <font color="#006600"> <b> 
                            {$list->format_currency_num($invoice.total_amt, '')}
                            </b></font> 
                            {/if}
                          </td>
                          <td width="100" align="right"> 
                            {if $invoice.due > 0}
                            <font color="#CC0000"> <b> 
                            {$list->format_currency_num($invoice.due, '')}
                            </b> </font> 
                            {else}
                            <font color="#999999"> 
                            {$list->format_currency_num('0', '')}
                            </font> 
                            {/if}
                          </td>
                          <td>&nbsp;&nbsp;&nbsp;&nbsp; 
                            {$list->date($invoice.date_orig)}
                          </td>
                        </tr>
                        {/foreach}
						{/if}

						{if $account_admin.service}
                        <tr> 
                          <td colspan="6"><b><br>
                            {translate module=account_admin}service_overview{/translate}</b></td>
                        </tr>
                        {foreach from=$account_admin.service item=service}
                        <tr valign="middle"> 
                          <td width="6">&nbsp; </td>
                          <td width="38"> 
                            {if $service.active == "1"}
                            <img src="themes/{$THEME_NAME}/images/icons/go_16.gif" border="0"> 
                            {else}
                            <img src="themes/{$THEME_NAME}/images/icons/stop_16.gif" border="0"> 
                            {/if}
                          </td>
                          <td><a href="?_page=service:view&id={$service.id}"> 
                            {$service.id}
                            </a> </td>
                          <td align="right"> 
                            {$list->format_currency_num($service.price, '')}
                          </td>
                          <td align="right"> 
                            {$service.sku}
                          </td>
                          <td> &nbsp;&nbsp;&nbsp;&nbsp; 
                            {if $service.type == 'domain'}
                            {$service.domain_name|lower}
                            . 
                            {$service.domain_tld|lower}
                            {else}
                            {translate module=service}
                            {$service.type}
                            {/translate}
                            {/if}
                          </td>
                        </tr>
                        {/foreach}
						{/if}
						
                      </table>
                    </td>
                  </tr>
                </table>
              </div>			
              <!-- MAIN ACCOUNT DETAILS -->
              <div id="main" {style_hide}> 
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top" class="row1"> 
                    <td width="33%" valign="top"> 
                      <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                        <tr valign="top" class="row1"> 
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_username 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_password 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_email 
                            {/translate}
                            </b> </td>
                        </tr>
                        <tr valign="top"> 
                          <td width="33%"> 
                            <input type="text" name="account_admin_username"  value="{$account_admin.username}">
                          </td>
                          <td width="33%"> 
                            <input type="text" name="_password" >
                          </td>
                          <td width="33%"> 
                            <input type="text" name="account_admin_email"  value="{$account_admin.email}">
                           </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top" class="row1"> 
                    <td width="33%" valign="top"> 
                      <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                        <tr valign="top" class="row1"> 
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_first_name 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_middle_name 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_last_name 
                            {/translate}
                            </b> </td>
                        </tr>
                        <tr valign="top"> 
                          <td width="33%"> 
                            <input type="text" name="account_admin_first_name"  value="{$account_admin.first_name}">
                          </td>
                          <td width="33%"> 
                            <input type="text" name="account_admin_middle_name"  value="{$account_admin.middle_name}">
                          </td>
                          <td width="33%"> 
                            <input type="text" name="account_admin_last_name"  value="{$account_admin.last_name}">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top" class="row1"> 
                    <td width="33%" valign="top"> 
                      <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                        <tr valign="top" class="row1"> 
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_title 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_address1 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_address2 
                            {/translate}
                            </b> </td>
                        </tr>
                        <tr valign="top"> 
                          <td width="33%"> 
                            <select name="account_admin_title"  onChange="submit()">
                              <option value="Mr"{if $account_admin.title == "Mr"} selected{/if}> 
                              {translate module=account_admin}
                              mr 
                              {/translate}
                              </option>
							  <option value="Ms"{if $account_admin.title == "Ms"} selected{/if}> 
							  {translate module=account_admin}
							  ms
							  {/translate}
							  </option>								  
                              <option value="Mrs"{if $account_admin.title == "Mrs"} selected{/if}> 
                              {translate module=account_admin}
                              mrs 
                              {/translate}
                              </option>
                              <option value="Miss"{if $account_admin.title == "Miss"} selected{/if}> 
                              {translate module=account_admin}
                              miss 
                              {/translate}
                              </option>
                              <option value="Dr"{if $account_admin.title == "Dr"} selected{/if}> 
                              {translate module=account_admin}
                              dr 
                              {/translate}
                              </option>
                              <option value="Prof"{if $account_admin.title == "Prof"} selected{/if}> 
                              {translate module=account_admin}
                              prof 
                              {/translate}
                              </option>
                            </select>
                          </td>
                          <td width="33%"> 
                            <input type="text" name="account_admin_address1"  value="{$account_admin.address1}">
                          </td>
                          <td width="33%"> 
                            <input type="text" name="account_admin_address2"  value="{$account_admin.address2}">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top" class="row1"> 
                    <td width="33%" valign="top"> 
                      <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                        <tr valign="top" class="row1"> 
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_city 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_state 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_zip 
                            {/translate}
                            </b> </td>
                        </tr>
                        <tr valign="top"> 
                          <td width="33%"> 
                            <input type="text" name="account_admin_city"  value="{$account_admin.city}">
                          </td>
                          <td width="33%"> 
                            <input type="text" name="account_admin_state"  value="{$account_admin.state}">
                          </td>
                          <td width="33%"> 
                            <input type="text" name="account_admin_zip"  value="{$account_admin.zip}">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top" class="row1"> 
                    <td width="33%" valign="top"> 
                      <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                        <tr valign="top" class="row1"> 
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_country_id 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_status 
                            {/translate}
                            </b> </td>
                          <td width="33%"><b> 
                            {translate module=account_admin}
                            field_company 
                            {/translate}
                            </b></td>
                        </tr>
                        <tr valign="top"> 
                          <td width="33%"> 
                            { $list->menu("no", "account_admin_country_id", "country", "name", $account_admin.country_id, "\" onChange=\"submit()\"") }
                          </td>
                          <td width="33%"> 
                            { $list->bool("account_admin_status", $account_admin.status, "onChange=\"submit()\"") }
                          </td>
                          <td width="33%"> 
                            <input type="text" name="account_admin_company"  value="{$account_admin.company}">
                          </td>
                        </tr>
                      </table>
					  
					{ $method->exe_noauth("tax","get_tax_ids")} 
					{if $tax_ids}
					<script language="javascript">
					{if $VAR.account_country_id != ""}
					var countryId='{$VAR.account_country_id}';  
					{else}
					var countryId='{$smarty.const.DEFAULT_COUNTRY}'; 
					{/if} 
					{literal}
					function taxIdsDisplay(id) {    
						try{ document.getElementById('tax_country_id_'+id).style.display='block'; } catch(e) {} 
						try{ document.getElementById('tax_country_id_'+countryId).style.display='none'; } catch(e) {}
						countryId=id;
					}
					{/literal}
					</script>  					  
                      <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
						{foreach from=$tax_ids item=tax}  
						<tr valign="top" id="tax_country_id_{$tax.country_id}" {if $account_admin.country_id!=$tax.country_id}{style_hide}{/if}> 
						  <td width="33%"> 
							<strong>{$tax.tax_id_name}</strong>
						  </td>
						  <td width="67%"> 
							<input type="text" name="account_admin_tax_id[{$tax.country_id}]" value="{$account_admin.tax_id}" {if $account_admin_tax_id == true}class="form_field_error"{/if}> 
							<!-- {if $tax.tax_id_exempt}
							(or) exempt 
							<input type="checkbox" name="account_tax_id_exempt[{$tax.country_id}]" value="1" {if !$account_admin.tax_id}checked{/if}>
							{/if} -->
						  </td>
						</tr>
						{/foreach}
                      </table>					
					  {/if}
					    
                    </td>
                  </tr>
                </table>
              </div>
              <!-- OPTIONAL ACCOUNT DETAILS -->
              <div id="options" {style_hide}> 
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top" class="row1"> 
                    <td width="33%"> 
                      <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                        <tr valign="top" class="row1"> 
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_currency_id 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_language_id 
                            {/translate}
                            </b></td>
                          <td width="33%"> <b> 
                            {translate module=account_admin}
                            field_date_expire 
                            {/translate}
                            </b> </td>
                        </tr>
                        <tr valign="top"> 
                          <td width="33%"> 
                            { $list->menu("no", "account_admin_currency_id", "currency", "name", $account_admin.currency_id, "\" onChange=\"submit()\"") }
                          </td>
                          <td width="33%"><b> 
                            { $list->menu_files("", "account_admin_language_id", $account_admin.language_id, "language", "", "_core.xml", "\" onChange=\"document.getElementById('update_form').submit()") }
                            </b> </td>
                          <td width="33%"> 
                            { $list->calender_view("account_admin_date_expire", $account_admin.date_expire, "form_field", $account_admin.id) }
                          </td>
                        </tr>
                      </table> 
					  </td>
                  </tr>
                  <tr valign="top" class="row1"> 
                    <td width="33%"> 
                      <table width="100%" border="0" cellspacing="0" cellpadding="3">
                        <tr> 
                          <td width="33%" valign="top"> 
                            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                              <tr valign="top" class="row1"> 
                                <td width="33%"> <b> 
                                  {translate}
                                  authorized_groups 
                                  {/translate}
                                  </b></td>
                              </tr>
                              <tr valign="top"> 
                                <td width="33%"> 
                                  { $list->select_groups($account_admin.groups,"groups","form_field","10", $account_admin.own_account) }
                                  <b> </b> </td>
                              </tr>
                            </table>
                          </td>
                          <td width="66%" valign="top"> 
                            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                              <tr valign="top" class="row1"> 
                                <td width="50%"> <b> 
                                  {translate module=account_admin}
                                  field_email_html 
                                  {/translate}
                                  </b></td>
                                <td width="50%"> <b> 
                                  {translate module=account_admin}
                                  field_theme_id 
                                  {/translate}
                                  </b> </td>
                              </tr>
                              <tr valign="top"> 
                                <td width="50%"> 
                                  { $list->bool("account_admin_email_type", $account_admin.email_type, "onChange=\"submit()\"") }
                                </td>
                                <td width="50%"> 
                                  { $list->menu_files("", "account_admin_theme_id", $account_admin.theme_id, "theme", "", ".user_theme", "onChange=\"submit()\"") }
                                </td>
                              </tr>
                              <tr valign="top"> 
                                <td width="50%"><b> 
                                  {if $list->is_installed('affiliate') }
                                  {translate module=account_admin}
                                  field_affiliate_id 
                                  {/translate}
                                  {/if}
                                  </b></td>
                                <td width="50%"><b> 
                                  {if $list->is_installed('campaign') }
                                  {translate module=account_admin}
                                  field_campaign_id 
                                  {/translate}
                                  {/if}
                                  </b></td>
                              </tr>
                              <tr valign="top"> 
                                <td width="50%"> 
                                  {if $list->is_installed('affiliate') }
                                  {html_select_affiliate name="account_admin_affiliate_id" default=$account_admin.affiliate_id}
                                  {/if}
                                </td>
                                <td width="50%"> 
                                  {if $list->is_installed('campaign') }
                                  { $list->menu("no", "account_admin_campaign_id", "campaign", "name", $account_admin.campaign_id, "\" onChange=\"submit()\"", all) }
                                  {/if}
                                </td>
                              </tr>
                              <tr valign="top">
                                <td><b>{translate module=account_admin} field_invoice_delivery {/translate}</b></td>
                                <td><b>{translate module=account_admin} field_invoice_show_itemized {/translate}</b></td>
                              </tr>
                              <tr valign="top">
                                <td><select name="account_admin_invoice_delivery">
                                  <option value="0" {if $account_admin.invoice_delivery =="0"}selected{/if}>None</option>
                                  <option value="1" {if $account_admin.invoice_delivery =="1"}selected{/if}>E-mail</option>
                                  <option value="2" {if $account_admin.invoice_delivery =="2"}selected{/if}>Print</option>
                                </select></td>
                                <td><select name="account_admin_invoice_show_itemized">
                                  <option value="0" {if $account_admin.invoice_show_itemized =="0"}selected{/if}>Overview Only</option>
                                  <option value="1" {if $account_admin.invoice_show_itemized =="1"}selected{/if}>Full Detail</option>
                                </select></td>
                              </tr>
                              <tr valign="top">
                                <td><b>{translate module=account_admin} field_invoice_grace {/translate}</b></td>
                                <td><b>{translate module=account_admin} field_invoice_advance_gen{/translate}</b></td>
                              </tr>
                              <tr valign="top">
                                <td width="5"><input name="account_admin_invoice_grace" type="text"  value="{$account_admin.invoice_grace}" size="5"></td>
                                <td><input name="account_admin_invoice_advance_gen" type="text"  value="{$account_admin.invoice_advance_gen}" size="5"></td>
                              </tr>
                              <tr valign="top">
                                <td><b>{translate module=account_admin}field_parent_id{/translate}</b></td>
                                <td>&nbsp;</td>
                              </tr>   
                              <tr valign="top">
                                <td>{html_select_account name="account_admin_parent_id" default=$account_admin.parent_id}</td>
                                <td>&nbsp;</td>
                              </tr>
							  {if $account_admin.parent_id == 0 || $account_admin.parent_id == ''}
                              <tr valign="top">
                                <td><b>{translate module=account_admin}field_max_child{/translate}</b></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr valign="top">
                                <td><input name="account_admin_max_child" type="text"  value="{$account_admin.max_child}" size="5"></td>
                                <td>&nbsp;</td>
                              </tr> 
							  {/if}
                            </table>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </div>
              <!-- CUSTOM FIELDS   -->
              <div id="custom" {style_hide}> 
			  {if $account_admin.static_var}
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top" class="row1"> 
                    <td width="33%"> 
                      <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                        {foreach from=$account_admin.static_var item=record}
                        <tr valign="top"> 
                          <td width="33%" valign="top"> 
                            {$record.name}
                          </td>
                          <td width="67%"> 
                            {$record.html}
                          </td>
                        </tr>
                        {/foreach}
                      </table>
                    </td>
                  </tr>
                </table>
				{/if}
              </div>
              <!--NOTES & MEMOS FOR ACCOUNT -->
              <div id="notes" {style_hide}> 
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top" class="row1"> 
                    <td width="33%"> 
                      <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                        <tr valign="top"> 
                          <td width="67%" align="center"> 
                            <textarea name="account_admin_misc" cols="85" rows="10" {if $account_admin_misc == true}class="form_field_error"{/if}>{$account_admin.misc}</textarea>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </div> 
            </td>
          </tr>
          <tr valign="top">
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="6" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="10%" valign="top"> 
                    <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="80%" valign="top" align="center">
                    <input type="hidden" id="tab" name="tab" value="{$VAR.tab}">
                    <input type="hidden" name="_page" value="account_admin:view">
                    <input type="hidden" name="_page_current" value="account_admin:view">
                    <input type="hidden" name="account_admin_id" value="{$account_admin.id}">
                    <input type="hidden" name="do[]" value="account_admin:update">
                    <input type="hidden" name="id" value="{$VAR.id}">
                  </td>
                  <td width="10%" valign="top" align="right"> 
                    <input type="button" name="delete2" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$account_admin.id}','{$VAR.id}');">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          {if $account_admin.static_var}
          {/if}
        </table>
      </td>
    </tr>
  </table>
  </form>

<center>  
<iframe name="iframeAccount" id="iframeAccount" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
</center>
  
<script language=javascript>
{if $VAR.tab == ''}
showAccount('overview');
{else}
showAccount('{$VAR.tab}');
{/if}
</script>
{/foreach} 
{/if} 