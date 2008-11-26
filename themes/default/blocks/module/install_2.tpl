<!-- Display the field validation -->
{if $form_validation}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
        <tr> 
          <td> 
            <table width="100%" border="0" cellspacing="1" cellpadding="0">
              <tr valign="top"> 
                <td width="65%" class="table_heading"> 
                  <div align="center"> 
                    {translate}
                    alert 
                    {/translate}
                  </div>
                </td>
              </tr>
              <tr valign="top"> 
                <td width="65%" class="row1"> <br>
                  {foreach from=$form_validation item=record}
                  &nbsp; 
                  {$record}
                  <br>
                  <br>
                  {/foreach}
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      
    </td>
  </tr>
</table>

{else}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading"> 
            <div align="center"> 
              {translate module=module}
              module_installed 
              {/translate}
            </div>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <div align="center"><br>
              Congratulations, the new module installation has been completed!<br>
              <br>
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
{/if}
