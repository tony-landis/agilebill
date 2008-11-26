{ $method->exe("service","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'service';
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
		 
		function statusUpdate(obj) {
			var status = obj.value;
			var id = '{/literal}{$service.id}{literal}';
			document.location = '?_page=service:view&id='+id+'&service_status='+status+'&do[]=service:update&do[]=service:queue';
		}
		 
		function showService(element)
		{
			if(document.getElementById(element).style.display == 'none') hideAll();
			document.getElementById(element).style.display='block';
			document.getElementById('tab').value = element; 
		}	
		
		function hideAll() 
		{
			var elements = new Array(10);
			elements[0] = 'recurring';
			elements[1] = 'group';
			elements[2] = 'domain';
			elements[3] = 'hosting'; 
			elements[4] = 'product';  
			for(i=0;i<5;i++) document.getElementById(elements[i]).style.display='none'; 
			showIFrame('iframeService',0,0,'themes/{/literal}{$THEME_NAME}{literal}/IEFrameWarningBypass.htm');
		}
		
		function showMemos(service_id) {
			hideAll();
			showIFrame('iframeService',getPageWidth(600),350,'?_page=core:search_iframe&module=service_memo&_escape=1&service_memo_service_id='+service_id+
					   '&_escape_next=1&_next_page_one=view&_next_page_none=add&name_id1=service_memo_service_id&val_id1='+service_id);
		}
		
		function accountJump(account_id,service_id)
		{
			var action = document.getElementById('Jump').value;
			if(action == 'become') {
				document.location = '{/literal}{$URL}{literal}?_page=account:account&id='+account_id+'&account_id='+account_id+'&do[]=account_admin:login';
 			} else if (action == 'email') { 
				hideAll();
				showIFrame('iframeService',getPageWidth(600),350,'?_page=account_admin:iframe_mail_one&_escape=1&mail_account_id='+account_id);
			} else if (action == 'resend_email') {
				hideAll();
				showIFrame('iframeService',getPageWidth(600),350,'?_page=core:blank&do[]=service:resend_hosting_email&id='+service_id+'&account_id='+account_id);			
			} else if (action == 'force_queue') {
				hideAll(); 
				document.location = '?_page=service:view&do[]=service:queue&id='+service_id;
			} else if (action == 'refresh') { 
				window.location.href = '?_page=service:view&id='+service_id;
			} else if (action == 'modify') { 
				window.location.href = '?_page=service:modify&account_id='+account_id+'&service_id='+service_id;
			}   
			document.forms.service_view.Jump.value='0';
		}
		
		function accountJumpView(account_id) {
			var module = document.getElementById('JumpView').value;
			document.forms.service_view.JumpView.value='0';
			var url = '?_page=core:search&module='+module+'&_next_page_one=view&'+module+'_account_id='+account_id;
		    document.location = url;			
		} 
		
		function accountJumpAdd(account_id) {
			var module = document.getElementById('JumpAdd').value;
			document.forms.service_view.JumpAdd.value='0'; 
			var url = '?_page='+module+':add&'+module+'_account_id='+account_id; 
			document.location = url; 
		} 				 
    </script>
{/literal}

<!-- Loop through each record -->
{foreach from=$service item=service} <a name="{$service.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form id="service_view" name="service_view" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=service}
                title_view 
                {/translate}
                {$service.id}
              </center>
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
                          <select name="Jump" id="Jump" onChange="accountJump('{$service.account_id}','{$service.id}')">
                            <option value="0"> 
                            {translate module=account_admin}
                            jump 
                            {/translate}
                            <option value="refresh"> 
                            {translate module=account_admin}
                            jump_refresh 
                            {/translate}
                            </option>
                            {if $smarty.const.SESS_ACCOUNT != $service.account_id }
                            <option value="become"> 
                            {translate module=account_admin}
                            jump_become 
                            {/translate}
                            </option>
                            {/if}
							{if $service.type == 'host' || $service.type == 'host_group' } 
                            <option value="resend_email"> 
							{translate module=service}
							resend_email 
							{/translate}
                            </option> 
							{/if} 
							{if $service.recur_modify == 1} 
                            <option value="modify"> 
							{translate module=service}
							modify_service 
							{/translate}
                            </option> 
							{/if} 					
							{if $service.queue != "" && $service.queue != "none" } 
                            <option value="force_queue"> 
							{translate module=service}
							force_queue 
							{/translate}
                            </option> 
							{/if}							
                            <option value="email"> 
                            {translate module=account_admin}
                            jump_email 
                            {/translate}
                            </option> 
                          </select>
                        </td>
                        <td width="33%"> 
                          <select name="JumpView" id="JumpView" onChange="accountJumpView('{$service.account_id}')">
                            <option value="0"> 
                            {translate module=account_admin}
                            jump_view 
                            {/translate}
                            </option>  
                            <option value="invoice"> 
                            {translate module=account_admin}
                            jump_invoices 
                            {/translate}
                            </option> 
                            <option value="service"> 
                            {translate module=account_admin}
                            jump_services 
                            {/translate}
                            </option> 
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
                          </select>
                        </td>
                        <td width="33%" align="right"> 
                          <select name="JumpAdd" id="JumpAdd" onChange="accountJumpAdd('{$service.account_id}')">
                            <option value="0"> 
                            {translate module=account_admin}
                            jump_add 
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
                            </option>
							{/if}
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
              <center>
                {if $service.date_next_invoice > 0}
                <a href="javascript:showService('recurring');"> <font color="#FFFFFF"> 
                {translate module=service}
                title_recurring 
                {/translate}
                </font></a> | 
                {/if}
                {if $service.type == 'group' || $service.type == 'host_group' || $service.type == 'product_group' }
                <a href="javascript:showService('group');"> <font color="#FFFFFF"> 
                {translate module=service}
                title_group 
                {/translate}
                </font> </a> | 
                {/if}
                <a href="javascript:showService('domain');"> 
                {if $service.type == 'domain' }
                <font color="#FFFFFF"> 
                {translate module=service}
                title_domain 
                {/translate}
                </font></a> | 
                {/if}
                <a href="javascript:showService('hosting');"> <font color="#FFFFFF"> 
                {if $service.type == 'host' || $service.type == 'host_group' }
                {translate module=service}
                title_hosting 
                {/translate}
                </font></a> | 
                {/if}
                <a href="javascript:showService('product');"> <font color="#FFFFFF"> 
                {if $service.type == 'product' || $service.type == 'product_group' }
                {translate module=service}
                title_product 
                {/translate}
                </font></a> | 
                {/if}
                <a href="javascript:showMemos({$service.id});"> <font color="#FFFFFF"> 
                {translate module=service}
                title_memo 
                {/translate}
                </font> </a> 
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1">
                <tr> 
                  <td valign="top"> 
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                      <tr valign="top" class="row1"> 
                        <td colspan="2"> <b> 
                          {translate module=service}
                          field_account_id 
                          {/translate}
                          </b> </td>
                        <td colspan="2"> <b><a href="?_page=invoice:view&id={$service.invoice_id}"> 
                          </a> 
                          {translate module=service}
                          field_sku 
                          {/translate}
                          </b> </td>
                      </tr>
                      <tr valign="top" class="row1"> 
                        <td width="5">&nbsp;</td>
                        <td width="435"> 
                          {html_select_account name="service_account_id" default=$service.account_id}
                        </td>
                        <td width="5">&nbsp;</td>
                        <td width="439">                            
                            <input type="text" name="service_sku" value="{$service.sku}"> 
                        </td>
                      </tr>
                      <tr valign="top" class="row1"> 
                        <td colspan="2" height="20"><b> 
                          {translate module=service}
                          field_date_orig 
                          {/translate}
                          </b></td>
                        <td colspan="2" height="20"><b> 
                          {translate module=service}
                          field_active 
                          {/translate}
                          </b></td>
                      </tr>
                      <tr valign="top" class="row1"> 
                        <td width="5">&nbsp;</td>
                        <td width="435"> 
                          {$list->date_time($service.date_orig)}
                        </td>
                        <td>&nbsp; </td>
                        <td>                            
                          { $list->bool("service_active", $service.active, "\" onChange=\"submit()\"") }							
                        </td>
                      </tr>
                      <tr valign="top" class="row1"> 
                        <td colspan="2"><b> 
                          {translate module=service}
                          field_date_last 
                          {/translate}
                          </b></td>
                        <td colspan="2"><b> 
                          {translate module=service}
                          field_queue 
                          {/translate}
                          </b></td>
                      </tr>
                      <tr valign="top" class="row1"> 
                        <td width="5">&nbsp;</td>
                        <td width="435"> 
                          {$list->date_time($service.date_last)}
                          <input type="hidden" name="service_date_last" value="{$smarty.now}">
						  </td>
                        <td><b><a href="?_page=invoice:view&id={$service.invoice_id}"> 
                          </a></b></td>
                        <td> 
                          <select name="service_queue" onChange="document.getElementById('queue_force').value='1'; submit();">
                            <option value="none" {if $service.queue =="none"}selected{/if}>{translate module=service}queue_none{/translate}</option>
                            <option value="new" {if $service.queue =="new"}selected{/if}>{translate module=service}new{/translate}</option>
                            <option value="active" {if $service.queue =="active"}selected{/if}>{translate module=service}active{/translate}</option>
                            <option value="inactive" {if $service.queue =="inactive"}selected{/if}>{translate module=service}inactive{/translate}</option>
                            <option value="delete" {if $service.queue =="delete"}selected{/if}>{translate module=service}delete{/translate}</option>
                          </select>
                          <input type="hidden" id="queue_force" name="queue_force" value="0">
                        </td>
                      </tr>
                      {if $service.prod_plugin_name eq "VOIP" || $service.prod_plugin_name eq "PREPAID"}
                      <tr valign="top" class="row1"> 
                        <td colspan="2"><b> 
                          {translate module=service}
                          field_service_did 
                          {/translate}
                          </b></td>
                        <td colspan="2">&nbsp;</td>
                      </tr>
                      <tr valign="top" class="row1">
                        <td width="5">&nbsp;</td> 
                        <td width="435"><a href="?_page=voip_did:view&id={voip_did_id service_id=$service.id}">{voip_did service_id=$service.id}</a></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>    
                      {/if}                    
                    </table>
                  </td>
				   
				  <!-- Invoice History for this service -->
				  {if $service.invoice}
                  <td width="40%" valign="top"> 
                    <table width="100%" border="0" cellspacing="4" cellpadding="0" class="body">
                      <tr> 
                        <td colspan="5"><b> 
                          {translate module=account_admin}
                          invoice_overview 
                          {/translate}
                          </b></td>
                      </tr>
                      {foreach from=$service.invoice item=invoice}
                      <tr valign="middle"> 
                        <td width="6">&nbsp; </td>
                        <td width="24"> 
                          {if $invoice.process_status == "1"}
                          <img src="themes/{$THEME_NAME}/images/icons/go_16.gif" border="0"> 
                          {else}
                          <img src="themes/{$THEME_NAME}/images/icons/stop_16.gif" border="0"> 
                          {/if}
                        </td>
                        <td width="50"><a href="?_page=invoice:view&id={$invoice.id}"> 
                          {$invoice.id}
                          </a> </td>
                        <td width="80" align="right"> 
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
                        <td width="80" align="right"> 
                          {if $invoice.due > 0}
                          <font color="#CC0000"> <b> 
                          {$list->format_currency_num($invoice.due, '')}
                          </b> </font> 
                          {else}
                          <font color="#999999"> 
                          {$list->format_currency_num('0', '')}
                          </font> 
                          {/if}
                          &nbsp;&nbsp;&nbsp;&nbsp; </td>
                      </tr>
					  {/foreach}
                      <tr valign="middle"> 
                        <td colspan="5"> <a href="?_page=core:search&module=invoice&join_service_id={$service.id}"> 
                          {translate module=service service=$service.id}invoice_history{/translate}</a> 
                        </td>
                      </tr> 
                    </table> 
                    </td>
				  {/if} 
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="6" cellpadding="1" class="body">
                <tr> 
                  <td width="5%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="90%" valign="middle" align="center"> <a href="javascript:showMemos({$service.id});"></a></td>
                  <td width="5%"> 
                    <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$service.id}','{$VAR.id}');">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
   
  <div id="recurring" {style_hide}>  
  {if $service.date_next_invoice > 0}
  <br>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=service}
                title_recurring 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
                <table width="100%" border="0" cellspacing="5" cellpadding="1" class="row1">
                  <tr class="row1"> 
                    <td width="50%" valign="top"> <b> 
                      {translate module=service}
                      field_date_last_invoice 
                      {/translate}
                      </b></td>
                    <td width="50%" valign="top"> <b> 
                      {translate module=service}
                      field_date_next_invoice 
                      {/translate}
                      </b></td>
                  </tr>
                  <tr> 
                    <td width="50%" valign="top"> 
                      { $list->calender_view("service_date_last_invoice", $service.date_last_invoice, "form_field", $service.id) }
                    </td>
                    <td width="50%" valign="top"> 
                      { $list->calender_view("service_date_next_invoice", $service.date_next_invoice, "form_field", $service.id) }
                    </td>
                  </tr>
                  <tr class="row1"> 
                    <td width="50%" valign="top"> <b> 
                      {translate module=service}
                      field_recur_schedule 
                      {/translate}
                      </b></td>
                    <td width="50%" valign="top"> <b> 
                      {translate module=service}
                      field_taxable 
                      {/translate}
                      </b></td>
                  </tr>
                  <tr> 
                    <td width="50%" valign="top"> 
                      {if $recur_price}
                      <select name="service_recur_schedule" onchange="document.location='?_page=service:view&id={$service.id}&do[]=service:admin_changeschedule&service_recur_schedule='+this.value">
                        {foreach from=$recur_price item=price_recurr key=key}
                        <option value="{$key}" {if $service.recur_schedule == $key} selected{/if}> 
                        {$list->format_currency($price_recurr.base, $smarty.const.SESS_CURRENCY)}
                        &nbsp;&nbsp; 
                        {if $key == "0" }
                        {translate module=cart}
                        recurr_week 
                        {/translate}
                        {/if}
                        {if $key == "1" }
                        {translate module=cart}
                        recurr_month 
                        {/translate}
                        {/if}
                        {if $key == "2" }
                        {translate module=cart}
                        recurr_quarter 
                        {/translate}
                        {/if}
                        {if $key == "3" }
                        {translate module=cart}
                        recurr_semianual 
                        {/translate}
                        {/if}
                        {if $key == "4" }
                        {translate module=cart}
                        recurr_anual 
                        {/translate}
                        {/if}
                        {if $key == "5" }
                        {translate module=cart}
                        recurr_twoyear 
                        {/translate}
                        {/if}
                        {if $key == "6" }
                        {translate module=cart}
                        recurr_threeyear 
                        {/translate}
                        {/if}
                        </option>
                        {/foreach}
                      </select>
                      {else}
                      <select name="service_recur_schedule" onchange="document.location='?_page=service:view&id={$service.id}&do[]=service:admin_changeschedule&service_recur_schedule='+this.value">
                        <option value="0" {if $service.recur_schedule == "0"} selected{/if}> 
                        {translate module=product}
                        recurr_week 
                        {/translate}
                        </option>
                        <option value="1" {if $service.recur_schedule == "1"} selected{/if}> 
                        {translate module=product}
                        recurr_month 
                        {/translate}
                        </option>
                        <option value="2" {if $service.recur_schedule == "2"} selected{/if}> 
                        {translate module=product}
                        recurr_quarter 
                        {/translate}
                        </option>
                        <option value="3" {if $service.recur_schedule == "3"} selected{/if}> 
                        {translate module=product}
                        recurr_semianual 
                        {/translate}
                        </option>
                        <option value="4" {if $service.recur_schedule == "4"} selected{/if}> 
                        {translate module=product}
                        recurr_anual 
                        {/translate}
                        </option>
                        <option value="5" {if $service.recur_schedule == "5"} selected{/if}> 
                        {translate module=product}
                        recurr_twoyear 
                        {/translate}
                        </option>
                        <option value="6" {if $service.recur_schedule == "6"} selected{/if}> 
                        {translate module=product}
                        recurr_threeyear 
                        {/translate}
                        </option>
                      </select>
                      {/if}
                    </td>
                    <td width="50%" valign="top"> 
                      { $list->bool("service_taxable", $service.taxable, "\" onChange=\"document.getElementById('service_view').submit()\"") }
                    </td>
                  </tr>
                  <tr> 
                    <td width="50%" valign="top"> <b> 
                      {translate module=service}
                      field_price 
                      {/translate}
                      </b></td>
                    <td width="50%" valign="top"><b> 
                      {translate module=service}
                      field_recur_type 
                      {/translate}
                      </b> </td>
                  </tr>
                  <tr> 
                    <td width="50%" valign="top"> 
                      <input type="text" name="service_price" value="{$service.price}"  size="5">
                      {$list->currency_iso("")}
                    </td>
                    <td width="50%" valign="top"> 
                      {if $service.recur_type == 0 }
                      {translate module=product}
                      recurr_type_aniv 
                      {/translate}
                      {else}
                      {translate module=product}
                      recurr_type_fixed 
                      {/translate}
                      {/if}
                    </td>
                  </tr>
                  {if $service.recur_type == 1 }
                  <tr> 
                    <td width="50%" valign="top"><b> 
                      {translate module=service}
                      field_recur_weekday 
                      {/translate}
                      </b></td>
                    <td width="50%" valign="top"> 
                      <input type="text" name="service_recur_weekday" value="{$service.recur_weekday}"  size="2" maxlength="2">
                      (1-28) </td>
                  </tr>
                  {/if}
                  <tr> 
                    <td width="50%" valign="top"><b> 
                      {translate module=service}
                      field_recur_schedule_change 
                      {/translate}
                      </b></td>
                    <td width="50%"> <a href="?_page=service:view&id={$service.id}&do%5B%5D=service:cancelservice"> 
                      </a> <b> 
                      { $list->bool("service_recur_schedule_change", $service.recur_schedule_change, "\" onChange=\"document.getElementById('service_view').submit()\"") }
                      </b> </td>
                  </tr>
                  <tr> 
                    <td width="50%" valign="top"><b>
                      {translate module=service}
                      field_recur_cancel 
                      {/translate}
                      </b></td>
                    <td width="50%" valign="top"> 
                      { $list->bool("service_recur_cancel", $service.recur_cancel, "\" onChange=\"document.getElementById('service_view').submit()\"") }
                    </td>
                  </tr> 
                  <tr> 
                    <td width="50%" valign="top"><b> 
                      {translate module=service}
                      field_recur_modify 
                      {/translate}
                      </b></td>
                    <td width="50%" valign="top">
                      { $list->bool("service_recur_modify", $service.recur_modify, "\" onChange=\"document.getElementById('service_view').submit()\"") }
                    </td>
                  </tr>
                  <tr> 
                    <td width="50%" valign="top"> 
                   	 <b> 
					 {translate module=service}
                      field_suspend_billing 
                      {/translate}</b>
                    </td>
                    <td width="50%" valign="top">  
                      { $list->bool("service_suspend_billing", $service.suspend_billing, "\" onChange=\"document.getElementById('service_view').submit()\"")  }                      
                    </td>
                  </tr>  
				  
				  {if $service.account_billing_id > 0}
                  <tr>
                    <td width="50%" valign="top"><b>  
                      {translate module=service}
                      field_account_billing_id 
                      {/translate}
                      </b></td>
                    <td width="50%" valign="top"> 
                      { $list->menu_cc_admin("service_account_billing_id", $service.account_id, $service.account_billing_id, "form_menu") }
                    </td>
                  </tr> 
				  {/if}
				  
				  
                </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  {/if}
  </div>
  
  <div id="group" {style_hide}>  
  {if $service.type == 'group' || $service.type == 'host_group' || $service.type == 'product_group' }
  <br>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=service}
                title_group
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="3" class="row1">
                <tr> 
                  <td width="98%" valign="top"> <b> </b> 
                    <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                      <tr> 
                        <td> 
                          <p> 
                            <input type="radio" name="service_group_type" value="0" {if $service.group_type == "0"}checked{/if}>
                            {translate module=product}
                            assoc_group_limited 
                            {/translate}
                            <input type="text" name="service_group_days" value="{$service.group_days}"  size="3">
                            <br>
                            <input type="radio" name="service_group_type" value="1" {if $service.group_type == "1"}checked{/if}>
                            {translate module=product}
                            assoc_group_subscription 
                            {/translate}
                            <br>
                            <input type="radio" name="service_group_type" value="2" {if $service.group_type == "2"}checked{/if}>
                            {translate module=product}
                            assoc_group_forever 
                            {/translate}
                          </p>
                        </td>
                      </tr>
                    </table>
                    </td>
                  <td width="2%" align="left" valign="top"> 
                    { $list->menu_multi($service.group_grant, "service_group_grant", "group", "name", "10", "", "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  {/if}
  </div>
  
  <div id="domain" {style_hide}>  
  {if $service.type == 'domain' }
  <br>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=service}
                title_domain 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row1"> 
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_domain_name 
                    {/translate}
                    </b></td>
                  <td width="40%"> <b> 
                    {translate module=service}
                    field_domain_term 
                    {/translate}
                    </b></td>
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_domain_date_expire 
                    {/translate}
                    </b></td>
                </tr>
                <tr valign="top"> 
                  <td width="30%"> 
                    { $service.domain_name|upper }.{ $service.domain_tld|upper }
                    <b>&nbsp; <a href="?_page=core:search&module=service&service_domain_name={$service.domain_name}&service_domain_tld={$service.domain_tld}"><img src="themes/{$THEME_NAME}/images/icons/zoomi_16.gif" border="0" width="16" height="16" alt="Resend Invoice"></a> 
                    </b> </td>
                  <td width="40%"> 
                    { $service.domain_term }
                    Year(s) <a href="?_page=service:view&id={$service.id}&do%5B%5D=invoice:generatedomaininvoice">
                    {translate module=cart}
                    renew
                    {/translate} 
                    </a>				  </td>
                  <td width="30%"> 
                    { $list->calender_view("service_domain_date_expire", $service.domain_date_expire, "form_field", $service.id) }
                  </td>
                </tr>
                <tr valign="top" class="row1"> 
                  <td width="30%" height="22"> <b> 
                    {translate module=service}
                    field_domain_type 
                    {/translate}
                    </b></td>
                  <td width="40%" height="22"> <b> 
                    {translate module=service}
                    field_domain_host_registrar_id 
                    {/translate}
                    </b></td>
                  <td width="30%" height="22"> <b> 
                    {translate module=service}
                    field_domain_host_tld_id 
                    {/translate}
                    </b></td>
                </tr>
                <tr valign="top"> 
                  <td width="30%" height="27"> 
                   {translate module=cart}{$service.domain_type}{/translate} 
                  </td>
                  <td width="40%" height="27"> 
                    { $list->menu("", "service_domain_host_registrar_id", "host_registrar_plugin", "name", $service.domain_host_registrar_id, "form_menu") }
                  </td>
                  <td width="30%" height="27"> 
                    { $list->menu("", "service_domain_host_tld_id", "host_tld", "name", $service.domain_host_tld_id, "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  {/if}
  </div>
  
  <div id="hosting" {style_hide}>  
  {if $service.type == 'host' || $service.type == 'host_group' }
  <br>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=service}
                title_hosting
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top"> 
                    <td width="50%"> 
                      {translate module=service}
                      field_domain_name 
                      {/translate}
                    </td>
                    <td width="50%"> <b> 
                      { $service.domain_name|upper }
                      . 
                      { $service.domain_tld|upper }
                      &nbsp; <a href="?_page=core:search&module=service&service_domain_name={$service.domain_name}&service_domain_tld={$service.domain_tld}"><img src="themes/{$THEME_NAME}/images/icons/zoomi_16.gif" border="0" width="16" height="16" alt="Resend Invoice"></a> 
                      </b> </td>
                  </tr>
                  <tr valign="top"> 
                    <td width="50%"> 
                      {translate module=service}
                      field_host_server_id 
                      {/translate}
                    </td>
                    <td width="50%"> 
                      { $list->menu("", "disabled", "host_server", "name", $service.host_server_id, "\" disabled") }
                    </td>
                  </tr>
                  { if $service.host_ip != "" }
                  <tr valign="top"> 
                    <td width="50%"> 
                      {translate module=service}
                      field_host_ip 
                      {/translate}
                    </td>
                    <td width="50%"> 
                      { $service.host_ip }
                    </td>
                  </tr>
                  {/if}
                  <tr valign="top"> 
                    <td width="50%"> 
                      {translate module=service}
                      field_host_username 
                      {/translate}
                    </td>
                    <td width="50%"> 
                      <input type="text" name="service_host_username" value="{$service.host_username}"  size="24">
                    </td>
                  </tr>
                  <tr valign="top"> 
                    <td width="50%"> 
                      {translate module=service}
                      field_host_password 
                      {/translate}
                    </td>
                    <td width="50%"> 
                      <input type="text" name="service_host_password" value="{$service.host_password}"  size="24">
                    </td>
                  </tr>
                </table>
            </td>
          </tr>
          <tr valign="top">
            <td width="65%" class="row1">
			   {if ($list->smarty_array("host_server","provision_plugin", "", "plugin")) } 
					{foreach from=$plugin item=arr} 
						{if $service.host_server_id == $arr.id}
			 			  {assign var="product" value=$service}
						  {assign var="afile" 	value=$arr.provision_plugin}
						  {assign var="ablock" 	value="host_provision_plugin:plugin_prod_"}
						  {assign var="blockfile" value="$ablock$afile"}
						  { $block->display($blockfile) } 
						{/if}
					 {/foreach}        						
				 {/if} 			
			</td>
          </tr>
        </table>
      </td>
    </tr>
  </table> 
  {/if}
  </div>
  
  <div id="product" {style_hide}>  
  {if $service.type == 'product' || $service.type == 'product_group' }
  <br>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                  {translate module=service}
                  title_product 
                  {/translate}
                </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top"> 
                    <td width="50%"> 
                      {translate module=service}
                      product_plugin 
                      {/translate}
                    </td>
                    <td width="50%"> <b> 
                      { $service.prod_plugin_name }
                      </b> </td>
                </tr>
                { if $service.host_ip != "" }
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">  
              {assign var="product" value=$service}
              {assign var="afile" 	value=$service.prod_plugin_name}
              {assign var="ablock" 	value="product_plugin:plugin_prod_"}
              {assign var="blockfile" value="$ablock$afile"}
              { $block->display($blockfile) } 
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  {/if}
  </div>  
  	<input type="hidden" name="_page" value="service:view">
   	<input type="hidden" name="service_id" value="{$service.id}">
	<input type="hidden" name="service_account_id" value="{$service.account_id}">
    <input type="hidden" name="do[]" value="service:update">
    <input type="hidden" name="id" value="{$VAR.id}">
    <input type="hidden" id="tab" name="tab" value="{$VAR.tab}">
  	<input type="hidden" name="service_type" value="{$service.type}">
  	<input type="hidden" name="service_price_type" value="{$service.price_type}">
</form>
<center>  
<iframe name="iframeService" id="iframeService" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
</center>
  
<script language=javascript>
{if $VAR.tab == ''}
	{if $service.date_next_invoice > 0}
		showService('recurring');
	{elseif $service.type == 'group' || $service.type == 'host_group' || $service.type == 'product_group' }
		showService('group');
	{elseif $service.type == 'host' || $service.type == 'host_group' }
		showService('hosting');
	{elseif $service.type == 'product' || $service.type == 'product_group' }
		showService('product');
	{elseif $service.type == 'domain' }
		showService('domain');
	{/if}
{else}
showService('{$VAR.tab}');
{/if}
</script>  
{/foreach}
{/if}
