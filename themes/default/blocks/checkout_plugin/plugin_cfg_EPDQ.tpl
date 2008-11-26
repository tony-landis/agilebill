{$list->unserial($checkout.plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      ePDQ administrative service ClientID (also Store ID)</td>
    <td width="50%"><input type="text" name="checkout_plugin_data[clientid]" value="{$plugin_data.clientid}" class="form_field"> 
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> ePDQ administrative service passphrase</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[passphrase]" value="{$plugin_data.passphrase}" class="form_field">
    </td>
  </tr>
  <tr valign="top">
    <td>Charge Type </td>
    <td>
	<select name="checkout_plugin_data[chargetype]" class="form_menu">
      <option value="Auth" {if $plugin_data.chargetype == "Auth"}selected{/if}>Auth (immediate shipment)</option>
      <option value="PreAuth" {if $plugin_data.chargetype == "PreAuth"}selected{/if}>PreAuth (delayed shipment)</option>
    </select>
	</td>  
  </tr>
</table>


 