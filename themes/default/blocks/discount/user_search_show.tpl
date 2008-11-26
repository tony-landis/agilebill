{$method->exe("discount","user_search_show")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}
    {if $results == 1}
        {translate results=$results}search_result_count{/translate}
    {else}
        {translate results=$results}search_results_count{/translate}
    {/if}
  <BR> 
  
<!-- BEGIN THE RESULTS CONTENT AREA -->
<div id="search_results" onKeyPress="key_handler(event);">
 <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <form id="form1" name="form1" method="post" action="">
    <tr>
      <td>
          <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
            <!-- DISPLAY THE SEARCH HEADING -->
            <tr valign="middle" align="center" class="table_heading"> 
              <td width="90" class="table_heading"> 
                {translate module=discount}
                field_status
                {/translate}
              </td>
              <td width="132" class="table_heading"> 
                {translate module=discount}
                field_name
                {/translate}
              </td>
              <td width="284" class="table_heading"> 
                {translate module=discount}
                field_new_status
                {/translate}
              </td>
              <td width="219" class="table_heading"> 
                {translate module=discount}
                field_recurr_status
                {/translate}
              </td>
              <!-- LOOP THROUGH EACH RECORD -->
              {foreach from=$discount item=record}
            <tr id="row{$record.id}" class="{$record._C}"> 
              <td width="90">&nbsp; 
                {if $record.status == "1"}
                {translate}
                true 
                {/translate}
                {else}
                {translate}
                false 
                {/translate}
                {/if}
              </td>
              <td width="132">&nbsp; 
                {$record.name}
              </td>
              <td width="284">&nbsp; 
                {if $record.new_status == "1"}
                {translate}
                true 
                {/translate}
                ( 
                {if $record.new_type == 0}
                {math equation="x * y" x=$record.new_rate y=100}%
                {else} 
                {$list->format_currency($record.new_rate, '')}
                {/if}
                ) 
                {else}
                {translate}
                false 
                {/translate}
                {/if}
              </td>
              <td width="219">&nbsp; 
                {if $record.recurr_status == "1"}
                {translate}
                true 
                {/translate}
                ( 
                {if $record.recurr_type == 0}
                {math equation="x * y" x=$record.recurr_rate y=100}%
                {else}
                {$list->format_currency($record.recurr_rate, '')}
                {/if}
                ) 
                {else}
                {translate}
                false 
                {/translate}
                {/if}
              </td>
            </tr> 
            {/foreach}
            <!-- END OF RESULT LOOP -->
          </table>
      </td>
    </tr>
  </form>
 </table>
  {/if}
</div>
