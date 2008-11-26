{ $method->exe("ticket","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'ticket';
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
		
		function refreshOpener() {
			try{ window.opener.refreshTicketSearch()} catch(e) {}
    	}
		 
    	function delete_record(id,ids)
    	{				
    		temp = window.confirm("{/literal}{translate}alert_delete{/translate}{literal}");
    		if(temp == false) return;
    		
    		var replace_id = id + ",";
    		ids = ids.replace(replace_id, '');		
    		if(ids == '') {
    			var url = '?_page=core:search&module=' + module + '&do[]=' + module + ':delete&delete_id=' + id;
    			window.location = url;
    			return;
    		} else {
    			var page = 'view&id=' +ids;
    		}				
    		
    		var doit = 'delete';
    		var url = '?_page='+ module +':'+ page +'&do[]=' + module + ':' + doit + '&delete_id=' + id;
    		window.location = url;	
    	}
		
		function delete_quick(id) {		
			try{window.opener.TicketDelete(id);	}catch(e){} 
    		try{window.close();	}catch(e){}
		}
		
		function delete_message(id,ids)
		{
			temp = window.confirm("{/literal}{translate}alert_delete{/translate}{literal}");
			if(temp == false) return; 
    		var url = '?_page=ticket:view&id=' + ids + '&do[]=ticket_message:delete&delete_id=' + id;
    		document.location = url;
    		return; 	
		}			
		
function swapMsgStatus(i) {
	// get current state:
	if($('msgbody_'+i).style.display=='block') {
		$('msgtitleOn_'+i).style.display='block';
		$('msgtitleOff_'+i).style.display='none';
		$('msgoptionsOn_'+i).style.display='block';
		$('msgoptionsOff_'+i).style.display='none';		
		$('msgbody_'+i).style.display='none';
	} else {
		$('msgtitleOn_'+i).style.display='none';
		$('msgtitleOff_'+i).style.display='block';
		$('msgoptionsOn_'+i).style.display='none';
		$('msgoptionsOff_'+i).style.display='block';				
		$('msgbody_'+i).style.display='block';	
	} 
}

function addFaq() {
	// set page & faq value
	if( $('add_faq').checked == true ) {
		$('page2').value = 'faq:add';
		$('faq_answer').value = $('ticket_reply').value;
	 } else {
	 	$('page2').value = '{/literal}{$VAR._page}{literal}';
		$('faq_answer').value = $('ticket_reply').value;
	 }
}
		
    //  END -->
    </script>
	 
<style>  
div.DialogBody table { font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; cellpadding:0; cellspacing:0; width:100%; }
 
div.details {     
  background-color:white;  
  padding:5px; 
  border:2px solid #C5DEA1;  
}
  
div.msgtitleStaff {      
  padding:3px;  
  background-color: #ECF3E1;
  border:1px solid #C5DEA1; 
  border-bottom: 0px solid #FFF;
  width:100%;
}

div.msgbodyStaff {      
  padding:3px; 
  background-color: #ECF3E1;
  border:1px solid #C5DEA1;
  border-bottom: 0px solid #FFF;
  border-top: 0px solid #FFF;
  width:100%;
}

div.msgtitleUser {      
  padding:3px;  
  border:1px solid #C5DEA1; 
  border-bottom: 0px solid #FFF;
  width:100%;
}

div.msgbodyUser {      
  padding:3px; 
  border:1px solid #C5DEA1;  
  border-bottom: 0px solid #FFF;
  border-top: 0px solid #FFF;
  width:100%;
}

div.msgtitleStaff table { font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; cellpadding:0; cellspacing:0; width:100%; }
div.msgtitleUser table { font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; cellpadding:0; cellspacing:0; width:100%; }
 
 
div.msgoptions { position:float; 
  text-align:right; 
}
 
div.DialogHeader {
	color: #fff;
	text-align:center; 
	background-color: #333333; 
	border: 1px solid #666666;  
	padding:3px; 
	width: 100%;
} 

div.DialogBody {
	color: #333;
	padding:3px;
	background-color:#fcfcfc; 
	border: 1px solid #666666;  
	width: 100%;
}

div.DialogAction { 
	border-top: 1px dotted #666666;  
	padding-top: 5px;
	margin-top: 10px;  
}  
  
</style> 	
{/literal} 

<!-- Loop through each record -->
{foreach from=$ticket item=ticket} 

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<form id="ticket_view" name="ticket_view" method="post" action="">
<div>
<table width="100%"  border="0" cellspacing="0">
  <tr>
    <td align="left" valign="top">	
	<h3>
	{if $ticket.status == "2"}<font color="#999999">{/if} 
		{$ticket.subject} -  
		{if $ticket.status == "0"}
		<font color="#990000">
                {translate module=ticket}
                status_open 
                {/translate}
		</font> {/if} 
		
     	{if $ticket.status == "1"}
		<font color="#CC9900">
                {translate module=ticket}
                status_hold 
                {/translate}
		</font> {/if} 
		
  		{if $ticket.status == "2"}  
                {translate module=ticket}
                status_close 
                {/translate}
		{/if} 
		
   		{if $ticket.status == "3"} 
		<font color="#0099CC">
                {translate module=ticket}
                status_pending
                {/translate}
		</font>{/if}
	</font></h3>
	</td>
    <td align="right" valign="bottom">
	<div id=showoptions {if $VAR.view_options}{style_hide}{/if}>{html_link name="More Options" hide=showoptions  show=main_full,hideoptions action="document.getElementById('view_options').value=1;"}</div>
	<div id=hideoptions {if !$VAR.view_options}{style_hide}{/if}>{html_link name="Hide Options" show=showoptions  hide=main_full,hideoptions action="document.getElementById('view_options').value=0;"}</div>	 	
	{if $ticket.account_id > 0}  
	<div id=showoptionsauth>{html_link name="Show User Authentication" hide=showoptionsauth  show=optionsauth,hideoptionsauth}</div>
	<div id=hideoptionsauth {style_hide}>{html_link name="Hide User Authentication" show=showoptionsauth  hide=optionsauth,hideoptionsauth}</div>		
	{/if}
	</td>
  </tr>
</table>
<br>
</div>

{if $ticket.account_id > 0} 
<div id="optionsauth" {style_hide}>
<div class="DialogHeader"> 
User's Services, Products, and Groups 
</div>
<div class="DialogBody">
<table width="100%"  border="0" cellspacing="0" >
  <tr>
    <th width="33%" style="border-right:1px #ccc solid; border-bottom:1px #ccc solid" scope="col">Services</th>
    <th width="33%" style="border-right:1px #ccc solid; border-bottom:1px #ccc solid" scope="col">Groups</th>
    <th width="33%" style="border-bottom:1px #ccc solid" scope="col">Products</th>
  </tr>
  <tr valign="top">
    <td width="33%" style="border-right:1px #ccc solid">
		{if !$ticket.authsrvc}&nbsp;{/if}
		{foreach from=$ticket.authsrvc item=service} 
		<p>&nbsp;<a href="?_page=service:view&id={$service.id}">{$service.date_orig}</a> &nbsp; 
		<font color="#{if $service.active}006600{else}CC0000{/if}">{$service.sku}</font>
		</p>
		{/foreach} 
	</td>
    <td width="33%" style="border-right:1px #ccc solid">
		{if !$ticket.authgrp}&nbsp;{/if}
		{foreach from=$ticket.authgrp item=group}
		<p>&nbsp;{$group.date_orig} &nbsp; 
		<font color="#{if $group.active}006600{else}CC0000{/if}">{$group.name}</font>
		</p>
		{/foreach} 
	</td>
    <td width="33%">
		{if !$ticket.authsku}&nbsp;{/if}
		{foreach from=$ticket.authsku item=sku}
		<p>&nbsp;<a href="?_page=invoice:view&id={$sku.id}">{$sku.dateorg}</a> &nbsp; 
		{$sku.sku} ({$sku.qty}) </p>
		{/foreach} 
	</td>
  </tr> 
</table>
</div>
</div>
<br>  
{/if}

<div id="main_full" {if !$VAR.view_options}{style_hide}{/if}> 
<div class="DialogHeader"> 
{translate module=ticket}title_view{/translate} {$ticket.id}
</div>
<div class="DialogBody">
<input type="hidden" name="_page" value="{$VAR._page}">
{if $VAR._escape} 
<input type="hidden" name="_escape" value="1">
{/if}
<input type="hidden" name="ticket_id" value="{$ticket.id}">
<input type="hidden" name="do[]" value="ticket:update">
<input type="hidden" name="id" value="{$VAR.id}">
<input type="hidden" name="ticket_date_last" value="">
<input type="hidden" name="view_options" id="view_options" value="1">  
<table width=100%>
    <tr> 
      <td> 
        <table width="100%" bgcolor="#FFFFFF">
          <tr valign="top"> 
            <td width="25%"> <b> 
              {translate module=ticket}
              field_date_orig 
              {/translate}
              </b> </td>
            <td width="50%" align="center"> <b> 
              {translate module=ticket}
              field_date_last 
              {/translate}
              </b> </td>
            <td width="25%" align="right"> <b> 
              {translate module=ticket}
              field_status 
              {/translate}
              </b> </td>
          </tr>
          <tr valign="top"> 
            <td width="25%"> 
              {$list->date_time($ticket.date_orig)}
            </td>
            <td width="50%" align="center"> 
              {$list->date_time($ticket.date_last)}
            </td>
            <td width="25%" align="right"> 
              <select id="ticket_status" name="ticket_status" onChange="document.getElementById('ticket_view').submit()">
                <option value="0" {if $ticket.status == "0"}selected{/if}> 
                {translate module=ticket}
                status_open 
                {/translate}
                </option>
                <option value="1" {if $ticket.status == "1"}selected{/if}> 
                {translate module=ticket}
                status_hold 
                {/translate}
                </option>
                <option value="2" {if $ticket.status == "2"}selected{/if}> 
                {translate module=ticket}
                status_close 
                {/translate}
                </option>
                <option value="3" {if $ticket.status == "3"}selected{/if}> 
                {translate module=ticket}
                status_pending
                {/translate}
                </option>				
              </select>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td> 
        <table width="100%">
          <tr valign="top"> 
            <td width="25%"> <b> 
              {translate module=ticket}
              field_staff_id 
              {/translate}
              </b> </td>
            <td width="50%" align="center"> <b> 
              {translate module=ticket}
              field_department_id 
              {/translate}
              </b> </td>
            <td width="25%" align="right"> <b> 
              {translate module=ticket}
              field_priority 
              {/translate}
              </b> </td>
          </tr>
          <tr valign="top"> 
            <td width="25%"> 
              { $list->menu("no", "ticket_staff_id", "staff", "nickname", $ticket.staff_id, "form_menu\" onChange=\"document.getElementById('ticket_view').submit()") }
            </td>
            <td width="50%" align="center"> 
              { $list->menu("no", "ticket_department_id", "ticket_department", "name", $ticket.department_id, "form_menu\" onChange=\"document.getElementById('ticket_view').submit()") }
            </td>
            <td width="25%" align="right"> 
              <select name="ticket_priority" onChange="document.getElementById('ticket_view').submit()">
                <option value="0" {if $ticket.priority == "0"}selected{/if}> 
                {translate module=ticket}
                priority_standard 
                {/translate}
                </option>
                <option value="1" {if $ticket.priority == "1"}selected{/if}> 
                {translate module=ticket}
                priority_medium 
                {/translate}
                </option>
                <option value="2" {if $ticket.priority == "2"}selected{/if}> 
                {translate module=ticket}
                priority_high 
                {/translate}
                </option>
                <option value="3" {if $ticket.priority == "3"}selected{/if}> 
                {translate module=ticket}
                priority_emergency 
                {/translate}
                </option>
              </select>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td> 
        <table width="100%">
          <tr valign="top"> 
            <td width="25%"> <b> 
              {translate module=ticket}
              field_email 
              {/translate}
              </b> </td>
            <td width="50%" align="center"> <b> 
              {translate module=ticket}
              field_account_id 
              {/translate}
              </b> </td>
            <td width="25%" align="right"><b> 
              {translate module=ticket}
              field_last_reply 
              {/translate}
              </b></td>
          </tr>
          <tr valign="top"> 
            <td width="25%"> 
              <input type="text" name="ticket_email" value="{$ticket.email}" size="22" >
            </td>
            <td width="50%" align="center"> 
              {html_select_account name="ticket_account_id" default=$ticket.account_id}
            </td>
            <td width="25%" align="right">
              <select name="ticket_last_reply" onChange="submit()">
			    <option value="0" {if $ticket.last_reply == ""}selected{/if}></option> 
                <option value="1" {if $ticket.last_reply == "1"}selected{/if}> 
                {translate module=ticket}last_reply_user{/translate}
                </option>
                <option value="2" {if $ticket.last_reply == "2"}selected{/if}> 
                {translate module=ticket}last_reply_staff{/translate}
                </option>  
              </select>
            </td>
          </tr>
        </table>
      </td>
    </tr>
	{if $ticket.static_var != false}
    <tr> 
      <td>  
        <table width="100%">
          {foreach from=$ticket.static_var item=record}
          <tr valign="top" class="row1"> 
            <td width="25%"> <b> 
              {$record.name}
              </b> </td>
            <td width="65%"> 
              {$record.html}
            </td>
          </tr>
          {/foreach} 
        </table> 
      </td>
    </tr>
	{/if} 
  </table>
<div class="DialogAction">
<table width="95%"> 
   <tr valign="middle"> 
      <td width="1%"> 
         <input type="submit" name="Submit32" value="{translate}submit{/translate}" class="form_button"> 
      </td>
      <td width="98%" align="center">
        { $method->exe_noauth("ticket","merge_list") }
      </td>
      <td width="1%" align="right"> 
         <input type="button" name="delete32" value="{translate}delete{/translate}" class="form_button" onClick="{if $VAR._page=='ticket:view_quick'}delete_quick('{$ticket.id}'){else}delete_record('{$ticket.id}','{$VAR.id}'){/if}">
      </td>
  </tr>
</table>
</div> 
</div>
<br> 
</div>
  

<div id="showmsg">
<div class="msgtitleUser" onclick="swapMsgStatus(0);">
<table width="100%"  border="0" cellspacing="0">
  <tr>
    <td align="left" valign="top">
		<div id="msgtitleOn_0" {if !$ticket.reply}{style_hide}{/if}>{$ticket.body|replace:"\r\n":" "|truncate:95}</div>
		<div id="msgtitleOff_0" {if $ticket.reply}{style_hide}{/if}><b>{$ticket.subject}</b></div>	
	</td>
    <td align="right" valign="top">
		<div id="msgoptionsOn_0" class="msgoptions" {if !$ticket.reply}{style_hide}{/if}>{$list->date_time($ticket.date_orig)}</div>
		<div id="msgoptionsOff_0" class="msgoptions" {if $ticket.reply}{style_hide}{/if}>{html_link name=edit hide=showmsg show=editmsg} | {$list->date_time($ticket.date_orig)}</div>	
	</td>
  </tr>
</table> 
</div>  
<div class="msgbodyUser" id="msgbody_0" {if $ticket.reply}{style_hide}{/if}>
<p>{$ticket.body|linkalize|replace:"\r\n":"<br>"}</p> 

{if $ticket.attachments}
<hr style="width:300px;" align="left">
{foreach from=$ticket.attachments item=attach}
<p><b>{$attach.name}</b><br>{$attach.size} <a href="{$URL}?_page=core:blank&_escape=1&do[]=ticket_attachment:download&id={$attach.id}">Download</a></p>
{/foreach}
{/if}

</div> 
</div>

<div id="editmsg" {style_hide}> 
<div class="msgtitleUser">
	<div class="msgoptions"> {html_link name=save action="getElementById('ticket_view').submit();"} {html_link name=cancel hide=editmsg show=showmsg} </div>  
	<div><input type="text" size="60" name="ticket_subject" value="{$ticket.subject|escape:"htmlall"}"></div>
</div>  
<div class="msgbodyUser">
<textarea name="ticket_body" id="ticket_body" cols="70" rows="3" onClick="{literal}$('ticket_body').rows='14'; {/literal}">{$ticket.body|escape:"htmlall"}</textarea> 
</div></form>
</div>  
 
{if $ticket.reply != false}
{foreach from=$ticket.reply item=reply}
<div class="msgtitle{if $reply.staff_id}Staff{else}User{/if}" {if !$reply.last}onclick="swapMsgStatus('{$reply.id}');"{/if}>
<table width="100%"  border="0" cellspacing="0">
  <tr>
    <td align="left" valign="top">
	<div id="msgtitleOn_{$reply.id}" {if $reply.last}{style_hide}{/if}>
		<b>{if $reply.staff_id}{$reply.staff_nickname}{else}{$reply.user_name}{/if}</b>
		{$reply.message|replace:"\r\n":" "|truncate:70}
	</div>
	<div id="msgtitleOff_{$reply.id}" {if !$reply.last}{style_hide}{/if}>
		<b>{if $reply.staff_id}{$reply.staff_nickname}{else}{$reply.user_name}{/if}</b>
	</div>
	</td>
    <td align="right" valign="top">
	<div id="msgoptionsOff_{$reply.id}" class="msgoptions" {if !$reply.last}{style_hide}{/if}>{html_link name=delete action="delete_message('`$reply.id`','`$VAR.id`');"} | {$list->date_time($reply.date_orig)}</div>
	<div id="msgoptionsOn_{$reply.id}" class="msgoptions" {if $reply.last}{style_hide}{/if}>{$list->date_time($reply.date_orig)}</div>
	</td>
  </tr>
</table>  
</div>
  
<div class="msgbody{if $reply.staff_id}Staff{else}User{/if}" id="msgbody_{$reply.id}" {if !$reply.last}{style_hide}{/if}>
	<p>{$reply.message|linkalize|replace:"\r\n":"<br>"} </p> 
</div>
{/foreach}
{/if} 
 

<div class="msgtitleStaff" id="replymsg">
	<div>
	<p><center>{translate module=ticket}user_add_response {/translate}</center></p>
	
	<form name="ticket_update" method="get" action=""> 
	<center>
	
	{if $list->is_installed('faq')}
	<p> 	  		
	<input type="hidden" id="faq_autofill_hidden" name="faq_translate_id" value="" />
	<input type="text" autocomplete="off" id="faq_autofill" name="faq_autofill" size="65" value="" onBlur="{literal}if($('faq_autofill_hidden').value!='' && $('faq_autofill_hidden').value!='null') { $('ticket_reply').value = $('faq_autofill_hidden').value + $('signature').innerHTML; $('ticket_reply').rows='14'; }{/literal}" onClick="this.value=''; $('faq_autofill_hidden').value=''; $('faq_autofill').focus();" style="font-size: 14px; border:2px solid #999; padding:2px;" /> 
	<div class="auto_complete" id="faq_autofiller"></div>  
	<script type="text/javascript">new Ajax.Autocompleter("faq_autofill", "faq_autofiller", "ajax.php?do[]=faq:autofill" )</script>
	</p>
	{/if} 
		
	<div id="signature" {style_hide}>{$signature}</div> 
	<p><textarea id="ticket_reply" name="ticket_reply" cols="65" rows="2" onfocus="{literal}$('ticket_reply').rows='14';{/literal}" onblur="addFaq()" style="font-size: 13px; border:2px solid #999; padding:2px; ">{$signature}</textarea></p> 
	 
	<p> 
	<input type="checkbox" name="enable_user_notice" value="1" checked> {translate module=ticket} enable_user_notice {/translate}
	<input type="checkbox" name="add_faq" id="add_faq" value="1" onClick="addFaq()"> Add Q&A to FAQ Module 
 
	<p>{html_button}</p
	>
	</p>

	  
	</center>  
	  <input type="hidden" name="_page" id="page2" value="{$VAR._page}"> 
	  {if $VAR._escape} 
	   <input type="hidden" name="_escape" value="1">
	  {/if}
	  <input type="hidden" name="ticket_id" value="{$ticket.id}">
	  <input type="hidden" name="do[]" value="ticket:reply">
	  <input type="hidden" name="id" value="{$VAR.id}"> 
	  
	  <input type="hidden" name="faq_name" value="{$ticket.subject}">
	  <input type="hidden" name="faq_question" value="{$ticket.body|escape:"htmlall"}">
	  <input type="hidden" name="faq_answer" id="faq_answer" value="">
	</form> 
	</div>
</div>
<script language="javascript">{if $VAR.do}refreshOpener(){/if}</script>
{/foreach}
{/if}
