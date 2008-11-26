{ $method->exe("static_page","page_list")} 		{ if ($method->result == FALSE) } { $block->display("core:method_error") } {/if}
{if $static_page_display == true}
{foreach from=$static_page_results item=record}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading_cart"> <a href="{$URL}?_page=static_page:show&name={$record.name}"> 
            {$record.title}
            </a></td>
        </tr>
        <tr> 
          <td bgcolor="#FFFFFF"> 
            <table width="100%" border="0" cellpadding="4">
              <tr> 
                <td class="body" bgcolor="#FFFFFF"> 
                  {$record.intro}
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
{/foreach}
{else}
 
{translate module=static_page}
 no_pages
{/translate}
 
{/if}
