{ $method->exe("file","category_list")} 		
{ if ($method->result == FALSE) } { $block->display("core:method_error") } 
{/if}
{if $file_category_display == true} 
{foreach from=$file_category_results item=record key=key}	
<table width="90%" border="0" cellpadding="0">
  <tr>  
    <td>  
      {assign var="desc" value=$record.description} 
	  <font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
	  <a href="{$URL}?_page=file:list&id={$record.id}"> 
      <u>{$record.name}
      </u></a> 
	  </font></td>
	</tr>
</table> 
{/foreach} 
<br>
{/if}