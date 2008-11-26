{ $method->exe("faq_category","category_list")} 
{if $faq_category_list_display == true}
<table width="140" border="0" cellspacing="0" cellpadding="0" class="menu_background" align="center">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0" align="center">
        <tr> 
          <td class="menu_1"> 
            <center>
		{translate module=faq}
		  faqs
		{/translate}
	    </center>
          </td>
        </tr>
        {foreach from=$faq_category_list_results item=record key=key}
        <tr> 
          <td class="menu_2"> &nbsp;&nbsp; 
		   <a href="?_page=faq:faq&category_id={$record.id}"> 
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
