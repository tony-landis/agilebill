
<form name="voip" method="post" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
         
		<tr valign="top">
          <td width="65%" class="table_heading">
            <center>Click to Call
              
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">            
          <table width="100%" border="0" cellspacing="4" cellpadding="3" class="row1">

  <tr valign="top">
    <td>Phone: 
        <select name="voip_from">
          <option label="Joe" value="Local/5672562455@longdistance/n">Joes Phone</option>
          <option label="Tony" value="Local/5672450529@longdistance/n">Tonys Phone</option>
        </select>
  </tr>
  <tr valign="top">
    <td>Calling Number/Name: 
        <select name="voip_callerid">
          <option value="5672562455">Thralling Penguin LLC</option>
          <option value="8643359468">AgileCo LLC</option>
        </select>
  </tr>
  <tr valign="top">
    <td>Destination Number: 
        <input type="text" name="voip_to" value="" size="30">
    </td>
  </tr>

  <tr class="row1" valign="middle" align="left">
    <td><div align="center">
        <input type="hidden" name="_page" value="voip:call">
        <input type="hidden" name="do[]" value="voip:place_call">
        <br>
        <br>
        <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
    </div></td>
  </tr>

          </table></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

</form> 
