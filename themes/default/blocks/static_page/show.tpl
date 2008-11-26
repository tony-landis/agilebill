  { $method->exe("static_page","page_show")}
  { if ($method->result == FALSE) }
  { $block->display("core:method_error") }
  {/if}
  {if $static_page_display == true}
  <center>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td><b> <font face="Verdana, Arial, Helvetica, sans-serif" color="#000066" size="3"> 
        </font></b> 
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr> 
            <td width="96%" align="left" valign="middle"> <font face="Verdana, Arial, Helvetica, sans-serif" size="3" color="#000066"> 
              <b> 
              <u>{$static_page_results.title}</u>
              </b></font></td>
          </tr>
        </table>
        <b><font face="Verdana, Arial, Helvetica, sans-serif" color="#000066" size="3"> 
        </font></b></td>
    </tr>
    <tr> 
      <td><font face="Verdana, Arial, Helvetica, sans-serif">
        {$static_page_results.body}
        </font></td>
    </tr>
  </table>
  <b> </b> 
</center>
  <BR><BR>

{else}
{translate module=static_page}page_not_found{/translate}
{/if}
