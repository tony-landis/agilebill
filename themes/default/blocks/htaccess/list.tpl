{ $method->exe("htaccess","list_dirs")} 		
{ if ($method->result == FALSE) } 
{ $block->display("core:method_error") } 
{/if}
{if $htaccess_display == true}
{popup_init src="$SSL_URL/includes/overlib/overlib.js"}
{foreach from=$htaccess_results item=record}       
<table width="90%" border="0" cellpadding="0">
  <tr>  
    <td> 
	<font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
      {assign var="desc" value=$record.description}
	  {if $desc == ''}{assign var="desc" value="No description available"}{/if}
      <a href="{$record.url}" target="_blank" {popup capcolor="ffffff" textcolor="333333" bgcolor="506DC7" fgcolor="FFFFFF" width="170" caption="Description" text="$desc" snapx=1 snapy=1 sticky=1}> 
      <u>{$record.name}
      </u></a> 
	  </font></td>
	</tr>
</table> 
{/foreach}
{else}
<font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
{translate module=htaccess}
none_authorized 
{/translate}
</font> 
{/if}
