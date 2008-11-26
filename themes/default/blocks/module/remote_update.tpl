<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            
          <td width="65%" class="table_heading"> 
            {translate module=module}
            consistancy_file
            {/translate}
          </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
              <tr valign="top"> 
                <td width="50%"> <b>
                  {translate module=module}
                  modified_files 
                  {/translate}
                  </b> </td>
                <td width="50%"><b> 
                  {translate module=module}
                  missing_files 
                  {/translate}
                  </b></td>
              </tr>
              <tr valign="top"> 
                <td width="50%"> 
                  {if $md5 <= 0}
                  <font color="#006600"> 
                  {translate module=module}
                  no_modified_files 
                  {/translate}
                  </font> 
                  {else}
                  <font color="#990000"> 
                  {translate module=module md5=$md5}
				  md5_files_count
				  {/translate}
                  </font> 
                  {/if}
                </td>
                <td width="50%"> 
                  {if $mis <= 0}
                  <font color="#006600"> 
                  {translate module=module}
                  no_missing_files 
                  {/translate}
                  </font> 
                  {else}
                  <font color="#FF9900"> 
                  {translate mis=$mis module=module}
				  mis_files_count
				  {/translate}
				  </font> 
                  {/if}
                </td>
              </tr>
            </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table> 
  <br>

{foreach from=$modules item=module}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              
            <div align="center"> {$module.name} </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
              <tr valign="top"> 
                <td width="50%"><b> 
                  {translate module=module}
                  modified_files 
                  {/translate}
                  </b></td>
                <td width="50%"><b>
                  {translate module=module}
                  missing_files 
                  {/translate}
                  </b></td>
              </tr>
              <tr valign="top"> 
                <td width="50%"> 
                  {if $module.md5 == ""}
                  <font color="#006600">
                  {translate module=module}
                  no_modified_files 
                  {/translate}
                  </font>
                  {else}
                  <font color="#990000"> 
                  {foreach from=$module.md5 item=md5}
                  {$md5}
                  <br>
                  {/foreach}
				  </font>
				{/if}</td>
                <td width="50%"> 
                  {if $module.mis == ""}
                  <font color="#006600">
                  {translate module=module}
                  no_missing_files 
                  {/translate}
                  </font> 
                  {else}
                  <font color="#FF9900"> 
                  {foreach from=$module.mis item=mis}
                  {$mis}
                  <br> 
                  {/foreach}
				  </font>
                  {/if}
                </td>
              </tr>
            </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table> 
<br>
{/foreach}
