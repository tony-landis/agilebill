{$list->unserial($host_tld.whois_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_whois_plugin}
      whois_server 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_tld_whois_plugin_data[whois_server]" value="{$plugin_data.whois_server}"  size="40">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_whois_plugin}
      avail_response 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_tld_whois_plugin_data[avail_response]" value="{$plugin_data.avail_response}" >
    </td>
  </tr>
</table>
 
