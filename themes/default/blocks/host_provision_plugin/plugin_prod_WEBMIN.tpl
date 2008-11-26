{$list->unserial($product.host_provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top" class="row2"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      service 
      {/translate}
    </td>
    <td width="17%"> 
      {translate module=host_provision_plugin}
      enabled 
      {/translate}
    </td>
    <td width="34%"> 
      {translate module=host_provision_plugin}
      options 
      {/translate}
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%">Network interface </td>
    <td width="17%">&nbsp; </td>
    <td width="34%"> 
      <select name="product_host_provision_plugin_data[network_interface]" class="form_menu">
        <option value="0" {if $plugin_data.network_interface == "0"} selected{/if}>Shared</option>
        <option value="1" {if $plugin_data.network_interface == "1"} selected{/if}>IP 
        Based</option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%">Create home directory?</td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[home_dir]", $plugin_data.home_dir, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%">Set up DNS zone?</td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[dns]", $plugin_data.dns, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%">Set up website for domain?</td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[website]", $plugin_data.website, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%">Create MySQL database?</td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[mysql]", $plugin_data.mysql, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> Create Unix user?</td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[unix]", $plugin_data.unix, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> Set up Webalizer for web logs?</td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[webalizer]", $plugin_data.webalizer, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%">Set up log file rotation?</td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[logrotate]", $plugin_data.logrotate, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%">Create Webmin login?</td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[webmin]", $plugin_data.webmin, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
</table>
 
<div align="center"> <br>
  <br>
</div>
