{$list->unserial($product.host_provision_plugin_data, "plugin_data")}
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%">Server Template Name
    </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[template]" value="{$plugin_data.template}" class="form_field" size="32">
    </td>
  </tr>
  <tr valign="top">
    <td>{translate module=host_provision_plugin}
ipinfo_namebased
  {/translate}</td>
    <td><select name="product_host_provision_plugin_data[ipinfo_namebased]" class="form_menu">
      <option value="1" {if $plugin_data.ipinfo_namebased == "1"} selected{/if}>Name Based</option>
      <option value="0" {if $plugin_data.ipinfo_namebased == "0"} selected{/if}>IP Based</option>
    </select></td>
  </tr>
  <tr valign="top">
    <td>Home directory exists? </td>
    <td>{ $list->bool("product_host_provision_plugin_data[dir]", $plugin_data.dir, "form_menu") }    </td>
  </tr>
  <tr valign="top">
    <td>Unix user exists? </td>
    <td>{ $list->bool("product_host_provision_plugin_data[unix]", $plugin_data.unix, "form_menu") }    </td>
  </tr>
  <tr valign="top">
    <td>DNS domain enabled? </td>
    <td>{ $list->bool("product_host_provision_plugin_data[dns]", $plugin_data.dns, "form_menu") }    </td>
  </tr>
  <tr valign="top">
    <td>Web virtual server enabled? </td>
    <td>{ $list->bool("product_host_provision_plugin_data[web]", $plugin_data.web, "form_menu") }    </td>
  </tr>
  <tr valign="top">
    <td>Webalizer reporting enabled? </td>
    <td>{ $list->bool("product_host_provision_plugin_data[webalizer]", $plugin_data.webalizer, "form_menu") }    </td>
  </tr>
  <tr valign="top">
    <td>Log file rotation enabled? </td>
    <td>{ $list->bool("product_host_provision_plugin_data[logrotate]", $plugin_data.logrotate, "form_menu") }    </td>
  </tr>
  <tr valign="top">
    <td>MySQL database enabled? </td>
    <td>{ $list->bool("product_host_provision_plugin_data[mysql]", $plugin_data.mysql, "form_menu") }    </td>
  </tr>
  <tr valign="top">
    <td>Status monitoring enabled? </td>
    <td>{ $list->bool("product_host_provision_plugin_data[status]", $plugin_data.status, "form_menu") }    </td>
  </tr>
  <tr valign="top">
    <td height="24">Webmin login enabled? </td>
    <td>{ $list->bool("product_host_provision_plugin_data[webmin]", $plugin_data.webmin, "form_menu") }    </td>
  </tr>
</table>
  