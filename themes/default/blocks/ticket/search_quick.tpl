{literal}
<style>   
div.results {  padding:1px; border:1px solid #C5DEA1; border-bottom:0px;} 
div.results table { font-size:11px; }
div.results table td { padding:2px; border-bottom:1px solid #C5DEA1; } 
</style> 

<script language="javascript">

var http=getHTTPObject();
var url=false;
var popurl=false;

function viewTicket(id) { 
	window.open('?_page=ticket:view_quick&_escape=1&id='+id, '_blank', 'toolbar=0,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=0,width=700,height=800');
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function refreshTicketSearch() { 
 if(url!=false) { 
  http.open("GET", url, true); 
  http.onreadystatechange = function() {
   if (http.readyState == 4) {  
 	try { document.getElementById('results').innerHTML = http.responseText  } catch(e) {} 
   }
 } 
 http.send(null);
 } 
}

function searchTicketsStatus(department,status) { 
  url = 'index.php?do[]=ticket:search_status&_page=ticket:search_quick_show&_escape=1&department=' + department + '&status=' + status;  
  http.open("GET", url, true); 
  http.onreadystatechange = function() {
   if (http.readyState == 4) {  
 	try { document.getElementById('results').innerHTML = http.responseText  } catch(e) {} 
   }
 } 
 http.send(null); 
} 	

function TicketRowDelete(id) { 
	try { document.getElementById('ticket_id_'+id).style.display='none'; } catch(e) {}
	try { document.getElementById('ticket_id2_'+id).style.display='none'; } catch(e) {}
}

function TicketDelete(id) {
 var urldel = 'index.php?_page=core:search&module=ticket&do[]=ticket:delete&_escape=1&delete_id=' + id;	
 http.open("GET", urldel, true); 
 http.onreadystatechange = function() {  
   TicketRowDelete(id);
 } 
 http.send(null); 
}  
					  
function searchTickets() {     
  var query= document.getElementById('query').value; 
  var query_type= getCheckedValue( document.forms['ticket_quick_search'].elements['query_type'] );
  var department= getCheckedValue( document.forms['ticket_quick_search'].elements['department'] );
  var status = getCheckedValue( document.forms['ticket_quick_search'].elements['status'] );      
  searchTicketsVals(query,query_type,department,status);
} 

function searchTicketsVals(query,query_type,department,status) {   
  url = 'index.php?do[]=ticket:search_quick&_page=ticket:search_quick_show&_escape=1'; 
  url += '&query=' + query; 
  url += '&query_type=' + query_type;
  url += '&department=' + department;
  url += '&status=' + status;      
  http.open("GET", url, true); 
  http.onreadystatechange = function() {
   if (http.readyState == 4) {  
 	try { document.getElementById('results').innerHTML = http.responseText  } catch(e) {} 
   }
 } 
 http.send(null);
} 		 
</script>
{/literal}

<form name="ticket_quick_search" method="post" action="javascript:void(0);" onSubmit="searchTickets()">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=ticket}
                title_search
                {/translate}
			  </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="18%"> 
                    Text/User Query </td>
                  <td width="82%"> 
                    <input id="query" name="query" type="text" value="{$VAR.query}" size="35" {if $ticket_subject == true}class="form_field_error"{/if}> 
					<input name="query_type" type="radio" value="all" checked>
                    All
                    <input name="query_type" type="radio" value="sender"> 
                    Sender Only
                    <input name="query_type" type="radio" value="text"> 
                    Text Only
				</td>
                </tr>
                <tr valign="top"> 
                  <td width="18%"> 
                    {translate module=ticket}field_department_id{/translate}</td>
                  <td width="82%"> 
                    <input name="department" type="radio" value="all" checked>
                    All 
					{foreach from=$overview item=record}
                    <input name="department" type="radio" value="{$record.id}">
                    {$record.name}
					{/foreach}
                    </td>
                </tr>   
                <tr class="row1" valign="top"> 
                  <td width="18%">Status</td>
                  <td width="82%">
				  <input name="status" type="radio" value="all" checked> All  
				  <input name="status" type="radio" value="0"> {translate module=ticket} status_open {/translate}
				  <input name="status" type="radio" value="2"> {translate module=ticket} status_close {/translate}
				  <input name="status" type="radio" value="1"> {translate module=ticket} status_pending {/translate}
				  <input name="status" type="radio" value="3"> {translate module=ticket} status_hold {/translate}
				  </td>
                </tr>
                <tr class="row1" valign="top">
                  <td><input type="button" name="Submit" value="{translate}search{/translate}" onClick="searchTickets()"></td>
                  <td>                    <input type="hidden" name="_page" value="ticket:search_quick_show">
                    <input type="hidden" name="do[]" value="ticket:search_quick"></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
 </form>
 
<div id="results"></div>

{if $VAR.department && $VAR.status && $VAR.query==''}
<script language="javascript">searchTicketsStatus('{$VAR.department}','{$VAR.status}');</script>
{elseif $VAR.department && $VAR.status && $VAR.query}
<script language="javascript">searchTicketsVals('{$VAR.query}','{$VAR.query_type}','{$VAR.department}','{$VAR.status}');</script>
{/if} 