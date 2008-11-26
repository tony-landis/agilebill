 
<form name="form1" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0" align="center">
          <tr> 
            <td class="table_heading"> 
              <center>
                {translate module=report}
                menu_view 
                {/translate}
              </center>
            </td>
          </tr>
          <tr> 
            <td class="row1" valign="top"> 
              <table width="100%" border="0" cellpadding="5" class="row1">
                <tr valign="top"> 
                  <td width="33%"> 
                    {translate module=report}
                    module_select 
                    {/translate}
                  </td>
                  <td width="33%"> 
				    {if $VAR.report_module != ""}
                    {translate module=report}
                    report_select 
                    {/translate}
					{/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    {$method->exe_noauth("report","module_menu")}
                  </td>
                  <td width="33%"> 
				    {if $VAR.report_module != ""}
                    {$method->exe_noauth("report","report_menu")}
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
  </form>
<p>
  {if $VAR.report_template != "" && $VAR.report_module != "" }
  {$block->display("report:generate")}
  {/if}
</p>
