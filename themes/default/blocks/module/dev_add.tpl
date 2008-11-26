 
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading"> 
            <div align="center">Development Options</div>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" class="body">
              <tr> 
                <td width="27%"> 
                  <form name="form1" method="post" action="">
                    { $list->menu_multi('all', "module", "module", "name", "", "15", "form_menu") }
                    <br>
                    <br>
                    <input type="submit" name="Submit2" value="Generate Install Files" class="form_button">
                    <input type="hidden" name="_page" value="module:dev_add">
                    <input type="hidden" name="do[]" value="module:dev_install_gen">
                  </form>
                </td>
                <td width="27%" valign="top"><a href="?_page=module:translate">Translate 
                  Modules</a></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
<form name="form" method="post" action="">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">Create New Module</div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <div align="center"> 
                <table width="400" border="0" class="body">
                  <tr> 
                    <td width="46%"> 
                      <input type="checkbox" name="m[]" value="add" checked>
                      Add </td>
                    <td width="54%"> 
                      <input type="checkbox" name="m[]" value="search_export">
                      Export (also select all below)</td>
                  </tr>
                  <tr> 
                    <td width="46%"> 
                      <input type="checkbox" name="m[]" value="update" checked>
                      Update</td>
                    <td width="54%"> 
                      <input type="checkbox" name="m[]" value="export_excel">
                      Export Excel </td>
                  </tr>
                  <tr> 
                    <td width="46%"> 
                      <input type="checkbox" name="m[]" value="delete" checked>
                      Delete </td>
                    <td width="54%"> 
                      <input type="checkbox" name="m[]" value="export_xml">
                      Export XML</td>
                  </tr>
                  <tr> 
                    <td width="46%"> 
                      <input type="checkbox" name="m[]" value="view" checked>
                      View </td>
                    <td width="54%"> 
                      <input type="checkbox" name="m[]" value="export_tab">
                      Export TAB </td>
                  </tr>
                  <tr> 
                    <td width="46%"> 
                      <input type="checkbox" name="m[]" value="search" checked>
                      Search</td>
                    <td width="54%"> 
                      <input type="checkbox" name="m[]" value="export_csv">
                      Export CSV</td>
                  </tr>
                </table>
                <p>
                  <input type="text" name="f[]"  value="id">
                  <input type="text" name="f[]"  value="site_id">
                  <input type="text" name="f[]"  value="date_orig">
                  <br>
                  <input type="text" name="f[]"  value="date_last">
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <input type="text" name="f[]" >
                  <br>
                  <br>
                </p>
                </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <p>
    <input type="submit" name="Submit" value="Submit" class="form_button">
  </p>
  <p><br>
    <br>
    <br>
    <input type="hidden" name="_page" value="module:dev_add1">
  </p>
</form>
<p>&nbsp; </p>
