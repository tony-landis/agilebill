{ $method->exe("faq_category","category_list")} 		{ if ($method->result == FALSE) } { $block->display("core:method_error") } {/if}
{if $faq_category_list_display == true}
<table width="100%" class="body">
  <tr>
    <td style="border-bottom: dotted 1px #888888;padding-bottom: 5px;">
      <a href="?_page=faq_category:faq_category">{translate module=faq}faqs{/translate}</a>
    </td>
  </tr>
  <tr>
    <td style="padding-top: 15px;"><b>{translate module=faq_category}categories{/translate}</b></td>
    </td>
  </tr>
  <tr>
    <td align="right">
      <table width="90%" class="body">
        <tr>
          {foreach name=forcatlist from=$faq_category_list_results item=record}
          <td>
          <a href="?_page=faq:list&id={$record.id}">{$record.name} ({$record.children})</a>
          </td>
          {if ($smarty.foreach.forcatlist.iteration%3 eq 0)}
          </tr>
          <tr>
          {/if}
          {/foreach}
        </tr>
      </table>
    </td>
  </tr>
</table>
{else}
 
{translate module=faq}
 no_pages
{/translate}
 
{/if}
