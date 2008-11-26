{if $module_recent_js != FALSE} 
<form name="search_recent" method="post" action="">

  <table width=100% border="0" cellspacing="0" cellpadding="1" class="table_background">
    <tr> 
      <td class="table_heading"> 
        <center>
          {translate}search_recent{/translate} 
        </center>
      </td>
    </tr>
    <tr> 
      <td> 
        <center>
          <input type="hidden" name="_page" value="module:search_show">
          <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
            <tr class="row1" valign="top"> 
              <td width="65%">
                <select name="search_id"  onChange="fill_search_recent(mod,fields,field_count,limit,order,s,c);">
                  <option value="">...</option>
					{foreach from=$module_recent_menu item=record}      
					<option value="{$record.id}">{$record.date_orig|date_format:"%B %d"} > {$record.sql|replace:"WHERE ":""|replace:"AND":"|"|replace:"%":""|replace:"'":""|truncate:70:"..."}</option>
					{/foreach}
        
                </select>
              </td>
            </tr>
            <tr class="row1" valign="top"> 
              <td width="65%"> 
                <input type="submit" name="Submit" value="{translate}view{/translate}" class="form_button">
              </td>
            </tr>
          </table>
          {$module_recent_js}
		</center>
      </td>
    </tr>
  </table>
</form>
{/if} 
