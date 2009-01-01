{ $method->exe("invoice","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>		
    <script language="JavaScript"> 
        var module 		= 'invoice';
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
		
		function approveInvoice(id,status) 
		{
			if(status.value == '1') {
				document.location = '?_page=invoice:view&id='+id+'&do[]=invoice:approveInvoice';
			} else {
				document.location = '?_page=invoice:view&id='+id+'&do[]=invoice:voidInvoice';
			}
		} 
		
		function printView(invoice_id) {
			window.open('?_page=invoice:pdf&id='+invoice_id+'&_escape=1', 'pdf', ''); 
			document.forms.invoice_view.invoice_print_status.value = 1;
			document.forms.invoice_view.submit();
		}
		
		function approveInvoice(id,status,ids)  { 
			if(status == '1') {
				document.location = '?_page=invoice:view&id='+id+'&do[]=invoice:approveInvoice&ids='+ids;
			} else {
				document.location = '?_page=invoice:view&id='+id+'&do[]=invoice:voidInvoice&ids='+ids;
			}
		}				
		
		function hideAll() 
		{
			var elements = new Array(10);
			elements[0] = 'items';
			elements[1] = 'billing';
			elements[2] = 'affiliate'; 
			for(i=0;i<3;i++) document.getElementById(elements[i]).style.display='none'; 
			showIFrame('iframeInvoice',0,0,'themes/{/literal}{$THEME_NAME}{literal}/IEFrameWarningBypass.htm');
		}
				
		function accountJump(account_id,invoice_id,ids)
		{
			var action = document.getElementById('Jump').value; 
			if(action == 'become') {
				document.location = '{/literal}{$URL}{literal}?_page=account:account&id='+account_id+'&account_id='+account_id+'&do[]=account_admin:login';
 			} else if (action == 'email') { 
				hideAll();
				showIFrame('iframeInvoice',getPageWidth(600),350,'?_page=account_admin:iframe_mail_one&_escape=1&mail_account_id='+account_id);
			} else if (action == 'refresh') { 
				window.location.reload( false );
			} else if (action == 'bill_force') {
				window.location.href = '?_page=invoice:view&id='+invoice_id+'&invoice_id='+invoice_id+'&do[]=invoice:autobill&ids='+ids+'&area=billing';
			} else if (action == 'pdf') { 
				printView(invoice_id);
			} else if (action == 'refund') { 
				showRefund('0');
			} else if (action == 'reconcile') { 
				showReconcile('0');
			} else if (action == 'void') { 
				approveInvoice(invoice_id,0,ids)
			} else if (action == 'approve') { 
				approveInvoice(invoice_id,1,ids)
			} 
			document.forms.invoice_view.Jump.value='0';
		}
		
		function accountJumpView(account_id) {
			var module = document.getElementById('JumpView').value;
			document.forms.invoice_view.JumpView.value='0';
			var url = '?_page=core:search&module='+module+'&_next_page_one=view&'+module+'_account_id='+account_id; 
		    document.location = url;			
		} 
		
		function accountJumpAdd(account_id) {
			var module = document.getElementById('JumpAdd').value;
			document.forms.invoice_view.JumpAdd.value='0'; 
			var url = '?_page='+module+':add&'+module+'_account_id='+account_id; 
			document.location = url; 
		} 
		
		 function displayArea() 
		 { {/literal} 
			 {if $VAR.area == ""}
				document.getElementById('items').style.display='block';
			 {elseif $VAR.area == "billing"}
				document.getElementById('billing').style.display='block';
			 {elseif $VAR.area == "affiliate"}
				document.getElementById('affiliate').style.display='block'; 
			 {/if} 
		 {literal} } 	
    </script>
{/literal}
  
<!-- Loop through each record -->
{foreach from=$invoice item=invoice} <a name="{$invoice.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form id="invoice_view" name="invoice_view" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=invoice}
                title_view 
                {/translate}
                { $invoice.id }
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
                          <select name="Jump" id="Jump" onChange="accountJump('{$invoice.account_id}','{$invoice.id}','{$VAR.ids}')">
                            <option value="0"> 
                            {translate module=account_admin}
                            jump 
                            {/translate}
                            <option value="refresh"> 
                            {translate module=account_admin}
                            jump_refresh 
                            {/translate}
                            </option>
                            {if $smarty.const.SESS_ACCOUNT != $invoice.account_id }
                            <option value="become"> 
                            {translate module=account_admin}
                            jump_become 
                            {/translate}
                            </option>
                            {/if}
							
                            <option value="email"> 
                            {translate module=account_admin}
                            jump_email 
                            {/translate}
                            </option>
							
                            <option value="pdf"> 
                            {translate module=invoice}
                            jump_pdf 
                            {/translate}
                            </option>
							
                            {if $invoice.billing_status != 1}
                            <option value="bill_force"> 
                            {translate module=invoice}
                            jump_bill_force 
                            {/translate}
                            </option>
							{/if}
							
							{if $invoice.billed_amt > 0}
                            <option value="refund"> 
                            {translate module=invoice}
                            jump_refund
                            {/translate}
                            </option>							
							{/if}
							
							{if $invoice.billed_amt < $invoice.total_amt}
                            <option value="reconcile"> 
                            {translate module=invoice}
                            jump_reconcile
                            {/translate}
                            </option>							
							{/if}
							
							{if $invoice.process_status == 1}
                            <option value="void"> 
                            {translate module=invoice}
                            jump_void
                            {/translate}
                            </option>							
							{elseif $invoice.billing_status == 1}
                            <option value="approve"> 
                            {translate module=invoice}
                            jump_approve
                            {/translate}
                            </option>							
							{/if}
							 
                          </select>
                        </td>
                        <td width="33%"> 
                          <select name="JumpView" id="JumpView" onChange="accountJumpView('{$invoice.account_id}')">
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
                          <select name="JumpAdd" id="JumpAdd" onChange="accountJumpAdd('{$invoice.account_id}')">
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
                <a href="#" onClick="hideAll();document.getElementById('items').style.display='block';"> <font color="#FFFFFF"> 
                {translate module=invoice}
                title_items 
                {/translate}</font></a> | 
				
				<a href="#" onClick="hideAll();document.getElementById('billing').style.display='block'; "><font color="#FFFFFF"> 
                {translate module=invoice}
                title_billing
                {/translate}</font></a> | 
	 				
				<a href="#" onClick="hideAll();showServices()"> <font color="#FFFFFF"> 
                {translate module=invoice}
                title_service
                {/translate}</font></a> |
				
				{if $list->is_installed("affiliate") }
				<a href="#" onClick="hideAll();document.getElementById('affiliate').style.display='block';"> <font color="#FFFFFF"> 
                {translate module=invoice}
                title_affiliate
                {/translate}</font></a> |
				{/if}
				 				
				<a href="#" onClick="hideAll();showMemos({$service.id});"> 
                <font color="#FFFFFF"> 
                {translate module=invoice}
                title_memo 
                {/translate}</font></a> 
				
				
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1">
                <tr> 
                  <td valign="top"> 
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                      <tr> 
                        <td colspan="2"><b>
                          {translate module=invoice}
                          field_total_amt 
                          {/translate}
                          </b></td>
                        <td colspan="2"><b> 
                          {translate module=invoice}
                          field_billing_status 
                          {/translate}
                          </b></td>
                        <td colspan="2"><b>
                          {translate module=invoice}
                          field_billed_amt 
                          {/translate}
                          </b></td>
                      </tr>
                      <tr> 
                        <td width="4" height="20">&nbsp;</td>
                        <td width="411" height="20"><font color="#{if $invoice.billing_status == 1}000000{else}CC0000{/if}"> 
                          {$list->format_currency_num($invoice.total_amt, $invoice.billed_currency_id)}
                          </font> </td>
                        <td width="7" height="20">&nbsp;</td>
                        <td width="446" height="20"> 
                          {if $invoice.balance == 0}
                          {translate module=invoice}
                          paid 
                          {/translate}
                          {else}
                          {$list->format_currency_num($invoice.balance,$invoice.billed_currency_id)} 
                          ( <a href="javascript:showReconcile('{$invoice.balance}');"> 
                          {translate module=invoice}
                          jump_reconcile 
                          {/translate}
                          </a> )
                          {/if}
                        </td>
                        <td width="8" height="20">&nbsp;</td>
                        <td width="329" height="20"> 
                          {if $invoice.billed_amt > 0}
                          {$list->format_currency_num($invoice.billed_amt,$invoice.billed_currency_id)}
                          ( <a href="javascript:showRefund('{$invoice.billed_amt}');"> 
                          {translate module=invoice}
                          jump_refund 
                          {/translate}
                          </a> ) 
                          {elseif $invoice.total_amt > 0}
                          --- 
                          {/if}
                        </td>
                      </tr>
                      <tr> 
                        <td colspan="2"><b>
                          {translate module=service}
                          field_account_id 
                          {/translate}
                          </b></td>
                        <td colspan="2"><b>
                          {translate module=invoice}
                          field_due_date 
                          {/translate}
                          </b></td>
                        <td colspan="2"><b>
                          {translate module=invoice}
                          field_discount_amt 
                          {/translate}
                          </b></td>
                      </tr>
                      <tr valign="top"> 
                        <td width="4">&nbsp;</td>
                        <td width="411"> 
                          {html_select_account name="invoice_account_id" default=$invoice.account_id}
                        </td>
                        <td width="7">&nbsp;</td>
                        <td width="446">
                          {$list->date($invoice.due_date)}
                        </td>
                        <td width="8">&nbsp;</td>
                        <td width="329"> 
						  {if $invoice.discount_amt > 0}  
						  <div id="taxpanel1" style="display:none">
						  {$invoice.discount_popup}
						  </div> 
						  <div id="taxpanel">   
                          <a href="#" onclick="{literal} new Effect.Fade('taxpanel', {duration: 0} ); new Effect.Appear('taxpanel1', {duration: .5}); return false;" {/literal}>{$list->format_currency_num($invoice.discount_amt, $invoice.billed_currency_id)}</a> 
						  </div>
						  {else}---{/if}
						  </td>
                      </tr>
                      <tr> 
                        <td colspan="2"><b>
                          {translate module=invoice}
                          field_date_orig 
                          {/translate}
                          </b></td>
                        <td colspan="2"><b>
                          {translate module=invoice}
                          field_process_status 
                          {/translate}
                          </b></td>
                        <td colspan="2"><b>
                          {translate module=invoice}
                          field_tax_amt 
                          {/translate}
                          </b></td>
                      </tr>
                      <tr> 
                        <td width="4">&nbsp;</td>
                        <td width="411">
                          {$list->date_time($invoice.date_orig)}
                        </td>
                        <td width="7">&nbsp;</td>
                        <td width="446"> 
                          {if $invoice.process_status == 1}
                          Yes ( <a href="javascript:approveInvoice('{$invoice.id}',0);"> 
                          {translate module=invoice}
                          jump_void 
                          {/translate}
                          </a> ) 
                          {elseif $invoice.billing_status == 1}
                          No ( <a href="javascript:approveInvoice('{$invoice.id}',1);"> 
                          {translate module=invoice}
                          jump_approve 
                          {/translate}
                          </a> ) 
                          {else}
                          {translate module=invoice}
                          billing_pending 
                          {/translate}
                          {/if}
                        </td>
                        <td width="8">&nbsp;</td>
                        <td width="329">
						 {if $invoice.tax_amt > 0}
                          {$list->format_currency_num($invoice.tax_amt, $invoice.billed_currency_id)}
						  {else}---{/if}
                        </td>
                      </tr>
                      <tr> 
                        <td colspan="2"><b>
                          {translate module=invoice}
                          field_date_last 
                          {/translate}
                          </b></td>
                        <td colspan="2"><b>
                          {translate module=invoice}
                          field_print_status 
                          {/translate}
                          </b></td>
                        <td colspan="2"><b>
                          {translate module=invoice}
                          field_tax_id 
                          {/translate}
                          </b></td>
                      </tr>
                      <tr> 
                        <td width="4">&nbsp;</td>
                        <td width="411">
                          {$list->date_time($invoice.date_last)}
                          <input type="hidden" name="invoice_date_last" value="{$smarty.now}">
                        </td>
                        <td width="7">&nbsp;</td>
                        <td width="446"> 
                          {if $invoice.print_status == 1}
                          {translate}
                          true 
                          {/translate}
                          {else}
                          {translate}
                          false 
                          {/translate}
                          {/if}
                          <input type="hidden" name="invoice_print_status" value="{$invoice.print_status}">
                          ( <a href="javascript:printView('{$invoice.id}');">
						  {translate module=invoice}
						  jump_pdf 
						  {/translate}</a> ) 
                        </td>
                        <td width="8">&nbsp;</td>
                        <td width="329">
						 {if $invoice.tax_amt > 0}
						 {foreach from=$invoice.tax_arr item=taxz}
						  {$taxz.description} - {$list->format_currency_num($taxz.amount, $invoice.billed_currency_id)}<BR>
						 {/foreach} 
						 {else}---{/if} 
                        </td>
                      </tr>					  					  					  
                    </table> 
                  </td> 
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="6" cellpadding="1" class="body">
                <tr> 
                  <td width="5%"> 
                    <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="90%" valign="middle" align="center"> <a href="javascript:showMemos({$service.id});"></a> 
                  </td>
                  <td width="5%"> 
				  {if $invoice.billing_status==0 || $invoice.refund_status==1 || $invoice.total_amt==0 }
                    <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$invoice.id}','{$VAR.ids}');">
				  {/if}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  
  <div id="billing" {style_hide}>
  <br>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
      <tr> 
        <td> 
          <table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr valign="top" class="table_background"> 
              <td width="65%" class="table_heading"> 
                <center>
                  {translate module=invoice}
                  title_billing 
                  {/translate}
                </center>
              </td>
            </tr>
			
		  <!-- billing details -->
          {* show checkout/payment plugin details *}
          {if $invoice.checkout_plugin_id != '0'}
          {assign var=sql1 value=" AND id='"}
          {assign var=sql2 value="' "}
          {assign var=sql3 value=$invoice.checkout_plugin_id}
          {assign var=sql  value=$sql1$sql3$sql2}
          {if $list->smarty_array("checkout", "checkout_plugin", $sql, "checkout") }
          {assign var=checkout_plugin value=$checkout[0].checkout_plugin}  
              {assign var="ablock" 	value="checkout_plugin:plugin_inv_"}
              {assign var="blockfile" value="$ablock$checkout_plugin"}
              {$block->display($blockfile)} 
          {/if}
          {/if} 
		  <!-- end billing details -->
				  			
            <tr valign="top"> 
              <td width="65%" class="row1">
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">     
				  <tr valign="top" class="row1">  				  
                    <td width="141"> <b> 
                      {translate module=invoice}
                      field_notice_count 
                      {/translate}
                      </b> </td>
                    <td width="103" valign="middle"> 
                      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                        <tr> 
                          <td> 
                            { $invoice.notice_count }
                          </td>
                          <td><a href="?_page=invoice:view&account_id={$invoice.account_id}&id={$invoice.id}&ids={$VAR.ids}&do[]=invoice:resend"><img src="themes/{$THEME_NAME}/images/icons/mail_16.gif" border="0" width="16" height="16" alt="Resend Invoice"></a></td>
                        </tr>
                      </table>
                    </td>
                    <td width="287"> <b>
                      {translate module=invoice}
                      field_due_date 
                      {/translate}
                      </b> </td>
                    <td width="212"> <b>
                      { $list->calender_view("invoice_due_date", $invoice.due_date, "form_field", $invoice.id) }
                      </b> </td>
                  </tr>
                  <tr valign="top"> 
                    <td width="141"> <b> 
                      {translate module=invoice}
                      field_notice_max 
                      {/translate}
                      </b> </td>
                    <td width="103"> 
                      <input type="text" name="invoice_notice_max" value="{$invoice.notice_max}"  size="2">
                    </td>
                    <td width="287"> <b>
                      {translate module=invoice}
                      field_notice_next_date 
                      {/translate}
                      </b> </td>
                    <td width="212"><b>
                      { $list->calender_view("invoice_notice_next_date", $invoice.notice_next_date, "form_field", $invoice.id) }
                      </b> </td>
                  </tr>
                  <tr valign="top"> 
                    <td width="141"><b><a href="?_page=invoice:view&id={$invoice.id}&invoice_id={$invoice.id}&do%5B%5D=invoice:autobill&ids={$VAR.ids}"> 
                      </a>
                      {translate module=invoice}
                      field_grace_period 
                      {/translate}
                      </b></td>
                    <td width="103">
                      <input type="text" name="invoice_grace_period" value="{$invoice.grace_period}"  size="2">
                    </td>
                    <td width="287"><b> 
                      {translate module=invoice}
                      field_suspend_billing 
                      {/translate}
                      </b></td>
                    <td width="212"> 
                      { $list->bool("invoice_suspend_billing", $invoice.suspend_billing, " onChange=\"submit()\"")  }
                    </td>
                  </tr> 
                  <tr valign="top">
                    <td><b>{translate module=invoice}field_checkout_plugin_id{/translate}</b></td>
                    <td><a href="?_page=checkout:view&id={$invoice.checkout_plugin_id}">{$invoice.checkout_plugin}</a></td>
                    <td><b>IP Address </b></td>
                    <td>{$invoice.ip}</td>
                  </tr> 
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table> 
  </div>
  
  <div id="affiliate" {style_hide}>
  <br>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
      <tr> 
        <td> 
          <table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr valign="top" class="table_background"> 
              <td width="65%" class="table_heading"> 
                <center>
                  {translate module=invoice}
                  title_affiliate
                  {/translate}
                </center>
              </td>
            </tr>
            <tr valign="top"> 
              <td width="65%" class="row1">
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top" class="row1"> 
                    <td width="33%"> <b> 
                      {if $invoice.affiliate_id != ""}
                      {assign var=affiliate_details value=$invoice.affiliate_popup}
                      {translate module=invoice}
                      field_affiliate_id 
                      {/translate}
                      {else}
                      {translate module=invoice}
                      field_affiliate_id 
                      {/translate}
                      {/if}
                      </b></td>
                    <td width="33%"><b> 
                      {translate module=invoice}
                      affiliate_commissions 
                      {/translate}
                      </b></td>
                    <td width="33%"><b> 
                      {translate module=invoice}
                      field_campaign_id 
                      {/translate}
                      </b> </td>
                  </tr>
                  <tr valign="top" class="row1"> 
                    <td width="33%"> 
                      {html_select_affiliate name="invoice_affiliate_id" default=$invoice.affiliate_id}
                    </td>
                    <td width="33%"> 
                      {if $invoice.affiliate_commissions > 0}
                      {$list->format_currency($invoice.affiliate_commissions, '')}
                      {else}
                      ---- 
                      {/if}
                    </td>
                    <td width="33%"> 
                      {$list->menu("no", "invoice_campaign_id", "campaign", "name", $invoice.campaign_id, "\" onChange=\"submit()", all) }
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    
  </div>
  
  <div id="items" style="display:block">
  <br>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0"> 
          <tr valign="top" class="table_background"> 
            <td width="65%" class="table_heading"> 
              <center>
                  {translate module=invoice}
                  title_items 
                  {/translate}
                </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <!-- Loop through each invoice item record -->
              {foreach from=$cart item=cart}
              <br>
              {if $cart.item_type == "2"}
              <!-- Show domain -->
              <table width="97%" border="0" cellspacing="0" cellpadding="0" class="table_background" align="center">
                <tr> 
                  <td> 
                    <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
                      <tr> 
                        <td width="70%" class="row1" valign="top"> 
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                            <tr> 
                              <td width="67%" class="row1"><b> </b> 
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                                  <tr> 
                                    <td width="44%"><b> <u> 
                                      {$cart.domain_name|upper}
                                      . 
                                      {$cart.domain_tld|upper}
                                      </u> </b></td>
                                    <td width="44%">&nbsp;</td>
                                    <td width="12%" align="right"><a href="?_page=product:details&id={$cart_assoc.product_id}"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a><a href="?_page=product:details&id={$cart.product_id}"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a></td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            <tr> 
                              <td width="67%"> &nbsp;&nbsp;<b> </b> 
                                {if $cart.sku == "DOMAIN-REGISTER"}
                                {translate module=cart}
                                register 
                                {/translate}
                                {elseif $cart.sku == "DOMAIN-TRANSFER"}
                                {translate module=cart}
                                transfer 
                                {/translate}
                                {elseif $cart.sku == "DOMAIN-PARK"}
                                {translate module=cart}
                                park 
                                {/translate}
                                {elseif $cart.sku == "DOMAIN-RENEW"}
                                {translate module=cart}
                                renew 
                                {/translate}
                                {/if}                              </td>
                            </tr>
                            {if $cart.cart_type == "1"}
                            {if $cart.host_type == "ns_transfer"}
                            {/if}
                            {if $cart.host_type == "ip"}
                            {/if}
                            {/if}
                          </table>
                        </td>
                        <td width="30%" class="row1" valign="top" align="right"> 
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                            <tr> 
                              <td width="43%"> 
                                {translate module=cart}
                                base_price 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                <div id="def_base_price_{$cart_assoc.id}"> 
                                  <div id="def_base_price_{$cart.id}"> 
                                    {$list->format_currency($cart.price_base, $invoice.billed_currency_id)}
                                  </div>
                                  <div id="base_price_{$cart.id}"></div>
                                </div>
                                <div id="base_price_{$cart_assoc.id}"></div>
                              </td>
                            </tr>
                          </table>
                          {if $cart.sku != 'DOMAIN-PARK'}
                          <select id="quantity_{$cart.id}"  disabled name="select2">
                            <option value=""> 
                            {$cart.domain_term}
                            Year</option>
                          </select>
                          {/if}
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              {elseif $cart.item_type == "3"}
              <!-- Show ad-hoc -->
              <table width="97%" border="0" cellspacing="0" cellpadding="0" class="table_background" align="center">
                <tr> 
                  <td> 
                    <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
                      <tr> 
                        <td width="70%" class="row1" valign="top"> 
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                            <tr> 
                              <td width="67%" class="row1"><b> </b> 
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                                  <tr> 
                                    <td width="44%"><b> <u> 
                                      {$cart.product_name}
                                      </u> </b></td>
                                    <td width="44%" align="right">( 
                                      {$cart.sku}
                                      )</td>
                                    <td width="12%" align="right"><a href="?_page=product:details&id={$cart_assoc.product_id}"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a><a href="?_page=product:details&id={$cart.product_id}"> 
                                      </a><a href="javascript:deleteCart('{$cart.id}');"> 
                                      </a> 
                                      {if $cart.attribute_popup != ""}
									  <div id="attr_panell_1_{$cart.id}" style="display:none">
									  <a href="#" onclick=" new Effect.Fade('attr_panel_1_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Fade('attr_panell_1_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Appear('attr_panel_2_{$cart.id}', {literal}{duration: .5}{/literal}); 
															new Effect.Appear('attr_panell_2_{$cart.id}', {literal}{duration: .5}{/literal}); 
															return false;"> 
                                      <img src="themes/{$THEME_NAME}/images/icons/edit_16.gif" border="0" width="16" height="16"> 
                                      </a> 
									  </div> 
									  <div id="attr_panell_2_{$cart.id}" >
									  <a href="#" onclick=" new Effect.Fade('attr_panel_2_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									 						new Effect.Fade('attr_panell_2_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Appear('attr_panel_1_{$cart.id}', {literal}{duration: .5}{/literal}); 
															new Effect.Appear('attr_panell_1_{$cart.id}', {literal}{duration: .5}{/literal}); 
															return false;"> 
                                      <img src="themes/{$THEME_NAME}/images/icons/edit_16.gif" border="0" width="16" height="16"> 
                                      </a> 
									  </div>	
                                      {/if}
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            <tr> 
                              <td width="67%"> &nbsp;&nbsp;<b> </b> 
                                {translate module=cart}
                                price_type_one 
                                {/translate}
                              </td>
                            </tr>
                            {if $cart.cart_type == "1"}
                            {if $cart.host_type == "ns_transfer"}
                            {/if}
                            {if $cart.host_type == "ip"}
                            {/if}
                            {/if}
                          </table>
                        </td>
                        <td width="30%" class="row1" valign="top" align="right">  
						  <div id="attr_panel_1_{$cart.id}" style="display:none">
						  {$cart.attribute_popup}
						  </div>  
						  <div id="attr_panel_2_{$cart.id}">   
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                            <tr> 
                              <td width="43%"> 
                                {translate module=cart}
                                base_price 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                <div id="def_base_price_{$cart_assoc.id}"> 
                                  <div id="def_base_price_{$cart.id}"> 
                                    {$list->format_currency($cart.price_base, $invoice.billed_currency_id)}
                                  </div>
                                  <div id="base_price_{$cart.id}"></div>
                                </div>
                                <div id="base_price_{$cart_assoc.id}"></div>
                              </td>
                            </tr>
                            <tr> 
                              <td width="43%"> 
                                {translate module=cart}
                                quantity 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                {$cart.quantity}
                              </td>
                            </tr>
                          </table>
						  </div>
						  
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              {else}
              <!-- Show product -->
              <table width="97%" border="0" cellspacing="0" cellpadding="0" class="table_background" align="center">
                <tr> 
                  <td> 
                    <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
                      <tr> 
                        <td width="70%" class="row1" valign="top"> 
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                            <tr> 
                              <td width="67%" class="row1"><b> </b> 
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                                  <tr> 
                                    <td width="35%"><b> 
                                      {if $list->translate("product_translate","name", "product_id", $cart.product_id, "translate_product")}
                                      {/if}
                                      <u> 
                                      {$translate_product.name}
                                      </u> </b></td>
                                    <td width="51%" align="right">&nbsp; 
                                      {if $invoice.type == 1 || $cart.service_id > 0}
                                      <b><a href="?_page=service:view&id={$cart.service_id}"> 
                                      {$cart.service_id}
                                      </a></b> - 
                                      {/if}
                                      <b><a href="?_page=product:view&id={$cart.product_id}"> 
                                      {$cart.sku}
                                      </a></b> 
                                      {if $cart.domain_name != ""}
                                      (<a href="?_page=service:view&id={$cart.service_id}"> 
                                      {$cart.domain_name}
                                      . 
                                      {$cart.domain_tld}
                                      </a>) 
                                      {/if}
                                    </td>
                                    <td width="20" align="right"> 
                                      {if $cart.attribute_popup != ""} 
									  <div id="attr_panell_1_{$cart.id}" style="display:none">
									  <a href="#" onclick=" new Effect.Fade('attr_panel_1_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Fade('attr_panell_1_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Appear('attr_panel_2_{$cart.id}', {literal}{duration: .5}{/literal}); 
															new Effect.Appear('attr_panell_2_{$cart.id}', {literal}{duration: .5}{/literal}); 
															return false;"> 
                                      <img src="themes/{$THEME_NAME}/images/icons/edit_16.gif" border="0" width="16" height="16"> 
                                      </a> 
									  </div>
									  
									  <div id="attr_panell_2_{$cart.id}" >
									  <a href="#" onclick=" new Effect.Fade('attr_panel_2_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									 						new Effect.Fade('attr_panell_2_{$cart.id}', {literal}{duration: 0}{/literal} ); 
									  						new Effect.Appear('attr_panel_1_{$cart.id}', {literal}{duration: .5}{/literal}); 
															new Effect.Appear('attr_panell_1_{$cart.id}', {literal}{duration: .5}{/literal}); 
															return false;"> 
                                      <img src="themes/{$THEME_NAME}/images/icons/edit_16.gif" border="0" width="16" height="16"> 
                                      </a> 
									  </div>	 
                                      {/if}
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            <tr> 
                              <td width="67%"> &nbsp;&nbsp; 
                                {if $cart.range != ""}
                                {$cart.range}
                                {else}
                                {if $cart.price_type == "0"}
                                {translate module=cart}
                                price_type_one 
                                {/translate}
                                {/if}
                                {if $cart.price_type == "1"}
                                {translate module=cart}
                                price_type_recurr 
                                {/translate}
                                {/if}
                                {if $cart.price_type == "2"}
                                {translate module=cart}
                                price_type_trial 
                                {/translate}
                                {/if}
                                {/if}
                              </td>
                            </tr>
                            <tr> 
                              <td width="67%"> &nbsp;&nbsp; 
                                {if $cart.price_type == "1"}
                                {$list->format_currency($cart.price_base, $invoice.billed_currency_id)}
                                {if $cart.recurring_schedule == "0" }
                                {translate module=cart}
                                recurr_week 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "1" }
                                {translate module=cart}
                                recurr_month 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "2" }
                                {translate module=cart}
                                recurr_quarter 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "3" }
                                {translate module=cart}
                                recurr_semianual 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "4" }
                                {translate module=cart}
                                recurr_anual 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "5" }
                                {translate module=cart}
                                recurr_twoyear 
                                {/translate}
                                {/if}
                                {if $cart.recurring_schedule == "6" }
                                {translate module=cart}
                                recurr_threeyear 
                                {/translate}
                                {/if}
                                &nbsp;&nbsp; + &nbsp; 
                                {$list->format_currency($cart.price_setup, $invoice.billed_currency_id)}
                                {translate module=cart}
                                setup 
                                {/translate}
                                {/if}
                              </td>
                            </tr>
							{if $invoice.type != "1" && $cart.service_id > 0}
							<tr>
							  <td width="67%">&nbsp;&nbsp; 
								{translate module=cart service=$cart.service_id}
								service_upgrade 
								{/translate}
							  </td>
							</tr>
							{/if}							
                            {if $cart.item_type == "1"}
                            {if $cart.domain_type == "ns_transfer"}
                            <tr> 
                              <td width="67%">&nbsp;&nbsp; 
                                {translate module=cart}
                                host_type_domain 
                                {/translate}
                                - <u> 
                                {$cart.domain_name}
                                . 
                                {$cart.domain_tld}
                                </u> </td>
                            </tr>
                            {/if}
                            {if $cart.domain_type == "ip"}
                            <tr> 
                              <td width="67%">&nbsp;&nbsp; 
                                {translate module=cart}
                                host_type_ip 
                                {/translate}
                              </td>
                            </tr>
                            {/if}
                            {/if}
                          </table>
                        </td>
                        <td width="30%" class="row1" valign="top"> 
						  <div id="attr_panel_1_{$cart.id}" style="display:none">
						  {$cart.attribute_popup}
						  </div>  
						  <div id="attr_panel_2_{$cart.id}"> 						
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" class="row1">
                            <tr> 
                              <td width="43%"> 
                                {translate module=cart}
                                base_price 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                <div id="def_base_price_{$cart.id}"> 
                                  {$list->format_currency($cart.price_base, $invoice.billed_currency_id)}
                                </div>
                                <div id="base_price_{$cart.id}"></div>
                              </td>
                            </tr>
                            <tr> 
                              <td width="43%"> 
                                {translate module=cart}
                                setup_price 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                <div id="def_setup_price_{$cart.id}"> 
                                  {$list->format_currency($cart.price_setup, $invoice.billed_currency_id)}
                                </div>
                                <div id="setup_price_{$cart.id}"></div>
                              </td>
                            </tr>
                            <tr> 
                              <td width="43%"> 
                                {translate module=cart}
                                quantity 
                                {/translate}
                              </td>
                              <td width="57%" valign="middle" align="right"> 
                                {$cart.quantity}
                              </td>
                            </tr>
                          </table>
						  </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              {/if}
              {/foreach}
              <br>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </div>  
    <input type="hidden" name="_page" value="invoice:view">
    <input type="hidden" name="invoice_id" value="{$invoice.id}">
    <input type="hidden" name="do[]" value="invoice:update">
    <input type="hidden" name="id" value="{$VAR.id}">
    <input type="hidden" name="invoice_discount_arr" value="IGNORE-ARRAY-VALUE"> 
</form> 
{/foreach}
{/if}

<center>  
<iframe name="iframeInvoice" id="iframeInvoice" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
</center> 
  
{literal} 
<script language="JavaScript"> 
 	displayArea();  
	var invoice_id 	= {/literal}{$invoice.id}{literal};
	var account_id 	= '{/literal}{$invoice.account_id}{literal}';  
	   
	function showMemos() {
		showIFrame('iframeInvoice',getPageWidth(600),350,'?_page=core:search_iframe&module=invoice_memo&_escape=1&invoice_memo_invoice_id='+invoice_id+
				   '&_escape_next=1&_next_page_one=view&_next_page_none=add&name_id1=invoice_memo_invoice_id&val_id1='+invoice_id);
	}
	
	function showServices() { 
		showIFrame('iframeInvoice',getPageWidth(600),350,'?_page=core:search_iframe&module=service&_escape=1&service_invoice_id='+invoice_id+
				   '&_escape_next=1&_next_page=iframe_search_show&_next_page_none=none&name_id1=service_invoice_id&val_id1='+invoice_id);
	}
	
	function showReconcile(amt) { 
		showIFrame('iframeInvoice',getPageWidth(600),200,'?_page=invoice:reconcile&id='+invoice_id+'&_escape=1&amount='+amt);
	}
	
	function showRefund(amt) { 
		showIFrame('iframeInvoice',getPageWidth(600),200,'?_page=invoice:refund&id='+invoice_id+'&_escape=1&amount='+amt);
	} 
</script>
{/literal}
