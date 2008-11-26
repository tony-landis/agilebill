{$list->unserial($product.host_provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> Host Type</td>
    <td width="50%"> 
      <select name="product_host_provision_plugin_data[hst_type]" class="form_menu">
        <option value="1" {if $plugin_data.hst_type == "1"} selected{/if}>Name 
        Based</option>
        <option value="0" {if $plugin_data.hst_type == "0"} selected{/if}>IP Based</option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> Disk Quota</td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[quota]" value="{$plugin_data.quota}" class="form_field" size="4">
      MB </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Max POP3/FTP Users</td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[users]" value="{$plugin_data.users}" class="form_field" size="4">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Enable FrontPage</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[enfp]", $plugin_data.enfp, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">PHP</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[enphp]", $plugin_data.enphp, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Shell Access</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[enshell]", $plugin_data.enshell, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">SSI</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[enssi]", $plugin_data.enssi, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">CGI</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[encgi]", $plugin_data.encgi, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">suEXEC</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[ensuexec]", $plugin_data.ensuexec, "form_menu") }
      (cannot be enabled at the same time as Miva)</td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Raw Log Access</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[enraw]", $plugin_data.enraw, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Miva Merchant</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[enmiva]", $plugin_data.enmiva, "form_menu") }
      (cannot be enabled at the same time as suEXEC)</td>
  </tr>
  <tr valign="top"> 
    <td width="50%">SSL</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[enssl]", $plugin_data.enssl, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Spam Filter (Spamassasin)</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[enfilter]", $plugin_data.enfilter, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Limit Bandwidth</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[enthrottle]", $plugin_data.enthrottle, "form_menu") }
    </td>
  </tr>
  <tr valign="top">
    <td width="50%">Limite Bandwidth Settings (If Yes to above)</td>
    <td width="50%"> Limit: 
      <input name="product_host_provision_plugin_data[limit]" size=7 value="{$plugin_data.limit}" class="form_field">
      <select name="product_host_provision_plugin_data[bwunit]" class="form_menu">
        <option value="G" {if $plugin_data.bwunit == "G"} selected{/if}>Gigabyte(s)</option>
        <option value="M" {if $plugin_data.bwunit == "M"} selected{/if}>Megabyte(s)</option>
        <option value="K" {if $plugin_data.bwunit == "K"} selected{/if}>Kilobyte(s)</option>
      </select>
      <br>
      Duration 
      <input name="product_host_provision_plugin_data[duration]" size=7 value="{$plugin_data.duration}" class="form_field">
      <select name="product_host_provision_plugin_data[durationunit]" class="form_menu">
        <option value="w" {if $plugin_data.durationunit == "w"} selected{/if}>Week(s)</option>
        <option value="d" {if $plugin_data.durationunit == "d"} selected{/if}>Day(s)</option>
        <option value="h" {if $plugin_data.durationunit == "h"} selected{/if}>Hour(s)</option>
      </select>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
