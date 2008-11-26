{$method->exe("ticket","overview")}
{if ($method->result != FALSE)} 

 <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <form id="form1" name="form1" method="post" action="">
    <tr>
      <td>
          
        <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="1">
          <!-- DISPLAY THE SEARCH HEADING -->
          <tr valign="middle" align="center" class="table_heading"> 
            <td width="250" class="table_heading"> {translate module=ticket}field_department_id{/translate}</td>
            <td width="151" class="table_heading">{translate module=ticket}new{/translate}</td>
            <td width="175" class="table_heading"> {translate module=ticket}awaiting_reply{/translate}</td>
            <td width="152" class="table_heading"> 
              {translate module=ticket}
              awaiting_customer 
              {/translate}
            </td>
            <td width="152" class="table_heading"> 
              {translate module=ticket}
              hold 
              {/translate}
            </td>
            <td width="152" class="table_heading"> 
              {translate module=ticket}
              pending 
              {/translate}
            </td>
            <td width="152" class="table_heading">
              {translate module=ticket}
              resolved 
              {/translate}
            </td>
            <!-- LOOP THROUGH EACH RECORD -->
            {foreach from=$overview item=record}
          <tr id="row{$record.id}" class="{$record.class}"> 
            <td width="250"> &nbsp; 
              {$record.name}
            </td>
            <td width="151"> &nbsp; 
              {if $record.new > 0}
              <a href="javascript:searchTicketsStatus('{$record.id}', 'new')"> 
              {$record.new}
              </a> 
              {else}
              {$record.new}
              {/if}
            </td>
            <td width="175">&nbsp; 
              {if $record.waiting > 0}
              <a href="javascript:searchTicketsStatus('{$record.id}', 'staff')">
              {$record.waiting}
              </a> 
              {else}
              {$record.waiting}
              {/if}
            </td>
            <td width="152">&nbsp; 
              {if $record.customer > 0}
              <a href="javascript:searchTicketsStatus('{$record.id}', 'user')"> 
              {$record.customer}
              </a> 
              {else}
              {$record.customer}
              {/if}
            </td>
            <td width="152">&nbsp; 
              {if $record.hold > 0}
              <a href="javascript:searchTicketsStatus('{$record.id}', 'hold')">
              {$record.hold}
              </a> 
              {else}
              {$record.hold}
              {/if}
            </td>
            <td width="152">&nbsp; 
              {if $record.pending > 0}
              <a href="javascript:searchTicketsStatus('{$record.id}', 'pending')">
              {$record.pending}
              </a> 
              {else}
              {$record.pending}
              {/if}
            </td>
            <td width="152">&nbsp; 
              {if $record.resolved > 0}
              <a href="javascript:searchTicketsStatus('{$record.id}', 'closed')">
              {$record.resolved}
              </a> 
              {else}
              {$record.resolved}
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
<br>  
{/if}
