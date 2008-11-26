<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="35%">Magrathea-Telecom.co.uk Username</td>
    <td width="65%"> 
      <input type="text" name="voip_did_plugin_plugin_data[user]" value="{$plugin.user}" class="form_field">
    </td>
  </tr>
  <tr valign="top">
    <td>Magrathea-Telecom.co.uk Pass </td>
    <td><input type="text" name="voip_did_plugin_plugin_data[pass]" value="{$plugin.pass}" class="form_field"></td>
  </tr>
  <tr valign="top">
    <td>Magrathea-Telecom.co.uk Server Hostname </td>
    <td><input type="text" name="voip_did_plugin_plugin_data[server]" value="{$plugin.server}" class="form_field"></td>
  </tr>
  <tr valign="top">
    <td>Number of DIDs to request </td>
    <td><input type="text" name="voip_did_plugin_plugin_data[poolcount]" value="{$plugin.poolcount}" class="form_field"></td>
  </tr>
  <tr valign="top">
    <td>Destination Host (for Provisioning) </td>
    <td><input type="text" name="voip_did_plugin_plugin_data[host]" value="{$plugin.host}" class="form_field"></td>
  </tr>
  <tr valign="top">
    <td colspan="2"><p><br>
      Please select the countries and area codes to retrieve lists of available.<br>
      <br>
      Formatting example (place each country on a new line): <br>
        44:20,121,871,800,1223</p>
      <p>For USA you can enter '*' to return all area codes, for all other countries you must enter each area code.</p>
      <p align="center">
        <textarea name="voip_did_plugin_plugin_data[country_area]" cols="60" rows="6" class="form_field">{$plugin.country_area}</textarea>
      </p></td>
  </tr>
</table>
 
 <br>
 <br>
