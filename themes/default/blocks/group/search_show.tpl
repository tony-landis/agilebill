{$method->exe("group","search_show")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}
    {if $results == 1}
        {translate results=$results}search_result_count{/translate}
    {else}
        {translate results=$results}search_results_count{/translate}
    {/if}
	<br><br>
	<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width=100% border="0" cellspacing="1" cellpadding="0" align="center">
        <tr> 
          <td class="table_heading"> 
            <center>
              {translate module=group}
              title_visual 
              {/translate}
            </center>
          </td>
        </tr>
        <tr> 
          <td class="row1"> 
            <table width="100%" border="0" cellpadding="5" class="body">
              <tr> 
                <td> { $method->exe("group","visual_layout")}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table> 
<br><br>
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width=100% border="0" cellspacing="1" cellpadding="0" align="center">
        <tr> 
          <td class="table_heading"> 
            <center>
              {translate module=group}
              menu 
              {/translate}
            </center>
          </td>
        </tr>
        <tr> 
          <td class="row1">
            <table width="100%" border="0" cellpadding="5" class="row1">
              <tr>
                <td>{translate module=group}help_file{/translate}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

{/if} 
