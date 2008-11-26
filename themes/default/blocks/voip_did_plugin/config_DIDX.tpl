<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="35%">DIDX Username</td>
    <td width="65%"> 
      <input type="text" name="voip_did_plugin_plugin_data[user]" value="{$plugin.user}" class="form_field">
    </td>
  </tr>
  <tr valign="top">
    <td>DIDX Pass </td>
    <td><input type="text" name="voip_did_plugin_plugin_data[pass]" value="{$plugin.pass}" class="form_field"></td>
  </tr>
  <tr valign="top">
    <td>Destination Host (for Provisioning) </td>
    <td><input type="text" name="voip_did_plugin_plugin_data[host]" value="{$plugin.host}" class="form_field"></td>
  </tr>
  <tr valign="top">
    <td>Channel Type</td>
    <td><input type="text" name="voip_did_plugin_plugin_data[type]" value="{$plugin.type}" class="form_field"></td>
  </tr>
  <tr valign="top">
    <td colspan="2"><p><br>
      Please select the countries and area codes to retrieve lists of available. If you do not select both the country and the area code, DIDX will not return any available DIDS.<br>
      <br>
      Formatting example (place each country on a new line): <br>
        1:888,800,864,567<br>
        44:20,121,871,800,1223</p>
      <p>        For USA you can enter '*' to return all area codes, for all other countries you must enter each area code.</p>
      <p align="center">
        <textarea name="voip_did_plugin_plugin_data[country_area]" cols="60" rows="6" class="form_field">{$plugin.country_area}</textarea>
      </p></td>
  </tr>
</table>
 
 <br>
 <br>
