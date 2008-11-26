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
    <td width="49%"> 
      {translate module=host_provision_plugin}
      bandwidth_threshold 
      {/translate}
    </td>
    <td width="17%">--- </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[bandwidth_threshold]" value="{$plugin_data.bandwidth_threshold}" class="form_field" size="10">
      bytes</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      ipinfo_namebased 
      {/translate}
    </td>
    <td width="17%">--- </td>
    <td width="34%"> 
      <select name="product_host_provision_plugin_data[ipinfo_namebased]" class="form_menu">
        <option value="1" {if $plugin_data.ipinfo_namebased == "1"} selected{/if}>Name 
        Based</option>
        <option value="0" {if $plugin_data.ipinfo_namebased == "0"} selected{/if}>IP 
        Based</option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      diskquota 
      {/translate}
    </td>
    <td width="17%"> 
      <select name="product_host_provision_plugin_data[diskquota_units]" class="form_menu">
        <option value="B" {if $plugin_data.diskquota_units == "B"} selected{/if}>Bytes</option>
        <option value="KB" {if $plugin_data.diskquota_units == "KB"} selected{/if}>Kilobytes</option>
        <option value="MB" {if $plugin_data.diskquota_units == "MB"} selected{/if}>Megaytes</option>
        <option value="GB" {if $plugin_data.diskquota_units == "GB"} selected{/if}>Gigabytes</option>
      </select>
    </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[diskquota_quota]" value="{$plugin_data.diskquota_quota}" class="form_field" size="10">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      telnet 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[telnet]", $plugin_data.telnet, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      ssh 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[ssh]", $plugin_data.ssh, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      imap 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[imap]", $plugin_data.imap, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      bind 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[bind]", $plugin_data.bind, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      anonftp 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[anonftp]", $plugin_data.anonftp, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      openssl 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[openssl]", $plugin_data.openssl, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      ssi 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[ssi]", $plugin_data.ssi, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      weblogs 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[weblogs]", $plugin_data.weblogs, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      vacation 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[vacation]", $plugin_data.vacation, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      majordomo 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[majordomo]", $plugin_data.majordomo, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      sqmail 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[sqmail]", $plugin_data.sqmail, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      frontpage 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[frontpage]", $plugin_data.frontpage, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      mivamerchant 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[mivamerchant]", $plugin_data.mivamerchant, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      analog 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[analog]", $plugin_data.analog, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      backup 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[backup]", $plugin_data.backup, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      files 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[files]", $plugin_data.files, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
</table>
 
<div align="center"> 
  {translate module=host_provision_plugin}
  notes 
  {/translate}
  <br>
  <textarea name="product_host_provision_plugin_data[notes]" class="form_field" cols="80" rows="5">{$plugin_data.notes}</textarea>
  <br>
</div>
