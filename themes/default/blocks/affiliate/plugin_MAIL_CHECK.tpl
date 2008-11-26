
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=affiliate}
      plugin_mail_check_payee 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="affiliate_plugin_data[payee]" value="{$plugin_data.payee}" >
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=affiliate}
      plugin_mail_check_address 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="affiliate_plugin_data[address]" value="{$plugin_data.address}"  size="40">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=affiliate}
      plugin_mail_check_city 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="affiliate_plugin_data[city]" value="{$plugin_data.city}"  size="20">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=affiliate}
      plugin_mail_check_state 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="affiliate_plugin_data[state]" value="{$plugin_data.state}"  size="20">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=affiliate}
      plugin_mail_check_zip 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="affiliate_plugin_data[zip]" value="{$plugin_data.zip}"  size="20">
    </td>
  </tr>
  <tr valign="top">
    <td width="50%"> 
      {translate module=affiliate}
      plugin_mail_check_email 
      {/translate}
    </td>
    <td width="50%">
      <input type="text" name="affiliate_plugin_data[email]" value="{$plugin_data.email}"  size="20">
    </td>
  </tr>
</table>
