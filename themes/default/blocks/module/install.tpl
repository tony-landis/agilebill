
<form id="module_add" name="module_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=module}
                title_install 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="42%"> 
                    {translate module=module}
                    install_name 
                    {/translate}
                  </td>
                  <td width="58%"> 
                    <input id="module_name" type="text" name="install_name" >
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%">&nbsp; </td>
                  <td width="58%">&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%"> 
                    {translate module=module}
                    install_group 
                    {/translate}
                  </td>
                  <td width="58%"> 
                    {$list->select_groups("", module_group, "form_field", "1", "_1")}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%"></td>
                  <td width="58%">&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%"></td>
                  <td width="58%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="module:install_2">
                    <input type="hidden" name="_page_current" value="module:install">
                    <input type="hidden" name="do[]" value="module:install">
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
