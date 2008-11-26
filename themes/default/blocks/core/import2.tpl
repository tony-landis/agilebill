{ if $list->auth_method_by_name($VAR.module,"import") } 
{if $rows > 0 }
<form name="form1" method="post" action="" enctype="multipart/form-data">
  Ready to import {$rows} rows of data to the {$VAR.module} module:<br>
  <br>
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="3" class="row1">
                {foreach from=$columns item=column}
                <tr valign="top" class="row1"> 
                  <td width="45%">Column 
                    {$column.idx}
                    , Sample Data: <u> 
                    {$column.sample|truncate:15}
                    </u> </td>
                  <td width="55%"> 
                    <select name="import_field[{$column.idx}]" class=form_menu>
                      {html_options options=$fields selected=0}
                    </select>
                  </td>
                </tr>
                {/foreach}
                <tr valign="top"> 
                  <td width="45%">&nbsp;</td>
                  <td width="55%"> 
                    <div align="right"></div>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="45%">Import Type:</td>
                  <td width="55%">
                    <input type="radio" name="import_type" value="db" checked>
                    Update Database 
                    <input type="radio" name="import_type" value="dl">
                    Download SQL commands</td>
                </tr>
                <tr valign="top">
                  <td width="45%">&nbsp;</td>
                  <td width="55%">
                    <input type="submit" name="Submit" 		value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" 		value="core:blank">
                    <input type="hidden" name="do[]" 		value="{$VAR.module}:import">
                    <input type="hidden" name="confirm"		value="1">
                    <input type="hidden" name="file"		value="{$file}">
                    <input type="hidden" name="type"		value="{$VAR.type}">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
<br>
{/if}
{else}
Not authorized! 
{/if}
