{$method->exe("import","search_show")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}
    {if $results == 1}
        {translate results=$results}search_result_count{/translate}
    {else}
        {translate results=$results}search_results_count{/translate}
    {/if}
  <BR> 
  {literal}
    <script language="JavaScript">
    <!-- START
    	var module 		= 'import';		
    	{/literal}
    	{if $VAR._print == TRUE}
    	var p 			= '&_escape=y&_print=y';
    	{else}
    	var p 			= '';
    	{/if}{literal}
    	var IMAGE 		= '{/literal}{$NONSSL_IMAGE}{literal}';
    	var order 		= '{/literal}{$order}{literal}';
    	var sort1  		= '{/literal}{$sort}{literal}';
    	var search_id 	= '{/literal}{$search_id}{literal}';
    	var page 		= {/literal}{$page}{literal};
    	var pages		= '{/literal}{$pages}{literal}';
    	var results		= '{/literal}{$results}{literal}';
    	var limit 		= '{/literal}{$limit}{literal}';
    	record_arr = new Array ({/literal}{$limit}{literal});
    	var i = 0;	
    //  END -->
    </script>
    <SCRIPT SRC="themes/{/literal}{$THEME_NAME}{literal}/search.js"></SCRIPT>
    {/literal}

    <!-- SHOW THE SEARCH NAVIGATION MENU -->
    <center><script language="JavaScript">document.write(search_nav_top());</script></center>

<!-- BEGIN THE RESULTS CONTENT AREA -->
<div id="search_results" onKeyPress="key_handler(event);">
 <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <form id="form1" name="form1" method="post" action="">
    <tr>
        <td> 
          <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
            <!-- DISPLAY THE SEARCH HEADING -->
            <tr valign="middle" align="center" class="table_heading"> 
              <td width="744" class="table_heading"> 
                {translate module=import}
                plugin
                {/translate}
              </td>
              <td width="196" class="table_heading">&nbsp; </td>
              <!-- LOOP THROUGH EACH RECORD -->
              {foreach from=$import item=record}
            <tr class="{$record._C}"> 
              <td width="744">&nbsp; 
                {$record.name|capitalize}
              </td>
              <td width="196"> 
                <div align="center">&nbsp; &nbsp; <a href="?_page=import:import&plugin={$record.name}"> 
                  {translate module=import}
                  view 
                  {/translate}
                  </a> </div>
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