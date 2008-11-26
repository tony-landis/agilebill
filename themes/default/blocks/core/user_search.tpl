<SCRIPT LANGUAGE="JavaScript">
<!-- START
	var sess_expires 	= "{$smarty.const.SESSION_EXPIRE}";
	var cookie_name		= "{$smarty.const.COOKIE_NAME}";
	var SESS	 	= "{$SESS}";
	var URL			= "{$URL}";
	var SSL_URL		= "{$SSL_URL}";
	var THEME_NAME  	= "{$THEME_NAME}";
	var REDIRECT_PAGE 	= "{$smarty.const.REDIRECT_PAGE}";
//  END -->
</SCRIPT>

<!-- Load the main javascript code -->
<SCRIPT SRC="themes/{$THEME_NAME}/top.js"></SCRIPT>
 
</HEAD>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" class="row1">
{ $method->exe($VAR.module,"user_search") }
{ if ($method->result == FALSE) }
{ $block->display("core:method_error") }
{else}
{if $results > 0}
<br>
<br>
<center>
  <br>
  <b>
  {translate}
  search_results 
  {/translate}
  <br>
  <br>
  {translate}
  alert_refresh_click
  {/translate}
  <br>
  <br>
  {if $results == "1" && $VAR._next_page_one != "" }
  	  <!-- ONLY ONE RESULT -->
	  <a href="?_page={$VAR.module}:{$VAR._next_page_one}&id={$record_id}{if $VAR._escape_next == "1" }&_escape=1&_escape_next=1{/if}">
	  {translate}alert_click{/translate}
	  </a>
  	  <SCRIPT LANGUAGE="JavaScript"> 
		  var url   = '?_page={$VAR.module}:{$VAR._next_page_one}&id={$record_id},{if $VAR._escape_next == "1" }&_escape=1&_escape_next=1{/if}';        	
		  document.location = url;
      </SCRIPT>	  	 
  {else}
	  {if $VAR._next_page == ""}
	  <a href="?_page={$VAR.module}:user_search_show&search_id={$search_id}&order_by={$order_by}&limit={$limit}&page=1{if $VAR._escape_next == "1" }&_escape=1&_escape_next=1{/if}">
	  {translate}alert_click{/translate}
	  </a><br>
	  {else}
	  <a href="?_page={$VAR.module}:{$VAR._next_page}&search_id={$search_id}&order_by={$order_by}&limit={$limit}&page=1{if $VAR._escape_next == "1" }&_escape=1&_escape_next=1{/if}">
	  {translate}alert_click{/translate}
	  </a><br>
	  {/if}
  	  <SCRIPT LANGUAGE="JavaScript">
        var module= "{$VAR.module}"; 
	    {if $VAR._next_page == ""}
        var url   = '?_page='+module+':search_show&search_id={$search_id}&order_by={$order_by}&limit={$limit}&page=1{if $VAR._escape_next == "1" }&_escape=1&_escape_next=1{/if}';
		{else}
        var url   = '?_page='+module+':{$VAR._next_page}&search_id={$search_id}&order_by={$order_by}&limit={$limit}&page=1{if $VAR._escape_next == "1" }&_escape=1&_escape_next=1{/if}';
		{/if}        	
		document.location = url;
  	  </SCRIPT>	  	  
  {/if}
  </b>
</center>
<b>
{elseif $VAR._next_page_none != "" && $results == 10 }
  	  <SCRIPT LANGUAGE="JavaScript">
        var module= "{$VAR.module}"; 
		var url   = '?_page='+module+':{$VAR._next_page_none}&{$VAR.name_id1}={$VAR.val_id1}&{$VAR.name_id2}={$VAR.val_id2}';     	
		document.location = url;
  	  </SCRIPT>
{else}
</b>
<center>
  <p>&nbsp;</p>
  <p><h3> 
    {translate}
    search_no_results 
    {/translate}
    </h3> </p>
  <form>
            
    <p>&nbsp;</p>
    <p>
      <input type="button" value="{translate}back{/translate}" onclick="history.back()" class="form_button">
      <input type="button" value="{translate}refresh{/translate}" onclick="location.reload()" class="form_button">
    </p>
  </form>
    </center>
    {/if}
{/if}
