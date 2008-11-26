{if $module_saved_js != FALSE}
<form name="search_saved" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="1" class="table_background">
  <tr> 
    <td class="table_heading">
      <center>
          {translate}search_saved_l{/translate} 
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
                <select name="search_id" onChange="fill_search_saved(s_mod,s_fields,s_field_count,s_limit,s_order,s_s,s_c);" >
                  <option value="">...</option>
					{foreach from=$module_saved_menu item=record_s}      
					<option value="{$record_s.id}">{$record_s.name|capitalize}</option>
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
        {$module_saved_js} 
        </center>     
    </td>
  </tr>
</table>
</form>
<p>{/if} </p>
