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
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[diskquota]" value="{$plugin_data.diskquota}" class="form_field" size="10">
      MB </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      maxusers 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[maxusers]" value="{$plugin_data.maxusers}" class="form_field" size="10">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      files 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[winfiles]", $plugin_data.winfiles, "form_menu") }
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
      { $list->bool("product_host_provision_plugin_data[winanalog]", $plugin_data.winanalog, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      odbc 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[odbc]", $plugin_data.odbc, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      coldfusion 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[coldfusion]", $plugin_data.coldfusion, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
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
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      perl 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[perl]", $plugin_data.perl, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      php 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[php]", $plugin_data.php, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      mysql 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[mysql]", $plugin_data.mysql, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      urchin 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[urchin]", $plugin_data.urchin, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      msftpsvc 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[msftpsvc]", $plugin_data.msftpsvc, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      cgi 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[cgi]", $plugin_data.cgi, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
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
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      sslc 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[sslc]", $plugin_data.sslc, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      w3svc 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[w3svc]", $plugin_data.w3svc, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      allowanonymous
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[allowanonymous]", $plugin_data.allowanonymous, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      logtype 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[logtype]", $plugin_data.logtype, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      msftpsvc 
      {/translate}
    </td>
    <td width="17%">
      { $list->bool("product_host_provision_plugin_data[msftpsvc]", $plugin_data.msftpsvc, "form_menu") }
    </td>
    <td width="34%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      ftp_maxconnections 
      {/translate}
    </td>
    <td width="17%">---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[ftp_maxconnections]" value="{$plugin_data.ftp_maxconnections}" class="form_field" size="10">
    </td>
  </tr>  
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      ftp_maxconnectionsunlimited 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[ftp_maxconnectionsunlimited]", $plugin_data.ftp_maxconnectionsunlimited, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      logtype 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[logtype]", $plugin_data.logtype, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      enablebandwidthquota 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[enablebandwidthquota]", $plugin_data.enablebandwidthquota, "form_menu") }
    </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[maxbandwidth]" value="{$plugin_data.maxbandwidth}" class="form_field" size="10">
      Kbps </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      cpuquota 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[cpuquota]", $plugin_data.cpuquota, "form_menu") }
    </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[cpuquota]" value="{$plugin_data.cpuquota}" class="form_field" size="10">
      1-100 </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      w3svc_maxconnections 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[w3svc_maxconnections]" value="{$plugin_data.w3svc_maxconnections}" class="form_field" size="10">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      w3svc_maxconnectionsunlimited 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[w3svc_maxconnectionsunlimited]", $plugin_data.w3svc_maxconnectionsunlimited, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      serversize 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <select name="product_host_provision_plugin_data[serversize]" class="form_menu">
        <option value="0" {if $plugin_data.serversize == "0"} selected{/if}>< 
        10,000 daily requests</option>
        <option value="1" {if $plugin_data.serversize == "1"} selected{/if}>10,000 
        - 100,000 daily requests</option>
        <option value="2" {if $plugin_data.serversize == "2"} selected{/if}>>100,000 
        daily requests</option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      winmail 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[winmail]", $plugin_data.winmail, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      userforwards 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[userforwards]", $plugin_data.userforwards, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      autoresponder 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[autoresponder]", $plugin_data.autoresponder, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
</table>
