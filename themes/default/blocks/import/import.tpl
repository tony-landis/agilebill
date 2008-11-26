
{ $method->exe("import","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal} 
    <script language="JavaScript">
 
    </script>
{/literal}
 
 
<form name="import_view" method="post" action="">
{$COOKIE_FORM}
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {$name} 
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="65%">{$instructions}</td>
                </tr>
              </table>
            </td>
          </tr>
        
		<!-- Loop through each record -->
		{foreach from=$import item=import}  

          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="69%">
                    {$import.desc}
                  </td>
                  <td width="31%"> 
                    {if $import.status == 'ready'}
                    <a href="?_page=core:blank&plugin={$VAR.plugin}&action={$import.name}&do[]=import:do_action"> 
                    {translate module=import}
                    {$import.status}
                    {/translate}
                    </a> 
                    {elseif $import.status == 'done'}
                    <a href="?_page=import:import&plugin={$VAR.plugin}&action={$import.name}&do[]=import:undo_action"> 
                    {translate module=import}
                    {$import.status}
                    {/translate}
                    ( 
                    {$import.records}
                    ) </a> 
                    {elseif $import.status == 'pending' }                    
                    {translate module=import}
                    {$import.status}
                    {/translate}
                    {/if}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
		  {/foreach}
		  
          <tr valign="top">
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%">&nbsp; </td>
                  <td width="65%">&nbsp; </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="import:view">
    <input type="hidden" name="import_id" value="{$import.id}">
    <input type="hidden" name="do[]" value="import:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  
{/if}
