{$method->exe_noauth("report","get_user_criteria")}
 
<!-- Display the form to collect the input values -->
<form name="report_view" method="post" action="" target="_blank">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
      <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                {foreach from=$userCriteria item=field}
                <tr valign="top"> 
                  <td width="36%"> 
                     {$field.display}
                  </td>
                  <td width="54%">
				    
					<!-- date handling -->
				    {if $field.type == 'date'}
						{html_menu_search_expr type=date module=report field=$field.name}
						{$list->calender_add("report[conditions][`$field.name`][value][]", "", "")}
						<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
						<br>
						{html_menu_search_expr type=date module=report field=$field.name} 
						{$list->calender_add("report[conditions][`$field.name`][value][]", "", "")}
						<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
						
					<!-- year handling -->
					{elseif $field.type == 'date_year'}
						{html_menu_search_expr type=dateex module=report field=$field.name}
						<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
						<input name="report[conditions][{$field.name}][value][]" type="text" value="{$field.value}" size="4" maxlength="4"> &nbsp; Example: 2005
						<br>
						{html_menu_search_expr type=dateex module=report field=$field.name} 
						<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
						<input name="report[conditions][{$field.name}][value][]" type="text" value="{$field.value}" size="4" maxlength="4"> &nbsp; Example: 2006	
					
					<!-- year/month handling -->
					{elseif $field.type == 'date_year_month'} 
						{html_menu_search_expr type=dateex module=report field=$field.name}
						<select name="report[conditions][{$field.name}][value][month][]">
						  <option></option>
						  <option value="1">Jan (1)</option>
						  <option value="2">Feb (2)</option>
						  <option value="3">Mar (3)</option>
						  <option value="4">Apr (4)</option>
						  <option value="5">May (5)</option>
						  <option value="6">Jun (6)</option>
						  <option value="7">Jul (7)</option>
						  <option value="8">Aug (8)</option>
						  <option value="9">Oct (9)</option>
						  <option value="10">Sep (10)</option>
						  <option value="11">Nov (11)</option>
						  <option value="12">Dec (12)</option> 
						</select>						
						<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
						<input name="report[conditions][{$field.name}][value][year][]" type="text" value="{$field.value}" size="4" maxlength="4"> &nbsp; Example: 2005
						<br>
						{html_menu_search_expr type=dateex module=report field=$field.name} 
						<select name="report[conditions][{$field.name}][value][month][]">
						  <option></option>
						  <option value="1">Jan (1)</option>
						  <option value="2">Feb (2)</option>
						  <option value="3">Mar (3)</option>
						  <option value="4">Apr (4)</option>
						  <option value="5">May (5)</option>
						  <option value="6">Jun (6)</option>
						  <option value="7">Jul (7)</option>
						  <option value="8">Aug (8)</option>
						  <option value="9">Oct (9)</option>
						  <option value="10">Sep (10)</option>
						  <option value="11">Nov (11)</option>
						  <option value="12">Dec (12)</option> 
						</select>								
						<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
						<input name="report[conditions][{$field.name}][value][year][]" type="text" value="{$field.value}" size="4" maxlength="4"> &nbsp; Example: 2006	
											  
					 <!-- boolean handling -->
                    {elseif $field.type == 'bool'}
					{html_menu_search_expr type=exact module=report field=$field.name}
					<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
                    <select name="report[conditions][{$field.name}][value][]">
                      <option value="" {if $field.value == ""}selected{/if}></option>
                      <option value="1" {if $field.value == "1"}selected{/if}>True</option>
                      <option value="0" {if $field.value == "0"}selected{/if}>False</option>
                    </select>
					
					<!-- text handling -->
                    {elseif $field.type == 'text'}
						{html_menu_search_expr type=text module=report field=$field.name}
						<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
                    	<input type="text" name="report[conditions][{$field.name}][value][]" value="{$field.value}">
					
					<!-- menu handling -->
                    {elseif $field.type == 'menu' }
						{html_menu_search_expr type=exact module=report field=$field.name} 
						<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
						{if $field.values}
						<select name="report[conditions][{$field.name}][value][]">
							<option></option>
						   {html_options options=$field.values }
						</select> 
						{else if $field.table}
							{ $list->menu("no", "report[conditions][`$field.name`][value][]", "`$field.table`", "`$field.col_name`", "all", "") }                  
						{/if}
						                   
					<!-- account autoselector -->
					{elseif $field.type == 'auto_account' }
						<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
						{html_menu_search_expr type=exact module=report field=$field.name}
						{html_select_account name="report[conditions][`$field.name`][value][]"}                  

					<!-- affiliate autoselector -->
					{elseif $field.type == 'auto_affiliate' }
						<input type="hidden" name="report[conditions][{$field.name}][type][]" value="{$field.type}">
						{html_menu_search_expr type=exact module=report field=$field.name}					
						{html_select_affiliate name="report[conditions][`$field.name`][value][]"}                  
					{/if}
					  
					 
                  </td>
                </tr>
                {/foreach}               
				<tr valign="top"> 
                  <td width="36%">Rendering Format:</td>
                  <td width="54%">
                    <div align="left">
                      <select name="report_format">
					    <option value="html">HTML</option>
                        <option value="pdf">PDF</option> 
                        <option value="text">TEXT</option>
                      </select>
                  </div></td>
                </tr>
				<tr valign="top">
				  <td><input type="submit" name="Submit" value="Submit">
				    <input type="hidden" name="_page" value="core:blank">
                    <input type="hidden" name="report_output" value="">
                    <input type="hidden" name="_escape" value="1">
                    <input type="hidden" name="do[]" value="report:view">
                    <input type="hidden" name="report_module" value="{$VAR.report_module}">
                    <input type="hidden" name="report_template" value="{$VAR.report_template}"></td>
				  <td>&nbsp;</td>
			    </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form> 