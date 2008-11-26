{ $method->exe("static_page_category","category_list")} 		{ if ($method->result == FALSE) } { $block->display("core:method_error") } {/if}
{if $static_page_category_display == true}
<table width="140" border="0" cellspacing="0" cellpadding="0" class="menu_background" align="center">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0" align="center">
        <tr> 
          <td class="menu_1"> 
            <center>
              {translate}
              static_page 
              {/translate}
            </center>
          </td>
        </tr>
        {foreach from=$static_page_category_results item=record key=key}
        <tr> 
          <td class="menu_2"> &nbsp;&nbsp; 
		   <a href="{$URL}?_page=static_page:list&id={$record.id}"> 
		   {$record.name}
           </a>
		  </td>
        </tr>
        {/foreach} 
      </table>
    </td>
  </tr>
</table>
<br>
{/if}
