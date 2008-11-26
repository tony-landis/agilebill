{$list->unserial($product.host_provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      quota</td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[quota]" value="{$plugin_data.quota}" class="form_field" size="32">
    </td>
  </tr>
  <tr valign="top">
    <td>maxusers</td>
    <td><input type="text" name="product_host_provision_plugin_data[maxusers]" value="{$plugin_data.maxusers}" class="form_field" size="32"></td>
  </tr>
  <tr valign="top">
    <td>ftp-quota</td>
    <td><input type="text" name="product_host_provision_plugin_data[ftp-quota]" value="{$plugin_data.ftp-quota}" class="form_field" size="32"></td>
  </tr>
  <tr valign="top">
    <td>ftp-maxconn</td>
    <td><input type="text" name="product_host_provision_plugin_data[ftp-maxconn]" value="{$plugin_data.ftp-maxconn}" class="form_field" size="32"></td>
  </tr>
  <tr valign="top">
    <td>enable-shell</td>
    <td> { $list->bool("product_host_provision_plugin_data[enable-shell]", $plugin_data.enable-shell, "form_menu") } </td>
  </tr>
  <tr valign="top">
    <td>enable-apop</td>
    <td>{ $list->bool("product_host_provision_plugin_data[enable-apop]", $plugin_data.enable-apop, "form_menu") } </td>
  </tr>
  <tr valign="top">
    <td>enable-cgi</td>
    <td>{ $list->bool("product_host_provision_plugin_data[enable-cgi]", $plugin_data.enable-cgi, "form_menu") } </td>
  </tr>
  <tr valign="top">
    <td>enable-php</td>
    <td>{ $list->bool("product_host_provision_plugin_data[enable-php]", $plugin_data.enable-php, "form_menu") } </td>
  </tr>
  <tr valign="top">
    <td>enable-ssi</td>
    <td>{ $list->bool("product_host_provision_plugin_data[enable-ssi]", $plugin_data.enable-ssi, "form_menu") } </td>
  </tr>
  <tr valign="top">
    <td>enable-ssl</td>
    <td>{ $list->bool("product_host_provision_plugin_data[enable-ssl]", $plugin_data.enable-ssl, "form_menu") } </td>
  </tr>
  <tr valign="top">
    <td>enable-java </td>
    <td>{ $list->bool("product_host_provision_plugin_data[enable-java]", $plugin_data.enable-java, "form_menu") } </td>
  </tr>
  <tr valign="top">
    <td>enable-ftp</td>
    <td>{ $list->bool("product_host_provision_plugin_data[enable-ftp]", $plugin_data.enable-ftp, "form_menu") } </td>
  </tr>
</table>
  <p><br>
<br>
</p>
  <p><br>
    <br>
    <br>
  </p>
