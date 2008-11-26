{$method->exe("voip","overview")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}

{popup_init src="includes/overlib/overlib.js"}

{literal}
<style type="text/css">
#v { background-color:#FFFFCC; }
#f { background-color:#DDE6F9; }
#c { background-color:#D7FEBA; }
</style>
{/literal}

<h2>{translate module=voip}in_calls_last{/translate}</h2> 
{if $in}
 <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <form id="form1" name="form1" method="post" action="">
    <tr>
      <td>
        <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2"> 
          <tr valign="middle" align="center" class="table_heading">
            <td width="3%" class="table_heading">{translate module=voip}field_tools{/translate}</td>
            <td width="21%" class="table_heading">{translate module=voip}field_date_orig{/translate}</td>
            <td width="40%" class="table_heading">
              {translate module=voip}field_dst{/translate}</td>
            <td width="26%" class="table_heading">
              {translate module=voip}field_src{/translate}</td>
            <td width="10%" class="table_heading">
              {translate module=voip}field_duration{/translate}</td>
            {foreach from=$in item=record}
          <tr id="{$record.type}" class="row1">
          	  <td width="2%">&nbsp;<a href="javascript:addBlacklist({$record.id});" onmouseover="return overlib('Add this caller to your blacklist');" onmouseout="return nd();" style="background: none;"><img border="0" src="themes/default/images/icons/del_16.gif"></a></td>             
              <td width="19%">&nbsp;{$list->date_time($record.date_orig)}                 
              </td>
	            <td>&nbsp;<a href="#" onmouseover="return overlib('{$record.location|escape:"htmlall"}');" onmouseout="return nd();">{$record.clid}</a></td>
	            <td>&nbsp; {$record.dst}</td>
	            <td>&nbsp; {$record.duration} min</td>
          </tr> 
			{/foreach} 	 
        </table>
      </td>
    </tr>
  </form>
 </table> 
</center>
{else}
<p>{translate module=voip}no_calls{/translate}</p>
{/if}

<h2>{translate module=voip}out_calls_last{/translate}</h2> 
{if $out}
 <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <form id="form1" name="form1" method="post" action="">
    <tr>
      <td>
        <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2"> 
          <tr valign="middle" align="center" class="table_heading">
            <td width="21%" class="table_heading">{translate module=voip}field_date_orig{/translate}</td>
            <td width="40%" class="table_heading">
              {translate module=voip}field_dst{/translate}</td>
            <td width="30%" class="table_heading">
              {translate module=voip}field_src{/translate}</td>
            <td width="10%" class="table_heading">
              {translate module=voip}field_duration{/translate}</td>
            {foreach from=$out item=record}
          <tr id="{$record.type}" class="row1">             
                <td>&nbsp;{$list->date_time($record.date_orig)}  </td>
	            <td>&nbsp; {$record.clid}</td>
	            <td>&nbsp;<a href="#" onmouseover="return overlib('{$record.location|escape:"htmlall"}');" onmouseout="return nd();">{$record.dst}</a></td>
	            <td>&nbsp; {$record.duration} min</td>
          </tr> 
			{/foreach} 	 
        </table>
      </td>
    </tr>
  </form>
 </table> 
</center>
{else}
<p>{translate module=voip}no_calls{/translate}</p>
{/if}
{if $in}
<h2>{translate module=voip}color_guide{/translate}</h2>
 <table id="main1" width="250" border="0" cellspacing="0" cellpadding="0" class="table_background"> 
    <tr>
      <td>
		<table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2"><tr id="{$record.type}" class="row1" >
		  <tr class="row1" >
			<td>{translate module=voip}color_a{/translate}</td>
		  </tr> 		
		    <td id="v">{translate module=voip}color_v{/translate}</td>
		  </tr>
		  <tr class="row1" >
			<td id="f">{translate module=voip}color_f{/translate}</td>
		  </tr>
		  <tr class="row1" >
			<td id="c">{translate module=voip}color_c{/translate}</td>
		  </tr>		  
		</table> 
      </td>
    </tr> 
</table>  
{/if}
{/if} 

{literal}
<script language="javascript">

var http = getHTTPObject();

// update Station list
function addBlacklist(voip_cdr_id) { 
 var url = "ajax.php?do[]=voip_blacklist:ajax_add&voip_cdr_id="+voip_cdr_id;
 http.open("GET", url, true); 
 http.onreadystatechange = function() {
    if (http.readyState == 4) { 
      try { eval(http.responseText) } catch(e) {} 
    }
  } 
  http.send(null);
}

</script>
{/literal}
