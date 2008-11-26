{$method->exe("voip","activity")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}

{popup_init src="includes/overlib/overlib.js"}

<select name="wnum" onChange="voipActivityWeekChng(this.value)">
{html_options options=$weeks selected=$wnum}
</select>

{literal}
<script language="javascript">
function voipActivityWeekChng(id) {
document.location='?_page=voip:activity&wnum='+id;
}</script>

<style type="text/css">
#v { background-color:#FFFFCC; }
#f { background-color:#DDE6F9; }
#c { background-color:#D7FEBA; }
</style>

{/literal}



<h2>{translate module=voip}in_calls{/translate}</h2> 
{if $in}
 <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <form id="form1" name="form1" method="post" action="">
    <tr>
      <td>
        <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2"> 
          <tr valign="middle" align="center" class="table_heading">
            <td width="24%" class="table_heading">{translate module=voip}field_date_orig{/translate}</td>
            <td width="42%" class="table_heading">
              {translate module=voip}field_dst{/translate}</td>
            <td width="18%" class="table_heading">
              {translate module=voip}field_src{/translate}</td>
            <td width="16%" class="table_heading">
              {translate module=voip}field_duration{/translate}</td>
            <td width="16%" class="table_heading">{translate module=voip}field_amount{/translate}</td>
            {foreach from=$in item=record}
          <tr id="{$record.type}" class="row1" >             
              <td width="24%">&nbsp;{$list->date_time($record.date_orig)}                 
              </td>
	            <td>&nbsp;<a href="#" onmouseover="return overlib('{$record.location|escape:"htmlall"}');" onmouseout="return nd();">{$record.clid}</a></td>
	            <td>&nbsp; {$record.dst}</td>
	            <td>&nbsp; {$record.duration} min</td>
                <td>{$currency}{$record.amount}</td>
          </tr> 
			{/foreach} 	 
        </table>
      </td>
    </tr>
  </form>
 </table>  
{else}
<p>{translate module=voip}no_calls{/translate}</p>
{/if}

<h2>{translate module=voip}out_calls{/translate}</h2> 
{if $out}
 <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <form id="form1" name="form1" method="post" action="">
    <tr>
      <td>
        <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2"> 
          <tr valign="middle" align="center" class="table_heading">
            <td width="24%" class="table_heading">{translate module=voip}field_date_orig{/translate}</td>
            <td width="42%" class="table_heading">
              {translate module=voip}field_dst{/translate}</td>
            <td width="18%" class="table_heading">
              {translate module=voip}field_src{/translate}</td>
            <td width="16%" class="table_heading">
              {translate module=voip}field_duration{/translate}</td>
            <td width="16%" class="table_heading">{translate module=voip}field_amount{/translate}</td>
            {foreach from=$out item=record}
          <tr id="{$record.type}" class="row1">             
                <td>&nbsp;{$list->date_time($record.date_orig)}  </td>
	            <td>&nbsp; {$record.clid}</td>
	            <td>&nbsp;<a href="#" onmouseover="return overlib('{$record.location|escape:"htmlall"}');" onmouseout="return nd();">{$record.dst}</a></td>
	            <td>&nbsp; {$record.duration} min</td>
                <td>{$currency}{$record.amount}</td>
          </tr> 
			{/foreach} 	 
        </table>
      </td>
    </tr>
  </form>
 </table>  
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
